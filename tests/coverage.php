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
 * @package    report_upgradelog
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Test coverage class
 *
 * @package    report_upgradelog
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_upgradelog_phpunit_coverage_info extends phpunit_coverage_info {
    /** @var array The list of folders relative to the plugin root to whitelist in coverage generation. */
    protected $whitelistfolders = [
        'classes',
    ];

    /** @var array The list of files relative to the plugin root to whitelist in coverage generation. */
    protected $whitelistfiles = [];

    /** @var array The list of folders relative to the plugin root to excludelist in coverage generation. */
    protected $excludelistfolders = [];

    /** @var array The list of files relative to the plugin root to excludelist in coverage generation. */
    protected $excludelistfiles = [];
};

return new report_upgradelog_phpunit_coverage_info();
