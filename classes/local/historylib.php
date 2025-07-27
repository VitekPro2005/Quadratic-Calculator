<?php

declare(strict_types=1);

namespace block_quadraticcalc\local;

defined('MOODLE_INTERNAL') || die();

class historylib {
    public static function save(int $userid, float $a, float $b, float $c, ?float $x1, ?float $x2): void {
        global $DB;

        $record = new \stdClass();
        $record->userid = $userid;
        $record->a = $a;
        $record->b = $b;
        $record->c = $c;
        $record->x1 = $x1;
        $record->x2 = $x2;
        $record->timemodified = time();

        $DB->insert_record('block_quadraticcalc_history', $record);
    }

    public static function get_history(int $userid): array {
        global $DB;
        return $DB->get_records('block_quadraticcalc_history', ['userid' => $userid], 'timemodified DESC');
    }
}