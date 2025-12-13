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
use moodle_url;
use html_writer;

/**
 * Helper class for plugin updates and installation details.
 *
 * @package    report_upgradelog
 * @copyright  2025 Alex Damsted <alexdamsted@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class details_helper {
    /**
     * Build link to plugin upgrade details report.
     *
     * @param string $value Unused
     * @param \stdClass $row Report row
     * @return string
     */
    public static function build_details(string $value, \stdClass $row): string {
        global $DB;

        if (empty($row->timemodified)) {
            return '';
        }

        $start = (int)$row->timemodified;
        $end   = $start + 60;

        // Count plugin installs vs upgrades in the time window.
        $sql = "
        SELECT
            SUM(CASE WHEN version IS NULL OR version = '' THEN 1 ELSE 0 END) AS installed,
            SUM(CASE WHEN version IS NOT NULL AND version <> '' THEN 1 ELSE 0 END) AS updated
        FROM {upgrade_log}
        WHERE plugin <> :core
          AND info IN (:installinfo, :upgradeinfo)
          AND timemodified >= :start
          AND timemodified < :end
    ";

        $params = [
            'core'        => 'core',
            'installinfo' => 'Starting plugin installation',
            'upgradeinfo' => 'Starting plugin upgrade',
            'start'       => $start,
            'end'         => $end,
        ];

        $counts = $DB->get_record_sql($sql, $params);

        $installed = (int)($counts->installed ?? 0);
        $updated   = (int)($counts->updated ?? 0);

        // If nothing happened, don’t show a link.
        if ($installed === 0 && $updated === 0) {
            return '';
        }

        $url = new \moodle_url('/report/upgradelog/details.php', [
            'start' => $start,
            'end'   => $end,
        ]);

        $summary = get_string(
            'upgradepluginsummary',
            'report_upgradelog',
            (object)[
                'installed' => $installed,
                'updated'   => $updated,
            ]
        );

        return format_text($row->info, FORMAT_PLAIN) . ': ' . html_writer::link($url, $summary);
    }
}
