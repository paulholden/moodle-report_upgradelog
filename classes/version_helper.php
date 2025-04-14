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

namespace report_upgradelog;

use core_text;

/**
 * Helper class for Moodle release name calculation
 *
 * @package    report_upgradelog
 * @copyright  2019 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class version_helper {

    /** @var int Branch date is in the format YYYYMMDD so 8 characters */
    const BRANCH_DATE_LENGTH = 8;

    /** @var array $branchdates See https://docs.moodle.org/dev/Releases */
    protected static $branchdates = [
        20250414 => '5.0',
        20241007 => '4.5',
        20240422 => '4.4',
        20231009 => '4.3',
        20230424 => '4.2',
        20221128 => '4.1',
        20220419 => '4.0',
        20210517 => '3.11',
        20201109 => '3.10',
        20200615 => '3.9',
        20191118 => '3.8',
        20190520 => '3.7',
        20181203 => '3.6',
        20180517 => '3.5',
        20171113 => '3.4',
        20170515 => '3.3',
        20161205 => '3.2',
        20160523 => '3.1',
        20151116 => '3.0',
        20150511 => '2.9',
        20141110 => '2.8',
        20140512 => '2.7',
        20131118 => '2.6',
        20130514 => '2.5',
        20121203 => '2.4',
        20120625 => '2.3',
        20111205 => '2.2',
        20110701 => '2.1',
        20110330 => '2.0',
        20110221 => '2.0.2',
        20101225 => '2.0.1',
        20101124 => '2.0',
    ];

    /**
     * Return formatted version string, accounting for truncated increment value
     *
     * @param string $version
     * @return string
     */
    public static function get_version_string(string $version): string {
        if (floor($version) != $version) {
            return sprintf('%.2f', $version);
        }

        return $version;
    }

    /**
     * Get branch (3.6, 3.7 etc) from version string
     *
     * @param string $version
     * @return string
     */
    protected static function get_branch_name(string $version): string {
        $branchdate = core_text::substr($version, 0, self::BRANCH_DATE_LENGTH);

        return self::$branchdates[$branchdate] ?? '';
    }

    /**
     * Get branch release and increment from version
     *
     * @param string $version
     * @return int[]
     */
    protected static function get_branch_release(string $version): array {
        $suffix = core_text::substr($version, self::BRANCH_DATE_LENGTH);

        // The suffix will look like RR.XX where RR is the release and XX is the optional increment.
        preg_match('/(?<release>\d{2})(?:\.(?<increment>\d{1,2}))?/', $suffix, $matches);

        return [(int)($matches['release']), (int)($matches['increment'] ?? 0)];
    }

    /**
     * Get Moodle release name from version
     *
     * @param string $version
     * @return string
     */
    public static function get_release_name(string $version): string {
        $branchname = self::get_branch_name($version);
        if (empty($branchname)) {
            return get_string('unknown', 'report_upgradelog');
        }

        list($release, $increment) = self::get_branch_release($version);

        // Include release if greater than zero.
        $branchrelease = ($release > 0 ? ".{$release}" : '');

        // If we have an increment value, append plus character.
        $branchrelease .= ($increment > 0 ? '+' : '');

        return "{$branchname}{$branchrelease}";
    }
}
