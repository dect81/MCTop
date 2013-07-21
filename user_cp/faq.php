<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class faq extends base {
	function faq() {
		global $DB, $CONF, $ADM, $LNG, $TMPL;
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];
			$id = stripslashes($id);
			$id = $DB->escape($id);
			$article = $DB->fetch("SELECT * from rtt_faq where id = '{$id}'",__FILE__,__LINE__);
			$title = $article['title'];
			$text = $article['text'];
			$moder_name = $article['moder_name'];
			$form = "<hr/>Статья №{$id}: $title<hr/></br>$text<hr/>Автор статьи: $moder_name<br/><hr/><h3><a href='http://mctop.su/cp/faq'>У меня есть еще вопросы!</a></h3>
			";
			$TMPL['user_cp_content'] = "<br/><br/>$form";
		} else {
		
			$TMPL['user_cp_content'] = "<br/><hr/><h3>FAQ. </h3><hr size ='1'/>";
			$TMPL['user_cp_content'] .= $LNG['u_cp_faq_notice'];
			$TMPL['user_cp_content'] .= "<script language='javascript' src='http://mctop.su/skins/Main/js/user_cp_faq.js'></script>
			<form id='searchForm' name='searchForm' method='post' action='javascript:insertTask();'>
			<div class='searchInput'>
			<input name='searchq' type='text' id='searchq' size='100%' onkeyup='javascript:searchNameq()'/>
			</div>
			</form>

			<div id='msg'></div>
			<div id='search-result'></div>";
	}
  }
}
?>