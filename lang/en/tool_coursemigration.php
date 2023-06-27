<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     tool_coursemigration
 * @category    string
 * @copyright   2023 Catalyst IT
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['coursemigration:restorecourse'] = 'Restore courses';
$string['destinationcategory'] = 'Destination category';
$string['filename'] = 'Filename';
$string['filters'] = 'Filters';
$string['pluginname'] = 'Course migration';
$string['privacy:metadata:tool_coursemigration'] = 'Data relating users for the tool coursemigration plugin';
$string['privacy:metadata:tool_coursemigration:action'] = 'The action type for course migration';
$string['privacy:metadata:tool_coursemigration:courseid'] = 'The source/destination courseid';
$string['privacy:metadata:tool_coursemigration:destinationcategoryid'] = 'The destination categoryid';
$string['privacy:metadata:tool_coursemigration:usermodified'] = 'The ID of the user who modified the record';
$string['settings:backup'] = 'Backup';
$string['settings:destinationwsurl'] = 'Destination URL';
$string['settings:destinationwsurldesc'] = 'Destination URL for web service end point';
$string['settings:wstoken'] = 'Web service token';
$string['settings:wstokendesc'] = 'Authentication token used for accessing the web service end point';
$string['settings:restore'] = 'Restore';
$string['settings:defaultcategory'] = 'Restore root category';
$string['settings:defaultcategorydesc'] = 'Default/root category for restoring courses.';
$string['settings:hiddencourse'] = 'Restore as a hidden course';
$string['settings:hiddencoursedesc'] = 'If enabled, the course visibility will be hidden.';
$string['settings:successfuldelete'] = 'Delete successfully restored backups';
$string['settings:successfuldeletedesc'] = 'If enabled, the backup will be deleted after a successful restore.';
$string['settings:faildelete'] = 'Delete failed backups';
$string['settings:faildeletedesc'] = 'If enabled, the backup will be deleted after a failed restore.';
$string['settings:storage'] = 'Storage';
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvdelimiter_help'] = 'CSV delimiter of the CSV file.';
$string['encoding'] = 'Encoding';
$string['encoding_help'] = 'Select the character encoding used for the data. (The standard encoding is UTF-8.) If the wrong encoding is selected by mistake, it will be noticeable when previewing the data for import.';
$string['coursemigrationupload'] = 'Upload course list';
$string['pluginname_help'] = 'Upload a list of courses as a CSV file that will be migrated to the remote instance as defined in the plugin settings. An example CSV file can be downloaded below.';
$string['csvfile'] = 'CSV file';
$string['settings_link_text'] = 'Admin tools -> Course migration';
$string['missing_column'] = 'CSV file must include one of {$a->columnlist} as column headings';
$string['error:nonintegervalue'] = 'Non integer value for {$a->csvcolumn} found on row {$a->rownumber}';
$string['error:pluginnotsetup'] = 'The course migration plugin is not setup: [Destination URL] and [Web service token] need to be configured in {$a}';
$string['error:createbackuptask'] = 'Error in creating backup task id: {a$->coursemigrationid} error message: {$a->errormessage}';
$string['error:backupalreadyrunning'] = 'Backup adhoc task id: {a$->coursemigrationid} for course: {$a->courseid} is already running.';
$string['successfullycreatebackuptask'] = 'Successfully created backup task id: {a$->coursemigrationid}';
$string['examplecsv'] = 'Example text file';
$string['examplecsv_help'] = 'To use the example text file, download it then open it with a text or spreadsheet editor. Leave the first line unchanged, then edit the following lines (records) and add your course ids and category ids. Save the file as CSV then upload it.';

$string['csvfile_help'] = 'The format of the CSV file is as follows:

* Each line of the file contains one record.
* Each record is a series of data in any order separated by commas or other standard delimiters.
* CSV fields that needs to be supported: id (course id), url (url to a course so we could get course id from url), destination category id.
* One of id or url should  AND one of category id should be in the file to pass validation.';

$string['returnmessages'] = 'File successfully processed.<br\><br\>
Total rows: {$a->rowcount}<br\>
Success: {$a->success}<br\>
Failed: {$a->failed}<br\>
Errors in CSV file: {$a->errorcount}<br\><br\>
{$a->errormessages}';
$string['status:notstarted'] = 'Not started';
$string['status:inprogress'] = 'In progress';
$string['status:completed'] = 'Completed';
$string['status:failed'] = 'Failed';
$string['status:invalid'] = 'Invalid';
$string['task:createrestoretasks'] = 'Create restore adhoc tasks for course migration';
$string['timecreated'] = 'Time created';
$string['timemodified'] = 'Time modified';
$string['event:file_uploaded'] = 'File uploaded';
$string['event:file_processed'] = 'File processed';
$string['event:restore_completed'] = 'Restore completed';
$string['event:restore_failed'] = 'Restore failed';
$string['checkprogress'] = 'Check progress';
$string['create_ad-hoc_backup_tasks'] = 'Course migration - create ad-hoc backup tasks';
