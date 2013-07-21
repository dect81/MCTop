<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class logs extends join_edit {
  function logs() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['a_edit_header'];

  
    $TMPL['username'] = $DB->escape($FORM['u']);
    $TMPL['module'] = $DB->escape($FORM['m']);

    list($check) = $DB->fetch("SELECT 1 FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    if ($check) {
        $this->form();
    }
    else {
      $this->error($LNG['g_invalid_u'], 'admin');
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;

	$query = "SELECT * FROM {$CONF['sql_prefix']}_logs WHERE username = '{$TMPL['username']}' and disabled = '0' ORDER BY `time` desc ";
	//echo $query;
    $result = $DB->select_limit($query, $CONF['num_list'], $start, __FILE__, __LINE__);
      while ($row = $DB->fetch_array($result)) {
		$time = $row['time'];
		$time = date('Y-m-d H:i', $time);
		$string = "
{$row['id']} | {$time} | {$row['action']}<br/ >";
		$TMPL['admin_content'] .= $string;
	  }
    


  }

}
?>
