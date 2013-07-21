<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class adv extends join_edit {
  function adv() {
    global $FORM, $LNG, $TMPL;       

    $TMPL['header'] = $LNG['user_cp_header'];

    $TMPL['user_cp_content'] = 
    "
    <!ATTLIST li title CDATA #IMPLIED>
    <div style='background-color: yellow; display:inline-block'><b>Юзер, помни! Когда ты под админом, рекламы <a href='javascript:alert(\"Считается что админы точно не будут играть на чужих проектах\");'>не видно</a>!</b></div>
    <br>
    <br>
    Что нужно сделать что бы заказать рекламу:
    <ul id='mcta_help'>
    <li>Перейти в <a href='ads_redirect' title='Личный кабинет'>MCTA</a></li>
    <li title='Поддерживается только jpg, png, gif, размером не более 300кб'>В вкладке \"заявки\" оставить заявку на баннер </li>
    <li title='После прихода письма, проверяйте спам фильтры'>Убедиться что она появилась в заявках</li>
    <li title='От часа до пары дней'>Дождаться письма с сообщением о утверждении заявки</li>
    <li title='Ecли перевод не зачислен за 48 часов, пишите!'>Перевести деньги согласно инструкциям в письме</li>
    <li title='Результат виден в КП мгновенно, остановить рекламу невозможно'>Часть суммы или всю потратить на показы баннера</li>
    </ul>
    <br><br>
    Для тех кто хочет заказать клики:<br>
    при заказе показов цена клика колеблется 0.5-1.5р, просто попробуйте заказать тысячу, убедитесь.
    <br><br>
    <div style='background-color: green; display:inline-block'><b>Последнее напутствие, пользуйтесь <a href='http://ads.mctop.su/ts.php'>технической поддержкой</a> в MCTA, ибо почта обрабатывается значительно реже.</b></div>
    
    
    <script src='http://code.jquery.com/jquery-1.8.3.js'></script>
  <script src='http://code.jquery.com/ui/1.10.0/jquery-ui.js'></script>
  <link rel='stylesheet' href='http://jqueryui.com/resources/demos/style.css' />
  <style>
  li.mouseover {background-color:gray;}
  li.mouseout {background-color:white;}
  </style>
  <script>
$(document).ready(function() {   
  
     $( document ).tooltip();

 $('#mcta_help li').mouseover(function() {
 
    //Add and remove class, Personally I dont think this is the right way to do it, 
    //if you have better ideas to toggle it, please comment    
    $(this).addClass('mouseover');
    $(this).removeClass('mouseout');   
     
  }).mouseout(function() { 
     
    //Add and remove class
    $(this).addClass('mouseout');
    $(this).removeClass('mouseover');    

  });  
         });
  </script>
    ";
    
    echo '<script type="text/javascript"> _shcp = []; _shcp.push({widget_id : 550327, widget : "Chat", side : "bottom", position : "right", template : "dark", title : "Поддержка по системе MCTop ADS", title_offline : "Поддержка по системе MCTop ADS"';
    
    $user = array(
      'nick' => $TMPL['uid']."_".$TMPL['username'],
'avatar' => '',
'id' => $TMPL['uid'], 
'email' => $TMPL['email'],
'data' => array() 
);
$time = time();
$secret = "Ba96nC52zR";
$user_base64 = base64_encode( json_encode($user) );
$sign = md5($secret . $user_base64 . $time);
$auth = $user_base64 . "_" . $time . "_" . $sign;
    echo ", auth : \"$auth\"";
    echo ' }); (function() { var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true; hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://widget.siteheart.com/apps/js/sh.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hcc, s.nextSibling); })(); </script>';
    return;
    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
list($advertiser, $id) = $DB->fetch("SELECT advertiser, id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

$TMPL['user_cp_content'] = '<br/><hr/><h3>Список Ваших рекламных объявлений</h3><hr size ="1"/>';

 $result = $DB->query("SELECT * FROM {$CONF['sql_prefix']}_adv WHERE uid = {$id}", __FILE__, __LINE__);
 if(!mysql_num_rows($result)) {
		$TMPL['user_cp_content'] .= $LNG['u_cp_adv_not_exist'];	
 } else {
	while ($row = $DB->fetch_array($result)) 
	{
		$ADV = array_merge($TMPL, $row);
		$balance = $ADV['max_views'] - $ADV['views'];
		$form = <<<HTML
		<a href="{$list_url}/cp/adv/edit/{$ADV['id']}"><table border='0' cellspacing = '3'>
		<tr><td>ID</td><td>{$ADV['id']}</td></tr>
		<tr><td>Название</td><td>{$ADV['title']}</td></tr>
		<tr><td>Ссылка на сайт</td><td>{$ADV['url']}</td></tr>
		<tr><td>URL изображения</td><td>{$ADV['img_url']}</td></tr>
		<tr><td>Описание</td><td>{$ADV['descr']}</td></tr>
		<tr><td>Показов</td><td>{$ADV['views']} <span class="jQtooltip" title="Показов на счету">({$balance})</span></td></tr>
		<tr><td>Кликов</td><td>{$ADV['clicks']}</td></tr>
		</table></a>
		<hr size ="1">
HTML;
		$TMPL['user_cp_content'] .= $form;
	}
 }
//$TMPL['user_cp_content'] = $this->do_skin('user_cp_adv');
  }

       
}

?>
