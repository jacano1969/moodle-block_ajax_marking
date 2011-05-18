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
 * This file conatins all the javascript for the AJAX Marking block
 * 
 * @package    block
 * @subpackage ajax_marking
 * @copyright  2011 Matt Gibson
 * @author     Matt Gibson {@link http://moodle.org/user/view.php?id=81450}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * This is to allow us to display a decent comment form for providing quiz feedback, rather that the
 * homemade one that the quiz module actually constructs via HTML fragments. We need to build this 
 * form both before and after the data is submitted.
 * 
 * Should pass in an array of question ids and the attemptobject as customdata
 *  
 */
class block_ajax_marking_quiz_form extends moodleform {
    
    function definition() {
        
        global $DB, $OUTPUT;
        
        $mform =& $this->_form;
        
        $mform->addElement('hidden', 'attemptid');
        $mform->setType('attemptid', PARAM_INT);
        $mform->addElement('hidden', 'questionid');
        $mform->setType('questionid', PARAM_INT);
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->setType('sesskey', PARAM_ALPHANUM);
        
        // User picture and name
        $student = $DB->get_record('user', array('id' => $attemptobj->get_userid()));
        $picture = $OUTPUT->user_picture($student, array('courseid'=>$attemptobj->get_courseid()));
        
        $mform->addElement('static', 'picture', $OUTPUT->user_picture($this->_customdata->user),
                                                fullname($this->_customdata->user, true) . '<br/>' .
                                                userdate($this->_customdata->submission->timemodified) .
                                                $this->_customdata->lateness );
        
        
        // Now come multiple (possibly) multiple question comment fields 
        
        // use $atemptobj->get_questions($arrayofquestionis);
        
        foreach ($this->_customdata->questions as $questionid => $question) {
            
            $mform->addElement('header', 'question'.$questionid, get_string('question', 'modulename'));
            
            // Display question text
            
            // Display user's answer
            
            // Display comment form
            $mform->addElement('editor', 'comment['.$questionid.']', get_string('comment', 'quiz').':', null, $this->get_editor_options() );
            
            // Display grade selector
            $grademenu = make_grades_menu($question->grade);
            $grademenu['-1'] = get_string('nograde');
            $mform->addElement('select', 'grade['.$questionid.']', get_string('grade').':', $grademenu, $attributes);
            
            // TODO set default to existing grade?
            $mfrom->setDefault('grade['.$questionid.']', -1);
            
            
            // outcomes?
            // lifted from the assignment module:
//            if (!empty($this->_customdata->enableoutcomes)) {
//                foreach($this->_customdata->grading_info->outcomes as $n=>$outcome) {
//                    $options = make_grades_menu(-$outcome->scaleid);
//                    if ($outcome->grades[$this->_customdata->submission->userid]->locked) {
//                        $options[0] = get_string('nooutcome', 'grades');
//                        echo $options[$outcome->grades[$this->_customdata->submission->userid]->grade];
//                    } else {
//                        $options[''] = get_string('nooutcome', 'grades');
//                        $attributes = array('id' => 'menuoutcome_'.$n );
//                        $mform->addElement('select', 'outcome_'.$n.'['.$this->_customdata->userid.']', $outcome->name.':', $options, $attributes );
//                        $mform->setType('outcome_'.$n.'['.$this->_customdata->userid.']', PARAM_INT);
//                        $mform->setDefault('outcome_'.$n.'['.$this->_customdata->userid.']', $outcome->grades[$this->_customdata->submission->userid]->grade );
//                    }
//                }
//            }
            
        }
        

        
    }
    
    function process_data() {
    
    
    }
    
}

?>