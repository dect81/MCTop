<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class delete_new extends base {
  function delete_new() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    if (isset($FORM['id'])) {
      if (is_array($FORM['id']) && count($FORM['id']) > 1) {
        $TMPL['title'] = sprintf($LNG['a_del_multi'], count($FORM['id']));
        $LNG['a_del_header'] = $LNG['a_del_headers'];
        $LNG['a_del_done'] = $LNG['a_del_dones'];
      }
      else {
        if (is_array($FORM['id']) && count($FORM['id']) == 1) {
          $TMPL['id'] = $DB->escape($FORM['id'][0]);
        }
        else {
          $TMPL['id'] = $DB->escape($FORM['id']);
        }
        list($TMPL['title']) = $DB->fetch("SELECT title FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
      }
      $TMPL['header'] = $LNG['a_del_header'];
    }
	echo $TMPL['title'];
    if (isset($TMPL['title']) && $TMPL['title']) {
      if (!isset($FORM['submit'])) {
        $this->warning();
      }
      else {
        $this->process();
      }
    }
    else {
      $this->error($LNG['g_invalid_u'], 'admin');
    }
  }

  function warning() {
    global $FORM, $LNG, $TMPL;

    $del_warn = sprintf($LNG['a_del_warn'], $TMPL['title']);

    $ids = '';

    if (is_array($FORM['id']) && count($FORM['id']) > 1) {
      foreach ($FORM['id'] as $id) {
        $ids .= "<input type=\"hidden\" name=\"u[]\" value=\"{$id}\" />\n";
      }
    }
    else {
        $ids .= "<input type=\"hidden\" name=\"u[]\" value=\"{$TMPL['id']}\" />\n";
    }

    $TMPL['admin_content'] = <<<EndHTML
{$del_warn}<br /><br />
<form action="{$TMPL['list_url']}/index.php?a=admin&amp;b=delete_new&id={$TMPL['id']}" method="post">
{$ids}<input type="submit" name="submit" value="{$LNG['a_del_header']}" />
</form>
EndHTML;
  }

  function process() {
    global $FORM, $LNG, $TMPL;

    if (is_array($FORM['id']) && count($FORM['id']) > 1) {
      foreach ($FORM['id'] as $id) {
        $this->do_delete($id);
      }
    }
    else {
      $this->do_delete($TMPL['id']);
    }

    $TMPL['admin_content'] = $LNG['a_del_done'];
  }

  function do_delete($id) {
    global $CONF, $DB;
	$query = "SELECT * from rtt_servers where id = '$id'";
	$result = mysql_query($query);
	$result = mysql_fetch_array($result);
	$EmailTo=$result['email'];

    $DB->query("DELETE FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$id}'", __FILE__, __LINE__);

  }
}
?>
