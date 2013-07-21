<?php


if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
$TMPL['page_name_title'] = '';
class full_news extends base {
  function full_news() {
    global $FORM;

    if (isset($FORM['id'])) { $news = new stats_site; } 
  }

 
}

class stats_site extends full_news {
  function stats_site() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG[''];

    $TMPL['id'] = $DB->escape($FORM['id'], 1);
    $id = $TMPL['id'];

    $news = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_news WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
    unset($news['username']);

    if ($news) {
      $TMPL = array_merge($TMPL, $news);

$username = $TMPL['username'];
      $TMPL['category_url'] = urlencode($TMPL['category']);

      $TMPL['header'] .= "  {$TMPL['title']}";

      $TMPL['content'] = $this->do_skin('main_full_news');
	  $TMPL['content'] .= $this->show_ads();
		session_start();

		if(!isset($_SESSION["main_page_news_{$id}"])){
			$_SESSION["main_page_news_{$id}"] = "1";
			$query = "UPDATE {$CONF['sql_prefix']}_news SET views = views + 1 WHERE id = '{$id}'";
			$DB->query($query, __FILE__, __LINE__);
		} 	  
	  //
    
    }
    else {
      $this->error($LNG['g_invalid_news_id']);
    }
  }
}


?>
