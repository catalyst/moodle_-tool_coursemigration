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
 * Upgrade hook.
 *
 * @package    tool_coursemigration
 * @author     Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright  2023 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade hook.
 *
 * @param int $oldversion Old version.
 *
 * @return true
 */
function xmldb_tool_coursemigration_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023063000) {
        // Change field 'error' into a text field.
        $table = new xmldb_table('tool_coursemigration');
        $field = new xmldb_field('error', XMLDB_TYPE_TEXT);

        if ($dbman->table_exists($table) && $dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        }

        // Coursemigration savepoint reached.
        upgrade_plugin_savepoint(true, 2023063000, 'tool', 'coursemigration');
    }

    if ($oldversion < 2023080800) {
        $saveto = get_config('tool_coursemigration', 'saveto');
        set_config('directory', $saveto, 'tool_coursemigration');

        unset_config('saveto', 'tool_coursemigration');
        unset_config('restorefrom', 'tool_coursemigration');

        // Coursemigration savepoint reached.
        upgrade_plugin_savepoint(true, 2023080800, 'tool', 'coursemigration');
    }

    return true;
}
