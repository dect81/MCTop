<?php

class request_server
{
  private $str;
  private $error;
  public function __construct()
  {
    $this->str = 'No request send';
  }
  
  public function __invoke($url, $get = array(), $post = false)
  {
    return $this->str = $this->CurlPost($url, $get, $post);
  }
  public function __get( $name )
  {
    if ($name == 'error')
      return $this->error;
    return 'undef';
  }
  public function __toString()
  {
    return (string)$this->str;
  }
  
  public static function MakeGet($get)
  {
    if (count($get))
      return "?".http_build_query($get);
    return '';
  }

  public static function MakePost($post)
  {
    return http_build_query($post);
  }


  private function CurlPost($url, $get = array(), $post = false)
  {
    $url_str = $url.self::MakeGet($get);

    $r = curl_init($url_str);
    
    //echo "Request $url_str\n";
    
    curl_setopt($r, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($r, CURLOPT_ENCODING, 'gzip, deflate'); 
    curl_setopt($r, CURLOPT_TIMEOUT, 30);
    
    if ($post != false)
    {
      curl_setopt($r, CURLOPT_POST, 1);
      curl_setopt($r, CURLOPT_POSTFIELDS, self::MakePost($post));
    }
    $ret = curl_exec($r);
    if (!$ret)
      $this->error = curl_error($r);
    curl_close($r);
    
    unset($r, $url_str, $url, $get, $post);
    return $ret;
  }
}


class vk_login extends base {

	  private static $appid = 2637167;
	  private static $secret_key = "1XVfl8zOYIbsxtpp9pA9";
	  private static $back_url = "http://mctop.su/index.php?a=vk_login";

	function login(){
	    $display = "page";
		$url = "https://oauth.vk.com/authorize?".
	"client_id=".self::$appid."&".
	"scope=$scope&".
	"redirect_uri=".self::$back_url."&".
	"display=$display&".
	"response_type=code";
		new redirect($url);
		exit();
	}

	function vk_login() {
		global $CONF, $DB, $FORM, $LNG, $TMPL;

		if(isset($_GET['code'])) {

			$url = "https://oauth.vk.com/access_token?". 
			"client_id=".self::$appid."&".
			"client_secret=".self::$secret_key."&".
			"code={$_GET['code']}&".
			"redirect_uri=".self::$back_url;

			$r = new request_server();
			$result = json_decode($r($url), true);

			if(isset($result['access_token'])){
				session_start();
				$_SESSION['user_id']=$result['user_id'];	
				$_SESSION['access_token']=$result['access_token'];			
				
				$url = "https://api.vk.com/method/users.get?". 
				"uid=".$_SESSION['user_id']."&".
				"access_token=".$_SESSION['access_token'];
				
				$r = new request_server();		
				$result = json_decode($r($url), true);


                $res = $DB->fetch("SELECT * FROM vk_logins WHERE vk_id = '{$_SESSION['user_id']}' LIMIT 1",__FILE__, __LINE__);

                if($res['id']){

					if($res['name']<>$result['response'][0]['first_name']." ".$result['response'][0]['last_name']) {					
						$query = $DB->query("UPDATE `vk_logins` set `name` = '".$result['response'][0]['first_name']." ".$result['response'][0]['last_name']."' where vk_id='{$_SESSION['user_id']}'",__FILE__, __LINE__) or die(mysql_error());
						$query = $DB->query("UPDATE `rtt_users` set `name` = '".$result['response'][0]['first_name']." ".$result['response'][0]['last_name']."' where vk_id='{$_SESSION['user_id']}'",__FILE__, __LINE__) or die(mysql_error());
					}
					list($hash) = $DB->fetch("SELECT hash FROM rtt_votes where vote = 0 and vk_id = {$_SESSION['user_id']} ORDER BY time desc", __FILE__, __LINE__);
					//echo $hash;
					//die();
					$rand = mt_rand(100000,999999);
                    $pwd = $_SESSION['user_id'] . 'verysecretlonglongword';
					setcookie('uid',$_SESSION['user_id']);
					setcookie('pass',md5($rand.md5($pwd).$rand));
                    setcookie('r_vote',$hash,time()+60*60*24*30,'/');
                }

                else {


                    $rand = mt_rand(100000,999999);
                    $pwd = $_SESSION['user_id'] . 'verysecretlonglongword';

                    $res = $DB->query("INSERT INTO `vk_logins` (`vk_id`, `name`, `passwd`, `rand`, `singup`, `singin`) VALUES ('{$_SESSION['user_id']}','".$result['response'][0]['first_name']." ".$result['response'][0]['last_name']."', '". md5($pwd). "', $rand, NOW(), NOW())",__FILE__, __LINE__);

                    $res = $DB->query("INSERT INTO `rtt_users` (vk_id, name)  VALUES ('{$_SESSION['user_id']}', '".$result['response'][0]['first_name']." ".$result['response'][0]['last_name']."')",__FILE__, __LINE__);;
					list($hash) = $DB->fetch("SELECT hash FROM rtt_votes where vote = 0 and vk_id = {$_SESSION['user_id']} ORDER BY time desc", __FILE__, __LINE__);
                    setcookie('uid',$_SESSION['user_id']);
                    setcookie('pass',md5($rand.md5($pwd).$rand));
                }
				
				if($hash) 					
					header("Location: http://mctop.su/go_to_vote");
				else 
					header("Location: http://mctop.su/playerCp");

			}
			
		}

    else
	    die('Hacking attempt');

	}

}
?>