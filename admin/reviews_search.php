<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class reviews_search extends base {
  function reviews_search() {
    global $DB, $CONF, $ADM, $LNG, $TMPL;
	$TMPL['message'] = $ADM['adm_msg'];

$timestamp = time();
$todayis = strftime('%Y-%m-%d',$timestamp);
    list($num_servers) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers where advertiser <> '1' and active <> '0'", __FILE__, __LINE__);
    list($num_servers_active) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers where active = 1 and advertiser <> '1' AND (success/attemps*100) > 30 ", __FILE__, __LINE__);
    list($num_servers_today) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers where join_date = '{$todayis}' and advertiser <> '1'", __FILE__, __LINE__);
    $TMPL['admin_content'] = "<hr><br><h3>Поиск по комментам</h3><br>";
    $TMPL['admin_content'] .= "<script language='javascript' src='http://mctop.su/skins/Main/js/ajax_framework.js'></script>
<form id='searchForm' name='searchForm' method='post' action='javascript:insertTask();'>
<div class='searchInput'>
<input name='searchq' type='text' id='searchq' size='30' onkeyup='javascript:searchNameq()'/>
<input type='button' name='submitSearch' id='submitSearch' value='Search' onclick='javascript:searchNameq()'/>
</div>
</form>

<div id='msg'></div>
<div id='search-result'></div>";





    $TMPL['admin_content'] .= "{$LNG['a_main_your']}: {$TMPL['version']}<br />";

    $TMPL['content'] = $this->do_skin('admin');
  }
}
?>