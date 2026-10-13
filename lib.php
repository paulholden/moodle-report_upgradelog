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
 * Lib file.
 *
 * @package    report_upgradelog
 * @copyright  2026 Alex Damsted <alexdamsted@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns navigation controls (tabtree).
 *
 * @param string $currenttab The current tab
 * @return tabtree
 */
function generate_tabs(string $currenttab): tabtree {
    $tabs = [];

    $tabs[] = new tabobject(
        'view',
        new moodle_url('/report/upgradelog/index.php'),
        get_string('pluginname', 'report_upgradelog')
    );

    $tabs[] = new tabobject(
        'viewall',
        new moodle_url('/report/upgradelog/details.php'),
        get_string('pluginupgrades', 'report_upgradelog')
    );

    return new tabtree($tabs, $currenttab);
}
