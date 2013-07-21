<?php


if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
$TMPL['page_name_title'] = '';
class full_news extends base {
  function full_news() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    if (isset($FORM['id'])) {
	$id = $DB->escape($FORM['id'], 1);
    session_start();

	if(!isset($_SESSION["news_{$id}"])){
		$_SESSION["news_{$id}"] = "1";
		$query = "UPDATE {$CONF['sql_prefix']}_adm_news SET views = views + 1 WHERE id = '{$id}'";
		$DB->query($query, __FILE__, __LINE__);
	} 

    $TMPL['header'] = $LNG[''];



    $news = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_adm_news WHERE id = '{$id}'", __FILE__, __LINE__);

    if ($news) {
		$TMPL = array_merge($TMPL, $news);


		$TMPL['category_url'] = urlencode($TMPL['category']);

		$TMPL['header'] .= "  {$TMPL['title']}";
		$time = $news['time'];

		$time = date('Y-m-d H:i', $time);
		$TMPL['date'] = $time;
		$TMPL['user_cp_content'] = $this->do_skin('user_cp_full_news');
	  
	  //
    
    }
    else {
      $this->error($LNG['g_invalid_news_id']);
    }	
	}
  }

 
}




?>
