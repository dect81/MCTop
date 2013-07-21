<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class rating_stats extends base {
  function rating_stats() {
    global $DB, $CONF, $ADM, $LNG, $TMPL;
	$TMPL['message'] = $ADM['adm_msg'];
$result = $DB->query("SELECT data FROM {$CONF['sql_prefix']}_sessions WHERE type = 'admin'", __FILE__, __LINE__);
while ($row = $DB->fetch_array($result)) 
{
$admins_online .= ''.$row['data'].'; ';
}  

$result = $DB->query("SELECT data FROM {$CONF['sql_prefix']}_sessions WHERE type = 'user_cp'", __FILE__, __LINE__);
while ($row = $DB->fetch_array($result)) 
{

$users_online .= ''.$row['data'].'; ';
} 
$timestamp = time();
$todayis = strftime('%Y-%m-%d',$timestamp);
    list($ads_views_today) = $DB->fetch("SELECT ads_views_today FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);
    list($ads_clicks_today) = $DB->fetch("SELECT ads_clicks_today FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);
	list($num_servers_today) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers where join_date = '{$todayis}' and advertiser <> '1'", __FILE__, __LINE__);
    $ctr = ($ads_clicks_today/$ads_views_today)*100;
    $ctr = ((int)($ctr)/100.000);
	$TMPL['admin_content'] = "<hr><br><h3>Мини-статистика по рейтингу</h3><br>";
    $TMPL['admin_content'] .= "
Показов рекламы за день: {$ads_views_today}<br/>
Кликов по рекламе за день: {$ads_clicks_today}<br/>
CTR в % за день: {$ctr}%<br/>
<hr>
Серверов сегодня: {$num_servers_today}<br><hr>";





    $TMPL['admin_content'] .= "{$LNG['a_main_your']}: {$TMPL['version']}<br />";

    $TMPL['content'] = $this->do_skin('admin');
  }
}
?>