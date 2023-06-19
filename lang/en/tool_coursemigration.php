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
