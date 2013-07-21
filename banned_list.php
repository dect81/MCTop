<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class banned_list extends base {
  function banned_list() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    // Get the category, default to no category
    if (isset($FORM['cat']) && $FORM['cat']) {
      $TMPL['category'] = strip_tags($FORM['cat']);
      $category_escaped = $DB->escape($FORM['cat']);
      $category_sql = "AND category = '{$category_escaped}'";
    }
    else {
      $TMPL['category'] = $LNG['main_all'];
      $category_sql = '';
    }

    $TMPL['header'] = "{$LNG['main_header']} - {$TMPL['category']}";

    // Get the ranking method, default to pageviews
    //$ranking_method = isset($FORM['method']) ? $FORM['method'] : $CONF['ranking_method'];
    //if (($ranking_method != 'pv') && ($ranking_method != 'in') && ($ranking_method != 'out')) {
    //  $ranking_method = 'pv';
    //}

    // Make ORDER BY clause
    //$order_by = $this->rank_by($ranking_method)." DESC";
      $order_by = "`num_ratings` DESC";
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
                                 FROM {$CONF['sql_prefix']}_servers sites, {$CONF['sql_prefix']}_stats stats
                                 WHERE sites.username = stats.username AND active = 3
                                 ORDER BY {$order_by}
                                ", $CONF['num_list'], $start, __FILE__, __LINE__);


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
    $TMPL['content'] = $this->do_skin('page_banned_open');
    if ($DB->num_rows($result)) {
      // Start the output with table_top_open if we're on the first page


      // All this $TMPL_original stuff is a hack to avoid doing an array_merge
      // on large arrays with conflicting keys, because that is very slow
      $TMPL_original = $TMPL;

      while ($row = $DB->fetch_array($result)) {
        $TMPL_original['content'] = $TMPL['content'];
        $TMPL_original['alt'] = $TMPL['alt'];
        $TMPL_original['rank'] = $TMPL['rank'];
        $TMPL = array_merge($TMPL_original, $row);

        $TMPL['category_url'] = urlencode($TMPL['category']);

        $ranking_periods = array('daily', 'weekly', 'monthly');
        $ranking_methods = array('unq_pv', 'tot_pv', 'unq_in', 'tot_in', 'unq_out', 'tot_out');
        foreach ($ranking_periods as $ranking_period2) {
          foreach ($ranking_methods as $ranking_method2) {
            $TMPL["{$ranking_method2}_avg_{$ranking_period2}"] = 0;
            for ($i = 0; $i < 10; $i++) {
              $TMPL["{$ranking_method2}_avg_{$ranking_period2}"] = $TMPL["{$ranking_method2}_avg_{$ranking_period2}"] + $TMPL["{$ranking_method2}_{$i}_{$ranking_period2}"];
            }
            $TMPL["{$ranking_method2}_avg_{$ranking_period2}"] = $TMPL["{$ranking_method2}_avg_{$ranking_period2}"] / 10;
          }
        }

        $TMPL['this_period'] = $TMPL["unq_{$ranking_method}_0_{$ranking_period}"];
        $TMPL['average'] = 0;
        for ($i = 0; $i < 10; $i++) {
          $TMPL['average'] = $TMPL['average'] + $TMPL["unq_{$ranking_method}_{$i}_{$ranking_period}"];
        }
        $TMPL['average'] = $TMPL['average'] / 10;


        // Only use _top skin on the first page
           
		  $TMPL['content'] .= $this->do_skin('table_banned');
		  $is_top = 1;


        if ($page_rank == $CONF['top_skin_num'] && $is_top) {
          $TMPL['content'] .= $this->do_skin('table_top_close');
          $do_table_open = 1;
        }


        $TMPL['rank']++;
        $page_rank++;
      }

      // If an ad break is directly after the last row, then there is no need to close the table

    }


#------------------------

	
	
  }
}
?>
