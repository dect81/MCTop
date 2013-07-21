<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class link_code extends join_edit {
  function link_code() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];
	//$TMPL['user_cp_content'] = '<br/><br/>Еще не готово';
      $this->form();

  }

  function form() {
	global $CONF, $DB, $LNG, $TMPL;
	list($id,$advertiser) = $DB->fetch("SELECT id, advertiser FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
	$servers_count = mysql_fetch_array(mysql_query("SELECT count(*) from rtt_servers_real where username='{$TMPL['username']}'"));
	$servers_count = $servers_count[0];
	$TMPL['project_id'] = $id;
	$list_url = 'http://mctop.su';
	if (!$advertiser){
		$return_set = array();

		$row = mysql_query("SELECT * from rtt_servers_real WHERE username = '{$TMPL['username']}'");
		//echo "SELECT * from rtt_servers_real WHERE username = '{$TMPL['username']}'";
		while ($server = mysql_fetch_assoc($row)) 
			array_push($return_set, $server);	
		for ( $i=0; $i<=$servers_count-1; $i++) {
			//echo $i;
			$server_info = $return_set[$i];
			$status = $server_info['status'];
			$sid = $server_info['id'];
			$form .= <<<HTML
			<img src="{$list_url}/status/s{$sid}/1" />
			<br>Код: <input readonly="readonly" type="text" name="code" size="47" onClick="this.select()" value='<img src="{$list_url}/status/s{$sid}/1" />' />
			<br/>------------------------------------------------------------------------------------------------<br>			
HTML;
		}
	
		$TMPL['accordion'] = $form;


		//$TMPL = array_merge($TMPL, $row);
		$TMPL['user_cp_content'] = $this->do_skin('link_code');
	} else {
		$TMPL['error_text'] = 'Данная функция доступна только для администраторов серверов.';
		$TMPL['user_cp_content'] = $this->do_skin('adv_error');
	}
	

	
HTML;



}


  }

 

?>
