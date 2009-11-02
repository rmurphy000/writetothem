<?
/*
 * Record answer to questionnaire to find out whether the representative
 * replied to their constituent.
 * 
 * Copyright (c) 2004 UK Citizens Online Democracy. All rights reserved.
 * Email: francis@mysociety.org. WWW: http://www.mysociety.org
 *
 * $Id: response.php,v 1.7 2009-11-02 11:40:20 louise Exp $
 * 
 */

require_once "../phplib/fyr.php";
require_once "../phplib/queue.php";

require_once "../../phplib/utility.php";

fyr_rate_limit(array());

$token = get_http_var('token');
if (!$token) {
    $missing_token_message = cobrand_missing_token_message($cobrand);
    if (!$missing_token_message) {
         $missing_token_message = "Please make sure you copy the URL from your email properly. The token was missing.";
    }
    template_show_error($missing_token_message);
}
$answer = get_http_var('answer');
if ($answer != "yes" && $answer != "no") {
    template_show_error("Please make sure you copy the URL from your
        email properly. The answer type was missing.");
}

$yes_url = cobrand_url($cobrand, "/firsttime?token=" . urlencode($token) .  "&answer=yes");
$no_url = cobrand_url($cobrand, "/firsttime?token=" . urlencode($token) .  "&answer=no");

$values = array(
    'first_time_yes' => "\"$yes_url\"",
    'first_time_no' => "\"$no_url\"",
    'cobrand' => $cobrand
    );

// Look up info about the message
$msg_id = msg_get_questionnaire_message($token);
msg_check_error($msg_id);
if (!$msg_id) {
    template_show_error("Failed to look up message id for token");
}
$msg_info = msg_admin_get_message($msg_id);
msg_check_error($msg_info);
$values = array_merge($msg_info, $values);

// 0 is the responsiveness question
$result = msg_record_questionnaire_answer($token, 0, $answer);
msg_check_error($result);
if ($answer == "yes") {
    template_draw("response-yes", $values);
} elseif ($answer == "no") {
    template_draw("response-no", $values);
} else {
    template_show_error("Unknown answer.");
}

?>

