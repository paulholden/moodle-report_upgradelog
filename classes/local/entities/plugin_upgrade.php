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

namespace report_upgradelog\local\entities;

use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\local\filters\date;

/**
 * Plugin upgrade/install entity.
 *
 * @package    report_upgradelog
 * @copyright  2026 Alex Damsted <alexdamsted@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_upgrade extends base {
    /**
     * Returns default tables used by this entity.
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return ['upgrade_log'];
    }

    /**
     * Default title for this entity.
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('pluginupgrades', 'report_upgradelog');
    }

    /**
     * Initialise entity: add columns and filters.
     *
     * @return self
     */
    public function initialise(): self {
        foreach ($this->get_all_columns() as $column) {
            $this->add_column($column);
        }

        foreach ($this->get_all_filters() as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns all columns for this entity.
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $ul = $this->get_table_alias('upgrade_log');

        return [
            (new column(
                'plugin',
                new lang_string('plugin'),
                $this->get_entity_name()
            ))
                ->add_fields("{$ul}.plugin"),

            (new column(
                'version',
                new lang_string('versionold', 'report_upgradelog'),
                $this->get_entity_name()
            ))
                ->add_fields("{$ul}.version"),

            (new column(
                'targetversion',
                new lang_string('versionnew', 'report_upgradelog'),
                $this->get_entity_name()
            ))
                ->add_fields("{$ul}.targetversion"),

            (new column(
                'timemodified',
                new lang_string('time'),
                $this->get_entity_name()
            ))
                ->add_fields("{$ul}.timemodified")
                ->set_type(column::TYPE_TIMESTAMP)
                ->set_is_sortable(true)
                ->add_callback(function ($timestamp) {
                    return \core_date::strftime(get_string('strftimerecentfull', 'langconfig'), $timestamp);
                }),
        ];
    }

    /**
     * Returns all filters for this entity.
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $ul = $this->get_table_alias('upgrade_log');

        return [
            (new filter(
                date::class,
                'timemodified',
                new lang_string('time'),
                $this->get_entity_name(),
                "{$ul}.timemodified"
            ))
                ->set_limited_operators([
                    date::DATE_ANY,
                    date::DATE_RANGE,
                    date::DATE_PREVIOUS,
                    date::DATE_CURRENT,
                ]),
        ];
    }
}
