<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_coursemigration\task;

use advanced_testcase;
use backup;
use backup_controller;
use context_course;
use core\task\manager;
use Exception;
use invalid_parameter_exception;
use moodle_exception;
use tool_coursemigration\coursemigration;
use tool_coursemigration\event\restore_completed;
use tool_coursemigration\event\restore_failed;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Course restore tests.
 *
 * @package    tool_coursemigration
 * @author     Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright  2023 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \tool_coursemigration\task\course_restore
 */
class course_restore_test extends advanced_testcase {

    /**
     * Test restore.
     */
    public function test_restore() {
        global $CFG, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $eventsink = $this->redirectEvents();

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['fullname' => 'Test restore course']);
        $category = $generator->create_category();

        // Backup the course.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);
        $bc->finish_ui();
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Get the backup file.
        $coursecontext = context_course::instance($course->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($coursecontext->id, 'backup', 'course', false, 'id ASC');
        /** @var \stored_file $backupfile */
        $backupfile = reset($files);
        $filename = $backupfile->get_filename();
        $backuppath = $CFG->tempdir . DIRECTORY_SEPARATOR;
        $backupfile->copy_content_to($backuppath . $filename);

        // Create coursemigration record.
        $coursemigration = new coursemigration(0, (object)[
            'action' => coursemigration::ACTION_RESTORE,
            'destinationcategoryid' => $category->id,
            'status' => coursemigration::STATUS_NOT_STARTED,
            'filename' => $filename,
        ]);

        set_config('restorefrom', $backuppath, 'tool_coursemigration');
        set_config('saveto', $backuppath, 'tool_coursemigration');

        $coursemigration->save();

        $task = new course_restore();
        $customdata = ['coursemigrationid' => $coursemigration->get('id')];
        $task->set_custom_data($customdata);
        manager::queue_adhoc_task($task);
        $task->execute();

        // Confirm the status is now completed.
        $currentcoursemigration = coursemigration::get_record(['id' => $coursemigration->get('id')]);
        $this->assertEquals(coursemigration::STATUS_COMPLETED, $currentcoursemigration->get('status'));

        // Confirm the course is restored.
        $newcourse = get_course($currentcoursemigration->get('courseid'));
        $this->assertNotEquals($course->id, $newcourse->id);
        $this->assertEquals($category->id, $newcourse->category);
        $this->assertStringContainsString('Test restore course', $newcourse->fullname);

        $eventclass = restore_completed::class;
        $events = array_filter($eventsink->get_events(), function ($event) use ($eventclass) {
            return $event instanceof $eventclass;
        });
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals($newcourse->id, $event->other['courseid']);
        $this->assertEquals($newcourse->fullname, $event->other['coursename']);
        $this->assertEquals($category->id, $event->other['destinationcategoryid']);
        $this->assertEquals($category->name, $event->other['destinationcategoryname']);
        $expectdescription = "Restoring course '{$newcourse->fullname}' (id: {$newcourse->id})" .
            " is successfully completed into category '{$category->name}' (id: {$category->id}).";
        $this->assertEquals($expectdescription, $event->get_description());
        $this->assertEquals(get_string('event:restore_completed', 'tool_coursemigration'), $event->get_name());
    }

    /**
     * Test restore without param.
     *
     * @covers ::restore
     */
    public function test_restore_invalid_param() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $eventsink = $this->redirectEvents();

        $task = new course_restore();
        manager::queue_adhoc_task($task);

        try {
            $task->execute();
        } catch (Exception $e) {
            $exceptionclassname = invalid_parameter_exception::class;
            $this->assertTrue($e instanceof $exceptionclassname);
            $this->assertStringContainsString('Invalid data. Error: missing one of the required parameters.', $e->getMessage());
        }

