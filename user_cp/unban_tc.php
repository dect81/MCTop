<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class unban_tc extends join_edit {
  function unban_tc() {
    global $FORM, $LNG, $TMPL, $CONF, $DB;

    $TMPL['header'] = $LNG['edit_header'];


	$this->form();
  }



  function form() {
	global $CONF, $DB, $LNG, $TMPL;
	$act = $_GET['act'];
	list($advertiser, $id, $status, $active) = $DB->fetch("SELECT advertiser, id, status, active FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
	$TMPL['status']=$status;
	if(!$advertiser) {
		if (($act == 'try') and $status and ($active==2)) {
			$TMPL['msg']="Вы включили отображение сервера в рейтинге";  
			$TMPL['user_cp_content'] = $this->do_skin('user_cp_restore');
			$DB->query("UPDATE {$CONF['sql_prefix']}_servers SET active = '1' WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
		} else {
			if(($status == 1) and ($active==1)) {
				$TMPL['msg']="Ваш сервер отображается в рейтинге";   
			} elseif (($status == 1) and ($active==2)) {
				$TMPL['msg']="Ваш сервер не отображается в рейтинге";
				$TMPL['msg_restore']="Для включения отображения сервера перейдите по ссылке: <a href='http://mctop.su/cp/restore/try'>http://mctop.su/cp/restore/try</a>";
			} elseif (($status == 0) and ($active==2)) {
				$TMPL['msg']="Ваш сервер не отображается в рейтинге";				
				$TMPL['msg_restore']="Возможность включения отображения будет доступна после включения сервера";				
			}
			$TMPL['user_cp_content'] = $this->do_skin('user_cp_restore');
		}
	} else {
			$TMPL['error_text'] = 'Данная функция доступна только для администраторов серверов.';
			$TMPL['user_cp_content'] = $this->do_skin('adv_error');
		}
}
  }
?>
