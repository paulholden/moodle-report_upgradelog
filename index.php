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
 * Entry point for showing upgrades report
 *
 * @package    report_upgradelog
 * @copyright  2019 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_reportbuilder\system_report_factory;
use report_upgradelog\local\systemreports\upgrades;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('reportupgradelog', '', null, '', ['pagelayout' => 'report']);

echo /** @var core_renderer $OUTPUT */ $OUTPUT->header();

echo $OUTPUT->heading_with_help(get_string('pluginname', 'report_upgradelog'), 'upgrades', 'report_upgradelog');

$report = system_report_factory::create(upgrades::class, context_system::instance());
echo $report->output();

echo $OUTPUT->footer();
