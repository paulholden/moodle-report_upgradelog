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

namespace report_upgradelog\output;

use renderable;
use stdClass;
use table_sql;
use report_upgradelog\version_helper;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Report table
 *
 * @package    report_upgradelog
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table extends table_sql implements renderable {

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('report-upgradelog-report-table');

        // Define columns.
        $columns = [
            'fullname' => null,
            'info' => get_string('info'),
            'moodleversion' => get_string('moodleversion'),
            'moodlerelease' => get_string('moodlerelease'),
            'timemodified' => get_string('time'),
        ];

        $this->define_columns(array_keys($columns));
        $this->define_headers(array_values($columns));

        // Table configuration.
        $this->set_attribute('cellspacing', '0');

        $this->sortable(true, 'timemodified', SORT_DESC);
        $this->no_sorting('moodlerelease');

        $this->initialbars(false);
        $this->collapsible(false);

        $this->useridfield = 'userid';

        // Initialize table SQL properties.
        $this->init_sql();
    }

    /**
     * Initializes table SQL properties
     *
     * @return void
     */
    protected function init_sql() : void {
        global $DB;

        $fields = 'ul.timemodified, ul.userid, ul.info, ul.version AS moodleversion, 0 AS moodlerelease, ' .
            get_all_user_name_fields(true, 'u');
        $from = '{upgrade_log} ul LEFT JOIN {user} u ON u.id = ul.userid';

        list($infowhere, $params) = $DB->get_in_or_equal(['Core installed', 'Core upgraded'], SQL_PARAMS_NAMED);
        $where = "ul.plugin = :plugin AND {$DB->sql_compare_text('ul.info')} {$infowhere}";
        $params['plugin'] = 'core';

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM {$from} WHERE {$where}", $params);
    }

    /**
     * Format information column
     *
     * @param stdClass $row
     * @return string
     */
    public function col_info(stdClass $row) : string {
        return $this->format_text($row->info, FORMAT_PLAIN);
    }

    /**
     * Format Moodle release column
     *
     * @param stdClass $row
     * @return string
     */
    public function col_moodlerelease(stdClass $row) : string {
        return version_helper::get_release_name($row->moodleversion);
    }

    /**
     * Format time modified column
     *
     * @param stdClass $row
     * @return string
     */
    public function col_timemodified(stdClass $row) : string {
        return userdate($row->timemodified);
    }
}