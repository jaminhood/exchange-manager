<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === cheching if the user exists
function emCheckUser()
{
  # === cheching if the user submitted a request
  if (isset($_REQUEST)) :
    # === field and value requests
    $field = $_REQUEST['field'];
    $value = $_REQUEST['value'];
    # === boolean data to be returned
    $data = 0;
    # === check if user exists
    if (get_user_by($field, $value)) :
      # === change data to true
      $data = 1;
    endif;
    # === success message
    wp_send_json_success($data);
  endif;
  # === kill request
  die();
}
# === Hooking the ajax function into wordpress
add_action('wp_ajax_emCheckUser', 'emCheckUser');
add_action('wp_ajax_nopriv_emCheckUser', 'emCheckUser');

function emCheckPass()
{
  # === cheching if the user submitted a request
  if (isset($_REQUEST)) :
    # === username and password requests
    $user = $_REQUEST['username'];
    $pass = $_REQUEST['password'];
    # === boolean data to be returned
    $data = 0;
    # === get current user
    $user = get_user_by('login', $user);
    # === check if user exists
    if ($user && wp_check_password($pass, $user->data->user_pass, $user->ID)) :
      # === change data to true
      $data = 1;
    endif;
    # === success message
    wp_send_json_success($data);
  endif;
  # === kill request
  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_emCheckPass', 'emCheckPass');
add_action('wp_ajax_nopriv_emCheckPass', 'emCheckPass');

function emlogUserIn()
{
  # === cheching if the user submitted a request
  if (isset($_REQUEST)) {
    # === username, password and remember me requests
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $rememberMe = $_REQUEST['rememberMe'];
    $rememberMeValue = false;
    # === Check if remember me input is true
    if ($rememberMe == 1) {
      # === Set remember me value to true
      $rememberMeValue = true;
    }
    # === Array to be processed
    $creds = array(
      'user_login'    => $username,
      'user_password' => $password,
      'remember'      => $rememberMeValue
    );

    $user = wp_signon($creds, is_ssl());

    $data = 0;

    if (is_wp_error($user)) {
      echo $user->get_error_message();
      $data = 0;
    } else {
      $data = 1;
    }

    wp_send_json_success($data);
  }

  die();
}

// Hooking the ajax function into wordpress
add_action('wp_ajax_emlogUserIn', 'emlogUserIn');
add_action('wp_ajax_nopriv_emlogUserIn', 'emlogUserIn');
