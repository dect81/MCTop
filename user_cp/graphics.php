<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}


$TMPL['header'] = 'КП - Графики';

if(isset($_GET['type'])){
	$type = $_GET['type'];
	if ($type == 1) {
		graphics::graphics_position();
	}
	if ($type == 2) {
		graphics::graphics_views();
	}
	if ($type == 3) {
		graphics::graphics_clicks();
	}
} else {
	$TMPL['user_cp_content'] = '<br/><hr/>';
	$TMPL['user_cp_content'] .= '<h3>Выберите тип графика</h3><hr/>';
	$TMPL['user_cp_content'] .= '<h4><a href="http://mctop.su/cp/graphics/1">Позиция сервера [за месяц]</a></h4>';
	$TMPL['user_cp_content'] .= '<h4><a href="http://mctop.su/cp/graphics/2">Просмотры страницы сервера [за месяц]</a></h4>';
	$TMPL['user_cp_content'] .= '<h4><a href="http://mctop.su/cp/graphics/3">Переходы на сайт сервера [за месяц]</a></h4>';
}


?>