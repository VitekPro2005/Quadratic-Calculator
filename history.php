<?php

declare(strict_types=1);

require_once('../../config.php');

use block_quadraticcalc\local\historylib;

require_login();

$context = context_system::instance();
require_capability('block/quadraticcalc:view', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/quadraticcalc/history.php'));
$PAGE->set_title(get_string('history', 'block_quadraticcalc'));
$PAGE->set_heading(get_string('history', 'block_quadraticcalc'));

echo $OUTPUT->header();

$records = historylib::get_history((int)$USER->id);

echo html_writer::start_div('block_quadraticcalc');

if (empty($records)) {
    echo html_writer::div(get_string('nohistory', 'block_quadraticcalc'), 'no-history');
} else {
    $table = new html_table();
    $table->head = ['a', 'b', 'c', 'x₁', 'x₂', 'Time'];

    foreach ($records as $r) {
        $row = [
            $r->a,
            $r->b,
            $r->c,
            $r->x1 ?? '-',
            $r->x2 ?? '-',
            userdate($r->timemodified)
        ];
        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo html_writer::end_div();

echo $OUTPUT->footer();