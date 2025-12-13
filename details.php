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
 * Entry point for showing plugin upgrades and installations report.
 *
 * @package    report_upgradelog
 * @copyright  2026 Alex Damsted <alexdamsted@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_reportbuilder\system_report_factory;
use report_upgradelog\local\systemreports\plugin_upgrades;
use core_reportbuilder\local\filters\date;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

// Set up page as an external admin page for reports.
admin_externalpage_setup('reportupgradelog', '', null, '', [
    'pagelayout' => 'report',
]);

// Print header and page title.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginupgrades', 'report_upgradelog'));

// Generate tabs.
if ($tabs = generate_tabs('viewall')) {
    echo $OUTPUT->render($tabs);
}

// Instantiate the report using the system_report_factory.
$report = system_report_factory::create(
    plugin_upgrades::class,
    context_system::instance()
);

// Apply a "Range" filter if start/end timestamps are provided.
$starttime = optional_param('start', null, PARAM_INT);
$endtime   = optional_param('end', null, PARAM_INT);

if (!empty($starttime) || !empty($endtime)) {
    $filtervalues = [
        'plugin_upgrade:timemodified_operator' => date::DATE_RANGE,
        'plugin_upgrade:timemodified_from'     => $starttime,
        'plugin_upgrade:timemodified_to'       => $endtime,
    ];
    $report->set_filter_values($filtervalues);
} else {
    // Unset any set filters.
    $report->set_filter_values([]);
}

// Output the report table.
echo $report->output();

// Print page footer.
echo $OUTPUT->footer();
