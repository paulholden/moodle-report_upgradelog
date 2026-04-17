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
use core_reportbuilder\local\helpers\database;
use report_upgradelog\local\entities\plugin_upgrade;

/**
 * Plugin upgrade/install report.
 *
 * @package    report_upgradelog
 * @copyright  2026 Alex Damsted <alexdamsted@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_upgrades extends system_report {
    /**
     * Initialise the report.
     *
     * Add entity, columns, filters and base conditions.
     */
    protected function initialise(): void {
        global $DB;

        // Add entity first (defines tables + aliases).
        $entity = new plugin_upgrade();
        $ul = $entity->get_table_alias('upgrade_log');

        $this->set_main_table('upgrade_log', $ul);
        $this->add_entity($entity);

        // Restrict to plugin upgrades/installs only.
        [$infosql, $params] = $DB->get_in_or_equal(
            ['Starting plugin upgrade', 'Starting plugin installation'],
            SQL_PARAMS_NAMED,
            database::generate_param_name('_')
        );

        $this->add_base_condition_sql(
            "{$ul}.plugin <> 'core'
             AND {$DB->sql_compare_text("{$ul}.info")} {$infosql}",
            $params
        );

        // Add columns and filters from entity.
        $this->add_columns();
        $this->add_filters();

        // Enable downloads.
        $this->set_downloadable(true, get_string('pluginupgrades', 'report_upgradelog'));
    }

    /**
     * Determine if the current user can view this report.
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('report/upgradelog:view', context_system::instance());
    }

    /**
     * Add report columns from the entity.
     */
    protected function add_columns(): void {
        $this->add_columns_from_entities([
            'plugin_upgrade:plugin',
            'plugin_upgrade:version',
            'plugin_upgrade:targetversion',
            'plugin_upgrade:timemodified',
        ]);

        // Default sorting by time modified descending.
        $this->set_initial_sort_column('plugin_upgrade:timemodified', SORT_DESC);
    }

    /**
     * Add report filters from the entity.
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'plugin_upgrade:timemodified',
        ]);
    }
}
