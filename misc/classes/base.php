<?php

if($debug) {
	$file = __FILE__;
	debug::include_report($file);
}


class base {

  function error($message, $skin = 0) {
    global $TMPL;

    $TMPL['error'] = $message;
    if ($skin) {
      $TMPL["{$skin}_content"] = $this->do_skin('error');
      $TMPL['content'] = $this->do_skin($skin);
    }
    else {
      $TMPL['content'] = $this->do_skin('error');
    }

    $skin = new main_skin('wrapper');
    echo $skin->make();
    exit;
  }
  function clear_var($var)
  {
		$var = mysql_escape_string ($var);
		$var = htmlspecialchars ($var);
		return $var;
  }
	function end_vote_proccess($bonuses, $filename, $username, $nickname, $hash, $script_url)
	{
		global $DB,$CONF;
		
		session_start();
		if(!empty($_SESSION['awards_token'])) {
		
			if($_SESSION['awards_token'] == $_POST['awards_token']) {
						
				$month = date("n",time());
				if (!mysql_ping($DB->dbl)) {
					echo 'Произошла ошибка во время выдачи бонуса, перезагрузите страницу, нажав кнопку F5 на клавиатуре.';
					exit;
					die();
				}
				
				$two_week = time() - (3600 * 24 * 14);
				$votes_queue = mysql_query("SELECT count(*) FROM rtt_votes WHERE username='{$info['username']}' and time> '$two_week'");
				$votes_queue = mysql_result($votes_queue, 0);
				
				$votes_month = $DB->query("SELECT count(*) FROM {$CONF['sql_prefix']}_votes WHERE username = '{$username}' and vote = 1 and month = '{$month}'", __FILE__, __LINE__);
				$votes_all = $DB->query("SELECT count(*) FROM {$CONF['sql_prefix']}_votes WHERE username = '{$username}' and vote = 1 and time> 1357020000", __FILE__, __LINE__);

				$votes_month = mysql_fetch_assoc($votes_month);
				$votes_month =  $votes_month['count(*)'];

				$votes_all = mysql_fetch_assoc($votes_all);
				$votes_all =  $votes_all['count(*)'];

				$votes_month++;
				$score = (($votes_month*15)+($votes_all/4));
				
				$awards_given = mysql_fetch_assoc($DB->query("SELECT awards_given FROM {$CONF['sql_prefix']}_votes WHERE username = '{$username}' AND hash='{$_COOKIE['r_vote']}'", __FILE__, __LINE__));
				$awards_given = $awards_given['awards_given'];
				if($awards_given <1) {
				$DB->query("UPDATE {$CONF['sql_prefix']}_stats SET votes_queue='{$votes_queue}', votes_month='{$votes_month}', votes_all='{$votes_all}', score='{$score}' WHERE username = '{$username}'", __FILE__, __LINE__) or die(mysql_error());
				$DB->query("UPDATE {$CONF['sql_prefix']}_votes SET vote='1', mark='5', month = '{$month}', nickname ='{$nickname}', `awards_given`=`awards_given`+1 WHERE username = '{$username}' AND hash='{$_COOKIE['r_vote']}'", __FILE__, __LINE__) or die(mysql_error());
				//echo 'okey';
				setcookie('r_vote','', 0,'/');	
				if($bonuses==1) {
						$data = "{$script_url}?player={$nickname}&hash={$hash}";
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $data);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						$answer = curl_exec($ch);
						curl_close($ch);
						$skin = new skin($filename);
						return $skin->make();
					}
				
				else 
					{
						$skin = new skin($filename);
						return $skin->make();
					}
				}
				else {
					$skin = new skin($filename);
					return $skin->make();				
				}
				

			
			}
			
			
			else {
					$skin = new skin($filename);
					return $skin->make();
			}

				
				
			

		}
		
