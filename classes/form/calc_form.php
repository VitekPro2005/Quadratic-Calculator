<?php

declare(strict_types=1);

namespace block_quadraticcalc\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class calc_form extends moodleform {
    public function definition(): void {
        $mform = $this->_form;

        foreach (['a', 'b', 'c'] as $field) {
            $mform->addElement('text', $field, get_string("number$field", 'block_quadraticcalc'), 
                ['placeholder' => get_string("placeholder$field", 'block_quadraticcalc')]);
            $mform->setType($field, PARAM_RAW);
            $mform->addRule($field, get_string('required'), 'required', null, 'client');
        }

        $this->add_action_buttons(false, get_string('solve', 'block_quadraticcalc'));
    }

    public function validation($data, $files): array {
        $errors = [];

        foreach (['a', 'b', 'c'] as $field) {
            $value = str_replace(',', '.', trim($data[$field]));
            if (!is_numeric($value)) {
                $errors[$field] = get_string('mustbenumber', 'block_quadraticcalc');
            } else if ($field === 'a' && (float)$value == 0.0) {
                $errors[$field] = get_string('acantbezero', 'block_quadraticcalc');
            }
        }

        return $errors;
    }
}