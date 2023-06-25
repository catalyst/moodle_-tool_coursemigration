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

/**
 * Settings page to upload courses in csv to restore.
 *
 * @package    tool_coursemigration
 * @author     Glenn Poder <glennpoder@catalyst-au.net>
 * @copyright  2023 Catalyst IT
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_coursemigration;

use csv_import_reader;
use html_writer;
use moodle_exception;
use moodle_url;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');

admin_externalpage_setup('coursemigrationupload', '', null);

// Set up the form.
$uploadcoursesurl = new moodle_url('/admin/tool/coursemigration/uploadcourses.php');
$form = new form\upload_course_list_form($uploadcoursesurl);
$returnurl = new moodle_url('/admin/tool/coursemigration/uploadcourses.php');

if ($form->is_cancelled()) {
    redirect($PAGE->url);
}

$PAGE->set_heading($SITE->fullname);

$PAGE->set_title($SITE->fullname . ': ' . get_string('pluginname', 'tool_coursemigration'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('coursemigrationupload', 'tool_coursemigration'));

// Check if plugin has been configured.
if (!get_config('tool_coursemigration', 'destinationwsurl') || !get_config('tool_coursemigration', 'wstoken')) {
    $settingsurl = new moodle_url('/admin/settings.php', ['section' => 'tool_coursemigration_settings']);
    $link = html_writer::link($settingsurl, get_string('settings_link_text', 'tool_coursemigration'));
    $displayform = false;
    echo $OUTPUT->error_text(get_string('error:pluginnotsetup', 'tool_coursemigration', $link));
    echo $OUTPUT->footer();
    die();
};

if ($data = $form->get_data()) {
    $importid = csv_import_reader::get_new_iid('csvfile');
    $csvimportreader = new csv_import_reader($importid, 'csvfile');
    $content = $form->get_file_content('csvfile');
    $readcount = $csvimportreader->load_csv_content($content, $data->encoding, $data->delimiter_name);

    unset($content);
    if ($readcount === false) {
        throw new moodle_exception('csvfileerror', 'error',
            $returnurl, null, $csvimportreader->get_error());
    } else if ($readcount == 0) {
        throw new moodle_exception('csvemptyfile', 'error',
            $returnurl, null, $csvimportreader->get_error());
    }

    // Process CSV file.
    $messages = uploadcourselist::process_submitted_form($csvimportreader);
    if ($messages) {
        echo $OUTPUT->notification($messages);
        echo $OUTPUT->continue_button($returnurl);
    }

} else {
    $form->display();
}
echo $OUTPUT->footer();


