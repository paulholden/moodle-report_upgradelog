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

declare(strict_types=1);

namespace report_upgradelog\local\systemreports;

use context_system;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\helpers\database;
use report_upgradelog\local\entities\upgrade;

/**
 * Upgrade log system report class implementation
 *
 * @package    report_upgradelog
 * @copyright  2022 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrades extends system_report {

    /**
     * Initialise report
     */
    protected function initialise(): void {
        global $DB;

        // Set our main table entity.
        $upgradeentity = new upgrade();
        $upgradetable = $upgradeentity->get_table_alias('upgrade_log');

        $this->set_main_table('upgrade_log', $upgradetable);
        $this->add_entity($upgradeentity);

        // Restrict to only core install/upgrade logs.
        [$infoselect, $params] = $DB->get_in_or_equal(['Core installed', 'Core upgraded'], SQL_PARAMS_NAMED,
            database::generate_param_name() . '_');

        $paramplugin = database::generate_param_name();
        $select = "{$upgradetable}.plugin = :{$paramplugin} AND {$DB->sql_compare_text("{$upgradetable}.info")} {$infoselect}";
        $this->add_base_condition_sql($select, array_merge($params, [$paramplugin => 'core']));

        // Join the user entity.
        $userentity = new user();
        $usertable = $userentity->get_table_alias('user');
        $this->add_entity($userentity->add_join("LEFT JOIN {user} {$usertable} ON {$usertable}.id = {$upgradetable}.userid"));

        $this->add_columns();
        $this->add_filters();

        $this->set_downloadable(true, get_string('pluginname', 'report_upgradelog'));
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('report/upgradelog:view', context_system::instance());
    }

    /**
     * Add report columns
     */
    protected function add_columns(): void {
        $this->add_columns_from_entities([
            'user:fullnamewithlink',
            'upgrade:information',
            'upgrade:version',
            'upgrade:release',
            'upgrade:timemodified',
        ]);

        // Default sorting.
        $this->set_initial_sort_column('upgrade:timemodified', SORT_DESC);
    }

    /**
     * Add report filters
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'user:fullname',
            'upgrade:timemodified',
        ]);
    }
}
