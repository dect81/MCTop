<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

function new_day($current_day) {

  global $CONF, $DB;


  $DB->query("UPDATE {$CONF['sql_prefix']}_etc SET last_new_day = {$current_day}", __FILE__, __LINE__);
  $DB->query("UPDATE {$CONF['sql_prefix']}_stats SET ads_clicks_today = 0", __FILE__, __LINE__);
  $DB->query("UPDATE {$CONF['sql_prefix']}_admin SET ads_views_today = 0", __FILE__, __LINE__);
  if ($CONF['delete_after'] > 0) {
    $result = $DB->query("SELECT username FROM {$CONF['sql_prefix']}_stats WHERE days_inactive >= {$CONF['delete_after']}", __FILE__, __LINE__);

    for ($i = 0; list($username) = $DB->fetch_array($result); $i++) {
      if ($i > 0) {
        $delete_usernames .= ', ';
      }
      else {
        $delete_usernames = '';
      }
      $delete_usernames .= "'{$username}'";
    }

    if ($i != 0) {
      $DB->query("DELETE FROM {$CONF['sql_prefix']}_servers WHERE username IN({$delete_usernames})", __FILE__, __LINE__);
      $DB->query("DELETE FROM {$CONF['sql_prefix']}_stats WHERE username IN({$delete_usernames})", __FILE__, __LINE__);
      $DB->query("DELETE FROM {$CONF['sql_prefix']}_reviews WHERE username IN({$delete_usernames})", __FILE__, __LINE__);
    }
  }
}

function new_week($current_week) {
  global $CONF, $DB;

  $DB->query("UPDATE {$CONF['sql_prefix']}_etc SET last_new_week = {$current_week}", __FILE__, __LINE__);
}

function new_month($current_month) {
  global $CONF, $DB;
  $DB->query("UPDATE {$CONF['sql_prefix']}_etc SET last_new_month = {$current_month}", __FILE__, __LINE__);
  //$DB->query("UPDATE {$CONF['sql_prefix']}_stats SET `num_ratings` = 0");		  

		  
}
?>
	