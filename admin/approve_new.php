<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class approve_new extends base {
  function approve_new() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['a_approve_header'];

    if (!isset($FORM['id'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;

    $alt = '';
    $num = 0;
    $result = $DB->query("SELECT id, username, title FROM {$CONF['sql_prefix']}_servers_real WHERE active = 0 and main_serv = 0 ORDER BY username ASC", __FILE__, __LINE__);
    if ($DB->num_rows($result)) {
      $TMPL['admin_content'] = <<<EndHTML
<script language="javascript">
function check(form_name, field_name, value)
{
  var check_boxes = document.forms[form_name].elements[field_name];
  var num_check_boxes = check_boxes.length;

  if (!num_check_boxes)
  {
    check_boxes.checked = value;
  }
  else {
    for(var i = 0; i < num_check_boxes; i++)
    {
      check_boxes[i].checked = value;
    }
  }
}

var count = 0;
function popup(id)
{
  count = count + 1;
  elem = document.getElementById(id);
  elem.style.zIndex = count;
  if (elem.style.display == "none") { elem.style.display = "block"; }
  else { elem.style.display = "none"; }
}
</script>

<form action="{$TMPL['list_url']}/admin" method="post" name="approve">
<table border="1" cellpadding="0" cellspacing="0" width="100%" id="table_approve">
<tr class="mediumbg">
<td></td>
<td align="center" width="1%">{$LNG['g_username']}</td>
<td width="100%">${LNG['table_title']}</td>
<td align="center" colspan="5">{$LNG['a_man_actions']}</td>
</tr>
EndHTML;

      while (list($id, $username, $title) = $DB->fetch_array($result)) {
        $url_url = urlencode($url);
        $user_ip_url = urlencode($user_ip);
        $username_url = urlencode($username);
        $email_url = urlencode($email);

        $TMPL['admin_content'] .= <<<EndHTML
<tr class="lightbg{$alt}">
<td><input type="checkbox" name="u[]" value="{$username}" id="checkbox_{$num}" /></td>
<td align="center"><font color="black">$username</font></td>
<td width="100%"><a href="{$url}" onclick="out('{$username}');">{$title}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/u/approve_new/{$id}" title = "{$username}">{$LNG['a_approve']}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/u/edit_new/{$id}" title = "{$username}">{$LNG['a_man_edit']}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/u/delete_new/{$id}" title = "{$username}">{$LNG['a_man_delete']}</a></td>
</tr>
EndHTML;
        if ($alt) { $alt = ''; }
        else { $alt = 'alt'; }
        $num++;
      }

      $TMPL['admin_content'] .= <<<EndHTML
</table><br />
<a href="javascript:void;" onclick="check('approve', 'u[]', true)">{$LNG['a_man_all']}</a> | 
<a href="javascript:void;" onclick="check('approve', 'u[]', false)">{$LNG['a_man_none']}</a><br /><br />
{$LNG['a_approve_sel']}<br />
<select name="b">
<option value="approve">{$LNG['a_approve']}</option>
<option value="delete">{$LNG['a_man_delete']}</option>
</select>
<input type="submit" value="{$LNG['g_form_submit_short']}" />
</form>
EndHTML;
    }
    else {
      $TMPL['admin_content'] = $this->error($LNG['a_approve_none'], 'admin');
    }
  }

  function process() {
    global $DB, $FORM, $LNG, $TMPL;

    /*if (is_array($FORM['id']) && count($FORM['id']) > 1) {
      foreach ($FORM['id'] as $id) {
        $this->do_approve($DB->escape($id));
      }

      $LNG['a_approve_done'] = $LNG['a_approve_dones'];
    }
    else {
      if (is_array($FORM['id']) && count($FORM['id']) == 1) {
        $id = $DB->escape($FORM['id'][0]);
      }*/
     // else {
        $id = $DB->escape($FORM['id']);
      //}

      $this->do_approve($id);
    

    $TMPL['admin_content'] = $LNG['a_approve_done'];
  }

  function do_approve($id) {
    global $CONF, $DB, $LNG, $TMPL;

    $DB->query("UPDATE {$CONF['sql_prefix']}_servers_real SET active = 1 WHERE id = '{$id}'", __FILE__, __LINE__);


  }
}
?>
