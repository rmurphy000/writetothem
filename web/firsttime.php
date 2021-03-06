<?
/*
 * firsttime.php:
 * Record answer to question about whether this is the first time the
 * constituent has contacted an elected representative.
 * 
 * Copyright (c) 2004 UK Citizens Online Democracy. All rights reserved.
 * Email: francis@mysociety.org. WWW: http://www.mysociety.org
 *
 * $Id: firsttime.php,v 1.10 2009-11-19 11:41:18 matthew Exp $
 * 
 */

require_once "../phplib/fyr.php";
require_once "../phplib/queue.php";

require_once "../commonlib/phplib/utility.php";
require_once "../commonlib/phplib/survey.php";

fyr_rate_limit(array());

$token = get_http_var('token');
if (!$token) {
    template_show_error("The token was missing.");
}
$answer = get_http_var('answer');
if ($answer != "yes" && $answer != "no") {
    template_show_error("The answer type was missing.");
}

// 1 is the firsttime question
$result = msg_record_questionnaire_answer($token, 1, $answer);
if (rabx_is_error($result)) {
    template_show_error($result->text);
}
$values = msg_admin_get_message($result);
$values['cobrand'] = $cobrand;
// Demographic survey
list($values['user_code'], $values['auth_signature']) = survey_sign_email_address($values['sender_email']);
$done_survey = survey_check_if_already_done($values['user_code'], $values['auth_signature']);
//$rand = rand(0, 4); // high rate when want lots of data
$rand = rand(0, 29); // low rate when not actively doing research, so still get 50-100 responses a month
if (!$done_survey && $rand == 0 && cobrand_display_survey($cobrand)) {
    $values['return_url'] = OPTION_BASE_URL . htmlspecialchars($_SERVER['REQUEST_URI']);
    template_draw("survey-questions", $values);
} else {
    // Either the questionnaire or the survey done
    template_draw("survey-done", $values);
}

