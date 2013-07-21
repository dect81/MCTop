<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class stats extends join_edit {
  function stats() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];

    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
$uid = $_COOKIE['uid'];


$TMPL['user_cp_content'] = '<h3>Ваши голоса</h3><hr size ="1">';

 $result = $DB->query("SELECT * FROM {$CONF['sql_prefix']}_votes WHERE vk_id = {$uid} order by time desc", __FILE__, __LINE__);
 while ($row = $DB->fetch_array($result)) 
 {

    $vd = array_merge($TMPL, $row);
	$vote = $vd['vote'];
	if ($vote == 1) {
	 $vote = "<font color='green'><b>да</b></font>";
	} else {
	 $vote = "<font color='red'><b>нет</b></font>";
	}
	$form = <<<HTML
	<a href="{$list_url}/playerCp/stats/vote/{$vd['id']}"><table border='0' cellspacing = '3'>
	<tr><td>ID</td><td>{$vd['id']}</td></tr>
	<tr><td>Логин администратора</td><td>{$vd['username']}</td></tr>
	<tr><td>Подтвержден</td><td>{$vote}</td></tr>
	</table></a>
    <hr size ="1">
HTML;
$TMPL['user_cp_content'] .= $form;
 }
//$TMPL['user_cp_content'] = $this->do_skin('user_cp_adv');
  }

       
}

?>