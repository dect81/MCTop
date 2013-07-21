<?php

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
$TMPL[page_name_title] = 'Новости';

class news extends base {
  function news() {
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
}
?>
