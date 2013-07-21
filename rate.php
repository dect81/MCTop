<?php
class rate extends base {
    function rate()
    {
        global $CONF, $DB, $FORM, $LNG, $TMPL;

        $TMPL['header'] = $LNG['rate_header'];

        if(isset($FORM['u']) && $FORM['u'])
            $TMPL['username'] = $DB->escape($FORM['u'], 1);

        $project=$DB->fetch("SELECT id,username,title FROM {$CONF['sql_prefix']}_servers WHERE username='{$TMPL['username']}' LIMIT 1",__FILE__, __LINE__);

        if(isset($project)){

            if(isset($_COOKIE['uid']))
                $_COOKIE['uid']=intval($_COOKIE['uid']);

            if (isset($_COOKIE['uid'])&&isset($_COOKIE['r_vote'])&&$_COOKIE['r_vote']!="1")
            {

                $vote=$DB->fetch("SELECT username,hash,time,vote FROM {$CONF['sql_prefix']}_votes WHERE hash='{$_COOKIE['r_vote']}' AND vk_id='{$_COOKIE['uid']}'  LIMIT 1",__FILE__, __LINE__);

                if($vote['vote']==1 or ($vote['hash']<>$_COOKIE['r_vote'])) {
                    header("Location: http://mctop.su/rating/vote/{$TMPL['username']}");
                    setcookie('r_vote','', 0,'/');
                }

                if(isset($vote['username'])&&$vote['hash']==$_COOKIE['r_vote']){

                    if ($vote['username']==$TMPL['username'])
                    {
                        if($vote['time']<time())
                        {

                            if (isset($FORM['u']) && $FORM['u']) {

                                $TMPL['username'] = $DB->escape($FORM['u'], 1);

								  require_once('recaptchalib.php');
								  $TMPL['publickey'] = "6LcIquESAAAAAFJXQoGkNqyxhWn57erCB7S4lL4M"; 
								  $TMPL['privatekey'] = "6LcIquESAAAAAC-t6GSV6dwncr4aMvXaRjtWZdaK";

									
									if (!isset($FORM['rating'])) {
										$this->form_vote($project);
									}
									else {
										//var_dump($_POST);
										$resp = recaptcha_check_answer ($TMPL['privatekey'],
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);
										

										if (!$resp->is_valid) {

										// What happens when the CAPTCHA was entered incorrectly
										die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
											 "(reCAPTCHA said: " . $resp->error . ")");
										
									  } 
									  else 
										
										$this->process($_COOKIE['r_vote'],$resp->is_valid);
									  
								  
								}

                            }

                        }

                        else
                            $this->form_wait($project);

                    }

                    else
                        $this->form_another($vote, $project);

                    //var_dump($vote);

                }

            }

            else {

                if (isset($_COOKIE['uid'])&&isset($_COOKIE['pass']))
                {
				//echo 1;
                    $hash = md5(time() + rand(1, 5000));
                    $vote = $DB->fetch("SELECT `id`, `time`, `hash`,`username` FROM {$CONF['sql_prefix']}_votes  WHERE vk_id='{$_COOKIE['uid']}' AND vote='0' order by time desc LIMIT 1", __FILE__, __LINE__);

                    if($vote['id'])
                    {

                        setcookie('r_vote', $vote['hash'], $vote['time']+60*60*24*30,'/');

                        if ($vote['username']==$TMPL['username'])
                            $this->form_wait($project);

                        else
                            $this->form_another($vote, $project);

                    }
                    else {
						$time = time()-86400;
						
						$vote = $DB->fetch("SELECT `id`, `time`, `hash`,`username` FROM {$CONF['sql_prefix']}_votes  WHERE ip='{$_SERVER['REMOTE_ADDR']}' and time > '{$time}' AND vote=0 order by time desc LIMIT 1", __FILE__, __LINE__);
						//echo "<!-- SELECT `id`, `time`, `hash`,`username` FROM {$CONF['sql_prefix']}_votes  WHERE ip='{$_SERVER['REMOTE_ADDR']}' and time > '{$time}' AND vote=0 order by time desc LIMIT 1-->";
						if(!empty($vote)) 
							$res = $DB->query("INSERT INTO double_votes (id_vk, id_vote) VALUES ('{$_COOKIE['uid']}', '{$vote['id']}')", __FILE__, __LINE__);
										
						$time = time()+(3600*24);
						$referer = $_SERVER['HTTP_REFERER'];
						$res = $DB->query("INSERT INTO {$CONF['sql_prefix']}_votes (vk_id, username, ip, hash, time, referer) VALUES ('{$_COOKIE['uid']}', '{$TMPL['username']}', '{$_SERVER['REMOTE_ADDR']}', '$hash', '$time', '$referer')", __FILE__, __LINE__);
						setcookie('r_vote', $hash, time()+60*60*24*30,'/');
						setcookie('popup', time()+(3600*24), time()+60*60*24*30,'/');
						$this->form_set();
						
                    }
                }

                else
                    $this->form_not_vk();

            }

        }

    }

    function form_vote($project) {
        global $LNG, $TMPL;
		$TMPL['captcha'] = recaptcha_get_html($TMPL['publickey']);
        $TMPL = array_merge($TMPL, $project);
        $link = "<a href=\"/rating/project/{$TMPL['id']}\" onclick=\"out('{$TMPL['username']}');\">{$TMPL['title']}</a>";
        $TMPL['rate_message'] = sprintf($LNG['rate_message'], $link);
		$TMPL['awards_token'] = md5(time());
	
		session_start();
		$_SESSION['awards_token'] = $TMPL['awards_token'];
		
        $TMPL['content'] = $this->do_skin('rate_form_vote');
    }

    function form_wait($project) {
        global $CONF, $DB, $TMPL;

        $vote=$DB->fetch("SELECT `id`,`time`, `username`, `hash` FROM {$CONF['sql_prefix']}_votes WHERE vk_id='{$_COOKIE['uid']}' and vote = 0 order by time desc LIMIT 1",__FILE__, __LINE__);

        if(!empty($vote) and empty($_COOKIE['r_vote']))
            setcookie('r_vote', $vote['hash'], time()+60*60*24*30,'/');

        if(empty($vote))
            header("Location: {$_SERVER['HTTP_REFERER']}");

        if(isset($_COOKIE['r_vote']) and isset($_COOKIE['uid']) and $vote['time'] == '14400' or empty($vote['time']))
            header("Location: {$_SERVER['HTTP_REFERER']}");

        $TMPL['time_user'] = date('d/m/Y H:i', $vote['time']);
        $TMPL['project_title'] = $project['title'];
        $TMPL['project_id'] = $project['id'];
        $TMPL['content'] = $this->do_skin('rate_form_wait');
    }

    function form_another($vote, $project) {
        global $CONF, $DB, $TMPL;
        list($title)=$DB->fetch("SELECT `title` FROM {$CONF['sql_prefix']}_servers WHERE username='{$vote['username']}' LIMIT 1",__FILE__, __LINE__);
        $TMPL['username'] = $vote['username'];
        $TMPL['title'] = $title;
        $TMPL['vkid'] = intval($_COOKIE['uid']);
        $TMPL['project_new_username'] = $project['username'];
        $TMPL['project_new_title'] = $project['title'];
        $TMPL['content'] = $this->do_skin('rate_form_another');
    }

    function form_not_vk() {
        global $CONF, $DB, $FORM, $LNG, $TMPL;
        $TMPL['url'] = $_SERVER['REQUEST_URI'];
        $TMPL['username'] = $DB->escape($FORM['u'], 1);
        $username = $TMPL['username'];
        setcookie('r_page',"$username",time()+60*60*24*60,'/');

        list($title, $id)=$DB->fetch("SELECT `title`, `id` FROM {$CONF['sql_prefix']}_servers WHERE username='$username' LIMIT 1",__FILE__, __LINE__);
        $TMPL['title']=$title;
        $TMPL['id']=$id;
        $TMPL['content'] = $this->do_skin('rate_form_not_vk');
    }

    function form_set() {
        global $TMPL;
        $TMPL['content'] = $this->do_skin('rate_form_set');
    }
	
	function form_set_fake() {
        global $TMPL;
        $TMPL['content'] = $this->do_skin('rate_form_set_fake');
    }


    function process($hash, $resp) {
        global $CONF, $DB, $FORM, $TMPL;


		
        if (isset($FORM['review_check'])&&($FORM['review_check']==1)) {
            $date = date("Y-m-d H:i:s", time() + (3600*$CONF['time_offset']));
            list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_reviews", __FILE__, __LINE__);
            $review = strip_tags($FORM['review']);
            $review = nl2br($review);
            $review = $this->bad_words($review);
            $nickname = strip_tags($FORM['nickname']);
            $TMPL['review'] = $review;
            $review = $DB->escape($review);
            $DB->query("INSERT INTO {$CONF['sql_prefix']}_reviews (vk_id, username, id, date, review, active) VALUES ('{$_COOKIE['uid']}','{$TMPL['username']}', {$id}, '{$date}', '{$review}', {$CONF['active_default_review']})", __FILE__, __LINE__);
        }

        $vote=$DB->fetch("SELECT username,hash,vote FROM {$CONF['sql_prefix']}_votes WHERE hash='{$_COOKIE['r_vote']}' AND vk_id='{$_COOKIE['uid']}' and vote = 0 LIMIT 1",__FILE__, __LINE__);

        if($vote['vote']==0)
        {

            list($give_bonus, $secret_word, $script_url) = $DB->fetch("SELECT give_bonus, secret_word, script_url FROM rtt_servers where username = '{$TMPL['username']}'", __FILE__, __LINE__);
	
			if($resp) {
				
				$nickname = $FORM['nickname'];
			
				if (($give_bonus == 1) and (!empty($secret_word)) and (!empty($script_url))) {
					$hash = md5($secret_word.$nickname);
					$TMPL['content'] = $this->end_vote_proccess(1,'rate_finish',$TMPL['username'],$nickname,$hash,$script_url);
				}

				else{
					$TMPL['content'] = $this->end_vote_proccess(0,'rate_finish',$TMPL['username'],$nickname,$hash,0);
				}
			
			}



        }

        else
        {
            $TMPL['content'] = $this->do_skin('rate_finish_earlier');
            setcookie('r_vote','', 1,'/');
        }

    }
}
?>
