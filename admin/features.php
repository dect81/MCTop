<?php
//==============================\\
//             L.R.E            \\
//     Created by RaM Team      \\
//  Based on Aardvark Topsite   \\
//==============================\\ 

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class features extends base {
  function features() {
    global $DB, $CONF, $ADM, $LNG, $TMPL;
	$TMPL['message'] = $ADM['adm_msg'];
    //$TMPL['admin_content'] = "{$LNG['a_main']}<br /><br />";
	$TMPL['admin_content'] = "Список фич для админов:<br>";
    $TMPL['admin_content'] .= "Сообщение на главной АП можно менять <a href='{$list_url}/admin/o/message'>здесь</a><hr size='1'>";





    $TMPL['admin_content'] .= "{$LNG['a_main_your']}: {$TMPL['version']}<br />";

    $TMPL['content'] = $this->do_skin('admin');
  }
}
?>