		else {
			$skin = new skin($filename);
			return $skin->make();	
		}


		
	}
	function do_skin_and_give_awards($filename, $nickname, $hash, $script_url) {
		$data = "{$script_url}?player={$nickname}&hash={$hash}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$answer = curl_exec($ch);
		curl_close($ch);
		$skin = new skin($filename);
		return $skin->make();
	}
	function do_skin($filename) {
		$skin = new skin($filename);
		return $skin->make();
	}
  function show_ads(){
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	$row = $DB->fetch ("SELECT * FROM {$CONF['sql_prefix']}_adv WHERE active = 1 AND `views`<`max_views` ORDER BY  `time` ASC, `max_views`-`views` DESC LIMIT 1", __FILE__, __LINE__);
    //сделать проверку - если совпадений нет - то ничего не выводим
	if (isset($row['title'])){
	$adv = array_merge($TMPL, $row);
	$form = <<<HTML
	<div id="s5_mr"  style="white-space: normal;">
<a href="/index.php?a=to&id={$adv['id']}" class="ad_box_new" style="" id="ad_box_ad_0" onmouseover="leftBlockOver('_ad_0')" onmouseout="leftBlockOut('_ad_0')" target="_blank">
<div id="ad_title" class="ad_title_new">{$adv['title']}</div>
<div class="ad_domain_new">{$adv['site']}</div>
<span>
  <div id="pr_image" style="position: relative;">
    <img src="{$adv['img_url']}" style="">
    <div id="ads_play_btn" style="display: none;"></div>
  </div>
</span>
<div id="ad_desc" class="ad_desc_new" style="">{$adv['descr']}</div>
</a>
</div>
<script>
function a()
{
    $.post("index.php?a=adv", {"id":"{$adv['id']}"} );
}
</script>
HTML;
//$res = $DB->query("UPDATE {$CONF['sql_prefix']}_adv SET views=views+1  WHERE id=".$row['id']." LIMIT 1", __FILE__, __LINE__);
/*$query = "UPDATE {$CONF['sql_prefix']}_adv SET views=views+1  WHERE id='{$adv['id']}' LIMIT 1";
$res = $DB->query($query, __FILE__, __LINE__);
$query = "UPDATE {$CONF['sql_prefix']}_admin SET ads_views_today=ads_views_today+1 LIMIT 1";
$res = $DB->query($query, __FILE__, __LINE__);*/
return $form;
}
	}
	function ruServers($number){
		static $servers=array(' серверов', ' сервер', ' сервера');
	 
		$numberLast=intval(substr(strval($number),-1,1));
		$numberPreLast=intval(substr(strval($number),-2,2));
	 
		if(($numberLast==0) or ((5<=$numberLast) and ($numberLast<=9)) or((11<=$numberPreLast) and ($numberPreLast<=19))){
			$type=0;
		}elseif(($numberLast==1) and ($numberPreLast!=11)){
			$type=1;
		}elseif((2<=$numberLast) and ($numberLast<=4)){
			$type=2;
		}
	 
		return $servers[$type];
	}	
	function module_status($username){
	//определение тестового профиля для отладки модулей
	if ($username == 'MCTop') return 1;
	else return 0;	
	}

	function write_log($module, $action, $username, $time, $ip){
		global $CONF, $DB;
		list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_logs", __FILE__, __LINE__);
		if (!$id) {
		$id = 1;
		}
		$query = "INSERT INTO {$CONF['sql_prefix']}_logs (id, module, action, username, time, ip) VALUES ('$id','$module', '$action', '$username', '$time', '$ip')";
		//echo $query;
		$DB->query($query, __FILE__, __LINE__);
	}

  function cp_news() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    // Get the category, default to no category
    if (isset($FORM['cat']) && $FORM['cat']) {
      $TMPL['category'] = strip_tags($FORM['cat']);
      $category_escaped = $DB->escape($FORM['cat']);
      $category_sql = "AND category = '{$category_escaped}'";
    }
    else {
      $TMPL['category'] = $LNG['news_all'];
      $category_sql = '';
    }

    $TMPL['header'] = "{$LNG['news_header']} - {$TMPL['category']}";


    // Make ORDER BY clause
    //$order_by = $this->rank_by($ranking_method)." DESC";
      $order_by = "`id` DESC";
    // Figure out what rows we want, and SELECT them
    if (isset($FORM['start'])) {
      $start = intval($FORM['start']);
      if ($start > 0) {
        $start--;
      }
    }
    else {
      $start = 0;
    }

    $result = $DB->select_limit("SELECT *
                                 FROM {$CONF['sql_prefix']}_adm_news 
                                 WHERE active = 1
                                 ORDER BY {$order_by}
                                ", $CONF['num_list'], $start, __FILE__, __LINE__);
 $result2 = $DB->query("SELECT *
                                 FROM {$CONF['sql_prefix']}_adm_news
                                 WHERE active = 1
                                 ORDER BY {$order_by}", __FILE__, __LINE__);
// Pagination
$numrows = mysql_num_rows($result2);
if ($start < $CONF['num_list']) {
$page = "0";
}
else {
$page = $start / $CONF['num_list'];
}
$pagescount = ceil($numrows/$CONF['num_list']);

// Paginare
$txt['nav'] = '';
if ($pagescount > 1) {
    if (($page + 1) > 1) {
$back = (($page - 1) * $CONF['num_list']) + 1;
$txt['nav'] .= ' <a href="feed/1';
                  if(isset($FORM['method'])) { $txt['nav'] .="&amp;method={$ranking_method}"; }
                  if(isset($FORM['cat']) && strlen($FORM['cat']) != "0" ) { $txt['nav'] .= "&amp;cat={$category_escaped}"; }
                  $txt['nav'] .= '" class="border2" title="Первая страница">&lt;&lt;</a> ';
$txt['nav'] .= ' <a href="'.$back.'';
                  if(isset($FORM['method'])) { $txt['nav'] .="&amp;method={$ranking_method}"; }
                  if(isset($FORM['cat']) && strlen($FORM['cat']) != "0" ) { $txt['nav'] .= "&amp;cat={$category_escaped}"; }
                  $txt['nav'] .= '" class="border2" title="Назад">&lt;</a> ';
}
    for ($page_number = 1; $page_number <= $pagescount; $page_number++) {
 $start_page = (($page_number - 1) * $CONF['num_list']) + 1;
        if (($page_number - 1) == $page) {
            $txt['nav'] .= ' <a href="'. $start_page .'';
                  if(isset($FORM['method'])) { $txt['nav'] .="&amp;method={$ranking_method}"; }
                  if(isset($FORM['cat']) && strlen($FORM['cat']) != "0" ) { $txt['nav'] .= "&amp;cat={$category_escaped}"; }
                  $txt['nav'] .= '" class="border3" title="Текущая страница - '. $page_number .'"><span>'. $page_number .'</span></a> ';
}
        else {
            if ($page_number >= $page - 8 && $page_number <= $page + 8) {
            $txt['nav'] .= ' <a href="'. $start_page .'';
                  if(isset($FORM['method'])) { $txt['nav'] .="&amp;method={$ranking_method}"; }
                  if(isset($FORM['cat']) && strlen($FORM['cat']) != "0" ) { $txt['nav'] .= "&amp;cat={$category_escaped}"; }
                  $txt['nav'] .= '" class="border2" title="Страница '. $page_number .'">'. $page_number .'</a> ';
}
            else {
                if ($page_number > $page && $dots_after != true) {
                    $txt['nav'] .= ' ...';
                    $dots_after = true;
                } elseif ($page_number < $page && $dots_before != true) {
                    $txt['nav'] .= ' ...';
                    $dots_before = true;
                }
            }
        }
    }
    if (($page + 1) < $pagescount) {
$next = (($page+1) * $CONF['num_list']) + 1;
$last = (($pagescount - 1) * $CONF['num_list']) + 1;
            $txt['nav'] .= ' <a href="'. $next .'';
                  if(isset($FORM['method'])) { $txt['nav'] .="&amp;method={$ranking_method}"; }
                  if(isset($FORM['cat']) && strlen($FORM['cat']) != "0" ) { $txt['nav'] .= "&amp;cat={$category_escaped}"; }
                  $txt['nav'] .= '" class="border2" title="Следующая страница">&gt;</a> ';
            $txt['nav'] .= ' <a href="'. $last .'';
                  if(isset($FORM['method'])) { $txt['nav'] .="&amp;method={$ranking_method}"; }
                  if(isset($FORM['cat']) && strlen($FORM['cat']) != "0" ) { $txt['nav'] .= "&amp;cat={$category_escaped}"; }
                  $txt['nav'] .= '" class="border2" title="Последняя страница">&gt;&gt;</a> ';
}
}

$TMPL['pagination'] = $txt['nav'];  
    if ($CONF['ranking_period'] == 'overall') {
      $ranking_period = 'daily';
    }
    else {
      $ranking_period = $CONF['ranking_period'];
    }
    if ($TMPL['category'] == $LNG['main_all']) {
      $is_main = 1;
    }
    else {
      $is_main = 0;
    }
    $TMPL['rank'] = $start + 1;
    $page_rank = 1;
    $top_done = 0;
    $do_table_open = 0;
    $TMPL['alt'] = 'alt';
//    $TMPL['content'] = $this->do_skin('page_banned_open');
    if ($DB->num_rows($result)) {
      // Start the output with table_top_open if we're on the first page


      // All this $TMPL_original stuff is a hack to avoid doing an array_merge
      // on large arrays with conflicting keys, because that is very slow
      $TMPL_original = $TMPL;
	 
      while ($row = $DB->fetch_array($result)) {

        $TMPL['id'] = $row['id'];	  
        $TMPL['title'] = $row['title'];
        $TMPL['views'] = $row['views'];
		$time = $row['time'];
		
		$time = date('Y-m-d H:i', $time);
        $TMPL['date'] = $time;



        // Only use _top skin on the first page
          //$TMPL
		  $TMPL['user_cp_content'] .= $this->do_skin('user_cp_news');

		  $is_top = 1;
            

        if ($page_rank == $CONF['top_skin_num'] && $is_top) {
          $TMPL['user_cp_content'] .= $this->do_skin('table_top_close');
          $do_table_open = 1;
        }


        $TMPL['rank']++;
        $page_rank++;
      }


	  
    }


#------------------------

	
	
  }    

  function bad_words($text) {
    global $CONF, $DB;

    $result = $DB->query("SELECT word, replacement, matching FROM {$CONF['sql_prefix']}_bad_words", __FILE__, __LINE__);
    while (list($word, $replacement, $matching) = $DB->fetch_array($result)) {
      if ($matching) { // Exact matching
        $word = preg_quote($word);
        $text = preg_replace("/\b{$word}\b/i", $replacement, $text);
      }
      else { // Global matching
        $word = preg_quote($word);
        $text = preg_replace("/{$word}/i", $replacement, $text);

      }
    }

    return $text;
  }
}

?>