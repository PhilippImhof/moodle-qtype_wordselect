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
 * wordselect question renderer class.
 *
 * @package    qtype
 * @subpackage wordselect
 * @copyright  Marcus Green 2016

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for wordselect questions.
 *
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;
        $question = $qa->get_question();
        $PAGE->requires->js('/question/type/wordselect/selection.js');
        $response = $qa->get_last_qt_data();
        $correctplaces = $question->get_correct_places($question->questiontext, $question->delimitchars);
        $output = $question->introduction;

        foreach ($question->get_words() as $place => $value) {
            $qprefix = $qa->get_qt_field_name('');
            $inputname = $qprefix . 'p' . ($place);
            $checked = null;
            $icon = "";
            $class = ' class=selectable ';
            /* if the current word/place exists in the response */
            if (array_key_exists('p' . ($place), $response)) {
                $checked = 'checked=true';
                $class = ' class=selected';
                if ($this->is_correct_place($correctplaces, $place)) {
                    $icon = $this->feedback_image(1);
                }
                if ($icon == "") {
                    $icon = $this->feedback_image(0);
                }
            }elseif($this->is_correct_place($correctplaces,$place)){
             if($options->correctness==1){
                 /* if the word is a correct answer but not selected
                  * and the marking is complete (correctness==1)
                  */
                $value='['.$value.']';    
             }
            }


            $readonly = "";
            /* When previewing after a quiz is complete */
            if ($options->readonly) {
                // $readonly = array('disabled' => 'true');
                $readonly = " disabled='true' ";
            }

            $regex = '/' . $value . '/';
            if (@preg_match($regex, $question->selectable)) {
                $output.='<input hidden=true ' . $checked . ' type="checkbox" name=' . $inputname . $readonly . ' id=' . $inputname . '>';
                $output .='<span name=' . $inputname . $class . '>' . $value . $icon . '</span></input>';
                $output.=' ';
            } else {
                $output.=' ' . $value;
            }
        }
        return $output;
    }
    /**
     * 
     * @param array $correctplaces
     * @param int $place 
     * @return boolean
     * Check if the number represented by place occurs in the 
     * array of correct places
     */
    protected function is_correct_place($correctplaces, $place) {
        foreach ($correctplaces as $key => $correctplace) {
            if ($place == $correctplace) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param question_attempt $qa
     * @return string feedback for correct/partially correct/incorrect feedback 
     */
    protected function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

}
