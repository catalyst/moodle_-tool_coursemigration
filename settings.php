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
 * Plugin administration pages are defined here.
 *
 * @package     tool_coursemigration
 * @category    admin
 * @copyright   2023 Catalyst IT
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_coursemigration\local\storage\backup_directory;
use tool_coursemigration\local\storage\storage_setting_configselect;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    $settings = new admin_settingpage('tool_coursemigration_settings', new lang_string('generalsettings', 'admin'));

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_heading('tool_coursemigration/backup',
            new lang_string('settings:backup', 'tool_coursemigration'), ''));

        $settings->add(new admin_setting_configtext('tool_coursemigration/destinationwsurl',
            get_string('settings:destinationwsurl', 'tool_coursemigration'),
            get_string('settings:destinationwsurldesc', 'tool_coursemigration'),
            '', PARAM_URL));

        $settings->add(new admin_setting_configpasswordunmask('tool_coursemigration/wstoken',
            get_string('settings:wstoken', 'tool_coursemigration'),
            get_string('settings:wstokendesc', 'tool_coursemigration'),
            ''));

        $settings->add(new admin_setting_configcheckbox('tool_coursemigration/failbackupdelete',
            get_string('settings:failbackupdelete', 'tool_coursemigration'),
            get_string('settings:failbackupdeletedesc', 'tool_coursemigration'),
            1));

        $settings->add(new admin_setting_heading('tool_coursemigration/restore',
            new lang_string('settings:restore', 'tool_coursemigration'), ''));

        $settings->add(new admin_settings_coursecat_select('tool_coursemigration/defaultcategory',
            get_string('settings:defaultcategory', 'tool_coursemigration'),
            get_string('settings:defaultcategorydesc', 'tool_coursemigration'), 1));

        $settings->add(new admin_setting_configcheckbox('tool_coursemigration/hiddencourse',
            get_string('settings:hiddencourse', 'tool_coursemigration'),
            get_string('settings:hiddencoursedesc', 'tool_coursemigration'),
            0));

        $settings->add(new admin_setting_configcheckbox('tool_coursemigration/successfuldelete',
            get_string('settings:successfuldelete', 'tool_coursemigration'),
            get_string('settings:successfuldeletedesc', 'tool_coursemigration'),
            0));

        $settings->add(new admin_setting_configcheckbox('tool_coursemigration/failrestoredelete',
            get_string('settings:failrestoredelete', 'tool_coursemigration'),
            get_string('settings:failrestoredeletedesc', 'tool_coursemigration'),
            0));

        $settings->add(new admin_setting_configtext('tool_coursemigration/restoremaxretires',
            get_string('settings:restoremaxretires', 'tool_coursemigration'),
            get_string('settings:restoremaxretiresdesc', 'tool_coursemigration'),
            2, PARAM_INT));

        $settings->add(new admin_setting_heading('tool_coursemigration/storage',
            new lang_string('settings:storage', 'tool_coursemigration'), ''));

        $settings->add(new storage_setting_configselect());
        $settings->add(new backup_directory('directory'));
    }

    $ADMIN->add('tools', new admin_category('coursemigration', get_string('pluginname', 'tool_coursemigration')));
    $ADMIN->add('coursemigration', $settings);

    // Section for uploading CSV.
    $ADMIN->add('coursemigration', new admin_externalpage('coursemigrationupload',
            get_string('coursemigrationupload', 'tool_coursemigration'),
            new moodle_url('/admin/tool/coursemigration/uploadcourses.php'))
    );

    $ADMIN->add('coursemigration',
        new admin_externalpage('tool_coursemigration_reports',
            new lang_string('reports'),
            new moodle_url('/admin/tool/coursemigration/reports.php'))
    );

}
