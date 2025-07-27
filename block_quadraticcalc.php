<?php

declare(strict_types=1);

use block_quadraticcalc\form\calc_form;
use block_quadraticcalc\local\historylib;

defined('MOODLE_INTERNAL') || die();

class block_quadraticcalc extends block_base {

    public function init(): void {
        $this->title = get_string('pluginname', 'block_quadraticcalc');
    }

    public function get_content() {
        global $OUTPUT, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        $form = new calc_form(null);
        $solution = '';
        $x1 = null;
        $x2 = null;

        if ($data = $form->get_data()) {
            $a = (float) str_replace(',', '.', $data->a);
            $b = (float) str_replace(',', '.', $data->b);
            $c = (float) str_replace(',', '.', $data->c);

            $d = $b * $b - 4 * $a * $c;

            if ($d < 0) {
                $solution = "У этого уравнения нет решений (нет вещественных корней).";
            } else if ($d == 0) {
                $x1 = -$b / (2 * $a);
                $solution = "Уравнение имеет один корень: x = $x1";
            } else {
                $x1 = (-$b + sqrt($d)) / (2 * $a);
                $x2 = (-$b - sqrt($d)) / (2 * $a);
                $solution = "x₁ = $x1, x₂ = $x2";
            }

            historylib::save((int)$USER->id, $a, $b, $c, $x1, ($d > 0 ? $x2 : null));

            $PAGE->requires->js_init_code("
                if (window.location.hash !== '#quadraticcalc') {
                    window.location.hash = 'quadraticcalc';
                }
            ");
        }

        ob_start();
        $form->display();
        $formhtml = ob_get_clean();

        $data = [
            'form' => $formhtml,
            'solution' => $solution
        ];

        $content = $OUTPUT->render_from_template('block_quadraticcalc/content', $data);

        $historyurl = new moodle_url('/blocks/quadraticcalc/history.php');
        $historybutton =html_writer::tag('div',
            html_writer::link(
                $historyurl,
                get_string('viewhistory', 'block_quadraticcalc'),
                ['class' => 'btn-history']
            ),
            ['class' => 'history-button-wrapper']
        );

        $this->content->text = html_writer::tag('div', $content . $historybutton, [
            'class' => 'block_quadraticcalc',
            'id' => 'quadraticcalc'
        ]);

        return $this->content;
    }

    public function applicable_formats(): array {
        return [
            'site-index' => true,
            'course-view' => true,
            'my' => true
        ];
    }

    public function get_required_javascript(): void {
        global $PAGE;
        $PAGE->requires->css('/blocks/quadraticcalc/styles.css');
    }
}