        $eventclass = restore_failed::class;
        $events = array_filter($eventsink->get_events(), function ($event) use ($eventclass) {
            return $event instanceof $eventclass;
        });
        $this->assertCount(1, $events);
        $event = reset($events);
        $expectdescription = "Restoring course is failed. Error: Invalid data. Error: missing one of the required parameters.";
        $this->assertEquals($expectdescription, $event->get_description());
        $this->assertEquals(get_string('event:restore_failed', 'tool_coursemigration'), $event->get_name());
    }

    /**
     * Test restore with invalid coursemigrationid.
     *
     * @covers ::restore
     */
    public function test_restore_invalid_coursemigrationid() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $eventsink = $this->redirectEvents();

        $task = new course_restore();
        $customdata = ['coursemigrationid' => 12345];
        $task->set_custom_data($customdata);
        manager::queue_adhoc_task($task);

        try {
            $task->execute();
        } catch (Exception $e) {
            $exceptionclassname = invalid_parameter_exception::class;
            $this->assertTrue($e instanceof $exceptionclassname);
            $this->assertStringContainsString('Invalid id. Error: could not find record for restore.', $e->getMessage());
        }

        $eventclass = restore_failed::class;
        $events = array_filter($eventsink->get_events(), function ($event) use ($eventclass) {
            return $event instanceof $eventclass;
        });
        $this->assertCount(1, $events);
        $event = reset($events);
        $expectdescription = "Restoring course is failed. Error: Invalid id. Error: could not find record for restore.";
        $this->assertEquals($expectdescription, $event->get_description());
        $this->assertEquals(get_string('event:restore_failed', 'tool_coursemigration'), $event->get_name());
    }

    /**
     * Test restore with invalid filename.
     *
     * @covers ::restore
     */
    public function test_restore_invalid_filename() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $eventsink = $this->redirectEvents();

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();

        // Create coursemigration record.
        $coursemigration = new coursemigration(0, (object)[
            'action' => coursemigration::ACTION_RESTORE,
            'destinationcategoryid' => $category->id,
            'status' => coursemigration::STATUS_NOT_STARTED,
            'filename' => 'invalid file name',
        ]);
        $coursemigration->save();

        $backuppath = $CFG->tempdir . DIRECTORY_SEPARATOR;
        set_config('restorefrom', $backuppath, 'tool_coursemigration');
        set_config('saveto', $backuppath, 'tool_coursemigration');

        $task = new course_restore();
        $customdata = ['coursemigrationid' => $coursemigration->get('id')];
        $task->set_custom_data($customdata);
        manager::queue_adhoc_task($task);
        $task->execute();

        // Check exception was thrown.
        $currentcoursemigration = coursemigration::get_record(['id' => $coursemigration->get('id')]);
        $expected = 'Cannot restore the course. File can not be pulled from the storage. Error: Cannot read file. ' .
            'Either the file does not exist or there is a permission problem. (/var/lib/phpunitdata/temp/invalid file name)';
        $this->assertEquals($expected, $currentcoursemigration->get('error'));

        $eventclass = restore_failed::class;
        $events = array_filter($eventsink->get_events(), function ($event) use ($eventclass) {
            return $event instanceof $eventclass;
        });
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertStringContainsString("file does not exist", $event->get_description());
        $this->assertEquals(get_string('event:restore_failed', 'tool_coursemigration'), $event->get_name());
    }

    /**
     * Test restore with not configured storage.
     *
     * @covers ::restore
     */
    public function test_restore_not_configured_storage() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $eventsink = $this->redirectEvents();

        $category = $this->getDataGenerator()->create_category();

        // Create coursemigration record.
        $coursemigration = new coursemigration(0, (object)[
            'action' => coursemigration::ACTION_RESTORE,
            'destinationcategoryid' => $category->id,
            'status' => coursemigration::STATUS_NOT_STARTED,
            'filename' => 'testfilename',
        ]);
        $coursemigration->save();

        // Break config for a storage.
        set_config('storagetype', '', 'tool_coursemigration');

        $task = new course_restore();
        $customdata = ['coursemigrationid' => $coursemigration->get('id')];
        $task->set_custom_data($customdata);
        manager::queue_adhoc_task($task);
        $task->execute();

        // Check exception was thrown.
        $currentcoursemigration = coursemigration::get_record(['id' => $coursemigration->get('id')]);
        $expected = 'Cannot restore the course. A storage class has not been configured';
        $this->assertEquals($expected, $currentcoursemigration->get('error'));

        $eventclass = restore_failed::class;
        $events = array_filter($eventsink->get_events(), function ($event) use ($eventclass) {
            return $event instanceof $eventclass;
        });
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertStringContainsString("A storage class has not been configured", $event->get_description());
    }
}
