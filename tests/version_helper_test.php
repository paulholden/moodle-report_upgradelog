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

defined('MOODLE_INTERNAL') || die();

use report_upgradelog\version_helper;

/**
 * Testcase for version_helper class
 *
 * @package     report_upgradelog
 * @group       report_upgradelog
 * @covers      \report_upgradelog\version_helper
 * @copyright   2019 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_upgradelog_version_helper_testcase extends advanced_testcase {

    /**
     * Data provider for test_get_release_name
     *
     * @return array
     */
    public function get_release_name_provider() : array {
        return [
            ['2019052002.04', '3.7.2+'],
            ['2019052001.1', '3.7.1+'],
            ['2019052001', '3.7.1'],
            ['2019052000.01', '3.7+'],
            ['2019052000', '3.7'],
            // There were some odd branching dates in the early days.
            ['2011033010', '2.0.10'],
            ['2011033003', '2.0.3'],
            ['2011022100', '2.0.2'],
            ['2010122500', '2.0.1'],
            ['2010112400', '2.0'],
            // Unknown (3.8dev).
            ['2019092000', 'Unknown']
        ];
    }

    /**
     * Test class get_release_name method
     *
     * @param string $version
     * @param string $expected
     * @return void
     *
     * @dataProvider get_release_name_provider
     */
    public function test_get_release_name(string $version, string $expected) {
        $this->assertEquals($expected, version_helper::get_release_name($version));
    }
}