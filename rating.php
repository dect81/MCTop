<?php


if (!defined('ATSPHP')) {
    die("This file cannot be accessed directly.");
}


class rating extends base
{
    function rating()
    {
        global $CONF, $DB, $FORM, $LNG, $TMPL;
        $TMPL['page_name_title'] = "<g:plusone></g:plusone><center>
	 </h1>";
	 
	  //  $TMPL['page_name_title'] = "<g:plusone></g:plusone><center><br/>
	//	<a style='color: rgb(0, 0, 0);font-size:11px; font-weight:normal;background-color: rgba(255, 0, 0, 0.38);padding: 5px;' href='http://mctop.su/feed/post/20'>Нововведения за 16.05.13</a><br/><br/><br/>
	// </h1>";
	 
		/*$TMPL['page_name_title'] .= '
	<div class="VQ">
			<div class="Twitter">
				<a class="twitter-timeline"  href="https://twitter.com/0Medvedkoo"  data-widget-id="358035553419132928">Твиты пользователя @0Medvedkoo</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

			</div>
			
	</div>
';*/
		
        // Get the category, default to no category
        if (isset($FORM['cat']) && $FORM['cat']) {
            $TMPL['category'] = strip_tags($FORM['cat']);
            $category_escaped = $DB->escape($FORM['cat']);
            $category_sql = "AND category = '{$category_escaped}'";
        } else {
            $TMPL['category'] = $LNG['main_all'];
            $category_sql = '';
        }

        $TMPL['header'] = "{$TMPL['category']}";


        $order_by = "`score` DESC";

        if (isset($FORM['start'])) {
            $start = intval($FORM['start']);
            if ($start > 0) {
                $start--;
            }
        } else {
            $start = 0;
        }
        if ($start < 0) {
            die('hacking attempt');
        }
        ;
        $time = time();
        $lastlogin = $time - (24 * 60 * 60 * 7);
        $result = $DB->select_limit("SELECT *
                                 FROM {$CONF['sql_prefix']}_servers servers, {$CONF['sql_prefix']}_stats stats
                                 WHERE servers.id = stats.id AND active = 1 AND (servers.success/servers.attemps*100) > 50  {$category_sql}
                                 ORDER BY {$order_by}
                                ", $CONF['num_list'], $start, __FILE__, __LINE__);
// Select count
        $result2 = $DB->query("SELECT *
                                 FROM {$CONF['sql_prefix']}_servers servers, {$CONF['sql_prefix']}_stats stats
                                 WHERE servers.id = stats.id AND active = 1  AND (servers.success/servers.attemps*100) > 50{$category_sql}
                                 ORDER BY {$order_by}
                                ", $CONF['num_list'], $start, __FILE__, __LINE__);

// Pagination
        $numrows = mysql_num_rows($result2);
        if ($start < $CONF['num_list']) {
            $page = "0";
        } else {
            $page = $start / $CONF['num_list'];
        }
        $pagescount = ceil($numrows / $CONF['num_list']);

// Paginare
		$project_count = 0;
        $txt['nav'] = '';
        if ($pagescount > 1) {
            if (($page + 1) > 1) {
                $back = (($page - 1) * $CONF['num_list']) + 1;
                $txt['nav'] .= ' <a href="index.php?start=1';
                if (isset($FORM['method'])) {
                    $txt['nav'] .= "&amp;method={$ranking_method}";
                }
                if (isset($FORM['cat']) && strlen($FORM['cat']) != "0") {
                    $txt['nav'] .= "&amp;cat={$category_escaped}";
                }
                $txt['nav'] .= '" class="border2" title="Первая страница">&lt;&lt;</a> ';
                $txt['nav'] .= ' <a href="index.php?start=' . $back . '';
                if (isset($FORM['method'])) {
                    $txt['nav'] .= "&amp;method={$ranking_method}";
                }
                if (isset($FORM['cat']) && strlen($FORM['cat']) != "0") {
                    $txt['nav'] .= "&amp;cat={$category_escaped}";
                }
                $txt['nav'] .= '" class="border2" title="Назад">&lt;</a> ';
            }
            for ($page_number = 1; $page_number <= $pagescount; $page_number++) {
                $start_page = (($page_number - 1) * $CONF['num_list']) + 1;
                if (($page_number - 1) == $page) {
                    $txt['nav'] .= ' <a href="index.php?start=' . $start_page . '';
                    if (isset($FORM['method'])) {
                        $txt['nav'] .= "&amp;method={$ranking_method}";
                    }
                    if (isset($FORM['cat']) && strlen($FORM['cat']) != "0") {
                        $txt['nav'] .= "&amp;cat={$category_escaped}";
                    }
                    $txt['nav'] .= '" class="border3" title="Текущая страница - ' . $page_number . '"><span>' . $page_number . '</span></a> ';
                } else {
                    if ($page_number >= $page - 5 && $page_number <= $page + 5) {
                        $txt['nav'] .= ' <a href="index.php?start=' . $start_page . '';
                        if (isset($FORM['method'])) {
                            $txt['nav'] .= "&amp;method={$ranking_method}";
                        }
                        if (isset($FORM['cat']) && strlen($FORM['cat']) != "0") {
                            $txt['nav'] .= "&amp;cat={$category_escaped}";
                        }
                        $txt['nav'] .= '" class="border2" title="Страница ' . $page_number . '">' . $page_number . '</a> ';
                    }
                    /*else {
                        if ($page_number > $page && $dots_after != true) {
                            $txt['nav'] .= ' ...';
                            $dots_after = true;
                        } elseif ($page_number < $page && $dots_before != true) {
                            $txt['nav'] .= ' ...';
                            $dots_before = true;
                        }
                    }*/
                }
            }
            if (($page + 1) < $pagescount) {
                $next = (($page + 1) * $CONF['num_list']) + 1;
                $last = (($pagescount - 1) * $CONF['num_list']) + 1;
                $txt['nav'] .= ' <a href="index.php?start=' . $next . '';
                if (isset($FORM['method'])) {
                    $txt['nav'] .= "&amp;method={$ranking_method}";
                }
                if (isset($FORM['cat']) && strlen($FORM['cat']) != "0") {
                    $txt['nav'] .= "&amp;cat={$category_escaped}";
                }
                $txt['nav'] .= '" class="border2" title="Следующая страница">&gt;</a> ';
                $txt['nav'] .= ' <a href="index.php?start=' . $last . '';
                if (isset($FORM['method'])) {
                    $txt['nav'] .= "&amp;method={$ranking_method}";
                }
                if (isset($FORM['cat']) && strlen($FORM['cat']) != "0") {
                    $txt['nav'] .= "&amp;cat={$category_escaped}";
                }
                $txt['nav'] .= '" class="border2" title="Последняя страница">&gt;&gt;</a> ';
            }
        }

        $TMPL['pagination'] = $txt['nav'];
        if ($CONF['ranking_period'] == 'overall') {
            $ranking_period = 'daily';
        } else {
            $ranking_period = $CONF['ranking_period'];
        }
        if ($TMPL['category'] == $LNG['main_all']) {
            $is_main = 1;
        } else {
            $is_main = 0;
        }
        $TMPL['rank'] = $start + 1;
        $page_rank = 1;
        $top_done = 0;
        $do_table_open = 0;
        $TMPL['alt'] = 'alt';

        if ($DB->num_rows($result)) {
            // Start the output with table_top_open if we're on the first page
            $TMPL['content'] = $this->do_skin('table_open');

            // All this $TMPL_original stuff is a hack to avoid doing an array_merge
            // on large arrays with conflicting keys, because that is very slow
            $TMPL_original = $TMPL;


            $TMPL['content'] .= $this->show_ads();


            while ($row = $DB->fetch_array($result)) {
                $TMPL_original['content'] = $TMPL['content'];
                $TMPL_original['alt'] = $TMPL['alt'];
                $TMPL_original['rank'] = $TMPL['rank'];
                $TMPL = array_merge($TMPL_original, $row);
                $ip = $TMPL['serv_ip'];
                $id = $TMPL['id'];
                $port = $TMPL['serv_port'];
                if ($TMPL['id'] == 928) $TMPL['star'] = '<img src="http://mctop.su/images/star2.png"/>';
                //echo $TMPL['votes_month'];
                $servers_count = mysql_fetch_array(mysql_query("SELECT count(*) from rtt_servers_real where uid='{$id}' and active = 1"));
                $servers_count = $servers_count[0];
                $all_uptime = 0;
                $TMPL['all_uptime'] = 0;
                $return_set = array();
                $res = mysql_query("SELECT * from rtt_servers_real where uid='{$id}' and active = 1");
                while ($server = mysql_fetch_assoc($res))
                    array_push($return_set, $server);
                $TMPL['status'] = '';
                //echo $status_real;
                for ($i = 0; $i <= $servers_count - 1; $i++) {

                    $server_info = $return_set[$i];
                    $status = $server_info['status'];
                    $title = $server_info['title'];
                    $sid = $server_info['id'];
                    $attempts = $server_info['attempts'];
                    $success = $server_info['success'];
                    if ($servers_count > 2)
                        $des = 5;
                    else
                        $des = $servers_count;
                    if ($status == 1) {
                        $t = 'online';
                    } elseif ($status == 0) {
                        $t = 'offline';
                    } elseif ($status == 2) {
                        $t = 'unknown';
                    }
                    $TMPL['status'] .= "<span class='formInfo'><a href='{$TMPL['list_url']}/tooltip.php?&id={$sid}&link={$TMPL['list_url']}/rating/server/{$sid}' class='jTip' id='server_{$sid}' name='{$title}'><img src='{$TMPL['list_url']}/images/status/multi/status_{$t}_{$des}.png'/></a></span>";
                    if ($attempts <> 0) {
                        $uptime = $success / $attempts * 100 * 100;
                        $uptime = ((int)($uptime) / 100.0);
                    } else {
                        $uptime = 0;
                    }

                    $all_uptime = $all_uptime + $uptime;


                }
                if ($servers_count == 0) {
                    $all_uptime == 0;
                } else {
                    $all_uptime = ($all_uptime / $servers_count) * 100;
                    $all_uptime = ((int)($all_uptime) / 100.0);
                }
                $TMPL['all_uptime'] = $all_uptime;


#		if ($status == 1) {
#		$TMPL['status']="<img src='{$TMPL['list_url']}/status/1' border='0'/>";
#		} elseif ($status == 0){
#			$TMPL['status']="<img src='{$TMPL['list_url']}/status/0' border='0'/>";
#		}

 //$TMPL['banner'] = "<a href= '{$TMPL['list_url']}/rating/server/out/{$TMPL['id']}'><img  src='{$TMPL['banner_url']}'/></a>"; 
 $TMPL['banner'] = "<a href= '{$TMPL['list_url']}/rating/project/{$TMPL['id']}'><img  src='{$TMPL['banner_url']}'/></a>";


                $TMPL['category_url'] = urlencode($TMPL['category']);
                #-----------------------------------
                if ($TMPL['whitelist']) $TMPL['whitelist'] = 'Включен';
                else $TMPL['whitelist'] = '<font color="red">Отключен</font>';
                #-----------------------------------
                if ($TMPL['clienttype']) $TMPL['clienttype'] = 'Лицензия';
                else $TMPL['clienttype'] = '<font color="red">Пиратка</font>';
                #---------------------------------------

                $CONF['top_skin_num'] = 15;
				
				if($row['minecon']!=0){
					$TMPL['minecon'] = 'minecon minecon_'.$row['minecon'];
				} else {
					$TMPL['minecon'] = '';
					$TMPL['minecon_link'] = '';
				}
                // Only use _top skin on the first page
                if ($page_rank <= $CONF['top_skin_num'] && (!isset($FORM['start']) || $FORM['start'] <= 1)) {
                    /* if($TMPL['vip']==1)
                     {
                     $TMPL['content'] .= $this->do_skin('table_vip');
                     $is_top = 1;
                     }
                     elseif($TMPL['vip']==0)
                     {	*/
                    // $TMPL['status'] .= "<span class='formInfo'><a href='{$TMPL['list_url']}/tooltip.php?&id={$sid}&link={$TMPL['list_url']}/rating/server/{$sid}' class='jTip' id='server_{$sid}' name='{$title}'><img src='{$TMPL['list_url']}/images/status/multi/status_{$t}_{$des}.png'/></a></span>";	
                    $TMPL['content'] .= $this->do_skin('table_top_row');
                    $is_top = 1;
                    //}
                } else {
                    // This sees if $do_table_open had been set during the last loop.  If so,
                    // a new table_open is printed.  This keeps a table_open form being the
                    // last thing on the page when there is an ad break at the end.
                    if ($do_table_open) {
                        $TMPL['content'] .= $this->do_skin('table_open');
                        $do_table_open = 0;
                    }
                    /*if($TMPL['vip']==1)
                    {
                    $TMPL['content'] .= $this->do_skin('table_vip');
                    $top_done = 1;
                    $is_top = 0;		  
                    }
                    elseif($TMPL['vip']==0)
                    {*/
					if($TMPL['mctq_time']<>-1){				
						$TMPL['players'] = '<tr><p><td>Игроков:</td> <td><span>'.$TMPL['online'].' / ' .$TMPL['slots'].'</span></td></p></tr>';
					}
						
                    $TMPL['content'] .= $this->do_skin('table_row');
                    $top_done = 1;
                    $is_top = 0;
                    //}
                }

                if ($page_rank == $CONF['top_skin_num'] && $is_top) {
                    $TMPL['content'] .= $this->do_skin('table_top_close');
                    $do_table_open = 1;
                }


                $TMPL['rank']++;
                $page_rank++;
                $project_count++;
				if($project_count == 10 ){
				
					$TMPL['content'] .= '
                <div class="server-block  minecon minecon_1">
                    <div class="server-block-right-container">
                        <div class="server-block-right">
                            <div class="server-number">
                                <span>?</span>
                            </div>
                        </div>
                    </div>
                    <div class="server-title" ><a href="http://cp.flydev.net/aff.php?aff=390">Flyspring.ru</a></div>
                    <div class="server-banner">				
                      <br/> <a href="http://cp.flydev.net/aff.php?aff=390"><img src="http://mctop.su/images/banner.png"/></a><br/>
                    </div>
					<br/>
                    <div class="server-content">
					<table cellpadding="2">
                        <tr>
						<td>
						Ищешь место для игры, но находишь ничего подходящего для себя и друзей? У нас есть решение!
						<br/>Создай свой собственный сервер!<br/>
						Это абсолютно не сложно. Не стоит этого бояться! Сервера создаются лишь несколькими нажатиями мышки.<br/>
						Выбираешь свою версию, при необходимости плагины, и играешь с друзьями!
						</td>
						</tr>

				    </table>
					<br/><br/>
                    </div>
                </div>
';					
				
				
				}



            }


			
        }

        if ($CONF['fill_blank_rows'] && $page_rank < $CONF['num_list']) {
            if (!isset($TMPL['content'])) {
                $page_rank = 0;
                $TMPL['content'] = $this->do_skin('table_open');
            }
            if ((isset($do_table_top_close) && $do_table_top_close) || $do_table_open) {
                $TMPL['content'] .= $this->do_skin('table_open');
            }

            while ($page_rank < $CONF['num_list']) {
                $page_rank++;
                $TMPL['content'] .= $this->do_skin('table_filler');
                $TMPL['rank']++;
            }

            $TMPL['content'] .= $this->do_skin('table_close');
        } elseif (isset($do_table_close) && $do_table_close) {
            $TMPL['content'] .= $this->do_skin('table_close');
        }


        if (!isset($TMPL['content'])) {
            $TMPL['content'] = '';
        }
        $TMPL['content'] = $this->do_skin('table_wrapper');


    }

}

?>
