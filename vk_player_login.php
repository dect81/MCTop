<?php 
class vk_player_login extends base {
	function clear_var($var)
	{
		$var = mysql_escape_string ($var);
		$var = htmlspecialchars ($var);
		return $var;
	}
	
	function vk_player_login() {
		global $CONF, $DB, $FORM, $LNG, $TMPL;


		$uid=$_REQUEST['uid'];
		$uid = intval($uid);
		$name=$_REQUEST['first_name'].' '.$_REQUEST['last_name'];

		$res = $DB->fetch("SELECT * FROM vk_logins WHERE vk_id = '$uid' LIMIT 1",__FILE__, __LINE__);
		setcookie('uid','');
		setcookie('pass','');
		if (isset($uid) and ($name)) {
		if ($res[id]) {
			list($hash) = $DB->fetch("SELECT hash FROM rtt_votes where vote = 0 and vk_id = $uid ORDER BY time desc", __FILE__, __LINE__);
			$user = $DB->fetch("SELECT * FROM vk_logins WHERE vk_id = $uid",__FILE__, __LINE__);
			$res = $DB->query("UPDATE vk_logins SET name = '$name', singin=NOW() WHERE vk_id = '$uid' LIMIT 1",__FILE__, __LINE__);

			setcookie('pass',md5($user['rand'].$user['passwd'].$user['rand']));
			setcookie('uid',$user['vk_id']);
			setcookie('r_vote',$hash,time()+60*60*24*30,'/');


		} else {


			$rand = mt_rand(100000,999999);

			$pwd = $uid . 'verysecretlonglongword';

			$pid=md5(uniqid(rand(),true));

			$res = $DB->query("INSERT INTO `vk_logins` (`vk_id`, `name`, `passwd`, `rand`, `singup`, `singin`) VALUES ('$uid','$name', '". md5($pwd). "', $rand, NOW(), NOW())",__FILE__, __LINE__);


			list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_users", __FILE__, __LINE__);
			if (!$id) {$id = 1;}

			$res = $DB->query("INSERT INTO rtt_users (id, vk_id, name)  VALUES ('$id','$uid', '$name')",__FILE__, __LINE__);;

			setcookie('pass',md5($rand.md5($pwd).$rand));

			setcookie('uid',$uid);

		}



			header("Location: http://mctop.su/playerCp");
			} else {
				die ('hacking attempt');
			}
	}

}
?>