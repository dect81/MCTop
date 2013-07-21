
<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class delete_review extends base {
  function delete_review() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    if (is_array($FORM['id']) && count($FORM['id']) > 1) {
      $LNG['a_del_rev_header'] = $LNG['a_del_rev_headers'];
      $LNG['a_del_rev_done'] = $LNG['a_del_rev_dones'];
      $LNG['a_del_rev_warn'] = $LNG['a_del_rev_warns'];
      $date = 1;
    }
    else {
      if (is_array($FORM['id']) && count($FORM['id']) == 1) {
        $TMPL['id'] = intval($FORM['id'][0]);
      }
      else {
        $TMPL['id'] = intval($FORM['id']);
      }
      list($date) = $DB->fetch("SELECT date FROM {$CONF['sql_prefix']}_reviews WHERE id = {$TMPL['id']}", __FILE__, __LINE__);
    }
    $TMPL['header'] = $LNG['a_del_rev_header'];

    if ($date) {
      if (!isset($FORM['submit'])) {
        $this->warning();
      }
      else {
        $this->process();
      }
    }
    else {
      $this->error($LNG['a_del_rev_invalid_id'], 'admin');
    }
  }

  function warning() {
    global $FORM, $LNG, $TMPL;

    $del_warn = $LNG['a_del_rev_warn'];

    $ids = '';

    if (is_array($FORM['id']) && count($FORM['id']) > 1) {
      foreach ($FORM['id'] as $id) {
        $ids .= "<input type=\"hidden\" name=\"id[]\" value=\"{$id}\" />\n";
      }
    }
    else {
        $ids .= "<input type=\"hidden\" name=\"id[]\" value=\"{$TMPL['id']}\" />\n";
    }

    $TMPL['admin_content'] = <<<EndHTML
{$del_warn}<br /><br />
<form action="index.php?a=admin&amp;b=delete_review" method="post">
{$ids}<input type="submit" name="submit" value="{$LNG['a_del_rev_header']}" />
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

    $TMPL['admin_content'] = $LNG['a_del_rev_done'];
  }

  function do_delete($id) {
    global $CONF, $DB;

    $DB->query("DELETE FROM {$CONF['sql_prefix']}_reviews WHERE id = {$id}", __FILE__, __LINE__);
  }
}
?>
