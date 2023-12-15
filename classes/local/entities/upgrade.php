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
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\filters\{date, number};
use report_upgradelog\version_helper;

/**
 * Upgrade log entity class implementation
 *
 * @package    report_upgradelog
 * @copyright  2022 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade extends base {

    /**
     * Database tables that this entity uses
     *
     * To ensure backwards compatibility, return those defined by {@see get_default_table_aliases}
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return array_keys($this->get_default_table_aliases());
    }

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['upgrade_log' => 'ul'];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('upgrade', 'report_upgradelog');
    }

    /**
     * Initialize the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $upgradetable = $this->get_table_alias('upgrade_log');

        // Information.
        $columns[] = (new column(
            'information',
            new lang_string('info'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$upgradetable}.info")
            ->set_is_sortable(true, [$DB->sql_order_by_text("{$upgradetable}.info")])
            ->add_callback(static function(string $information): string {
                return format_text($information, FORMAT_PLAIN);
            });

        // Moodle version.
        $columns[] = (new column(
            'version',
            new lang_string('moodleversion'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$upgradetable}.version")
            ->set_is_sortable(true)
            ->add_callback([version_helper::class, 'get_version_string']);

        // Moodle release.
        $columns[] = (new column(
            'release',
            new lang_string('moodlerelease'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$upgradetable}.version")
            ->set_is_sortable(false)
            ->add_callback([version_helper::class, 'get_release_name']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('time'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$upgradetable}.timemodified")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $upgradetable = $this->get_table_alias('upgrade_log');

        // Moodle version.
        $filters[] = (new filter(
            number::class,
            'version',
            new lang_string('moodleversion'),
            $this->get_entity_name(),
            "{$upgradetable}.version"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                number::ANY_VALUE,
                number::EQUAL_OR_LESS_THAN,
                number::EQUAL_OR_GREATER_THAN,
            ]);

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('time'),
            $this->get_entity_name(),
            "{$upgradetable}.timemodified"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
                date::DATE_PREVIOUS,
                date::DATE_CURRENT,
            ]);

        return $filters;
    }
}
