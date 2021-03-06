<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class approve extends base {
  function approve() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['a_approve_header'];

    if (!isset($FORM['u'])) {
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
    $result = $DB->query("SELECT id, username, url, title, email, user_ip, status FROM {$CONF['sql_prefix']}_servers WHERE active = 0 ORDER BY username ASC", __FILE__, __LINE__);
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
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="table_approve">
<tr class="mediumbg">
<td></td>
<td class="head" align="center" width="1%">{$LNG['g_username']}</td>
<td class="head"  width="100%">${LNG['table_title']}</td>
<td class="head"  align="center" colspan="5">{$LNG['a_man_actions']}</td>
</tr>
EndHTML;

      while (list($id, $username, $url, $title, $email, $user_ip) = $DB->fetch_array($result)) {
		$style = null;
		$status = $DB->fetch("SELECT status FROM {$CONF['sql_prefix']}_servers_real WHERE username = '{$username}'", __FILE__, __LINE__);
		if($status['status']==1) 
			$status = 'http://mctop.su/images/on.png';
		elseif($status['status']==0) {
			$style="server_offline";
			$status = 'http://mctop.su/images/off.png';
			}
		elseif($status['status']==2) 
			$status = 'http://mctop.su/images/status/check_1.gif';		
		
		$url_url = urlencode($url);
        $user_ip_url = urlencode($user_ip);
        $username_url = urlencode($username);
        $email_url = urlencode($email);

        $TMPL['admin_content'] .= <<<EndHTML
<tr class="lightbg{$alt} {$style}" >
<td><input type="checkbox" name="u[]" value="{$username}" id="checkbox_{$num}" /></td>
<td align="center" ><font><img style="border: 1px solid rgb(211, 211, 211);padding: 2px;border-radius: 5px;" src="{$status}" border="0" alt=""/><br/>$username</font></td>
<td width="100%"><a href="{$url}" onclick="out('{$username}');">{$title}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/u/approve/{$username}" title = "{$username}">{$LNG['a_approve']}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/u/edit/{$username}" title = "{$username}">{$LNG['a_man_edit']}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/u/delete/{$username}" title = "{$username}">{$LNG['a_man_delete']}</a></td>
<td align="center"><a href="mailto:{$email}" title = "{$username}">{$LNG['a_man_email']}</a></td>
<td align="center"><a href="javascript:void(0);" onclick="popup('ban_{$num}')">{$LNG['a_menu_manage_ban']}</a>
<div id="ban_{$num}" class="lightbg{$alt}" style="display: none; border: 1px solid #000; position: absolute; padding: 2px; text-align: left;">
<a href="{$TMPL['list_url']}/index.php?a=admin&amp;b=manage_ban&amp;string={$url_url}&amp;field=url&amp;matching=1">URL</a><br />
<a href="{$TMPL['list_url']}/index.php?a=admin&amp;b=manage_ban&amp;string={$user_ip_url}&amp;field=ip&amp;matching=1">User IP</a><br />
<a href="{$TMPL['list_url']}/index.php?a=admin&amp;b=manage_ban&amp;string={$username_url}&amp;field=username&amp;matching=1">Username</a><br />
<a href="{$TMPL['list_url']}/index.php?a=admin&amp;b=manage_ban&amp;string={$email_url}&amp;field=email&amp;matching=1">Email</a>
</div>
</td>
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

    if (is_array($FORM['u']) && count($FORM['u']) > 1) {
      foreach ($FORM['u'] as $username) {
        $this->do_approve($DB->escape($username));
      }

      $LNG['a_approve_done'] = $LNG['a_approve_dones'];
    }
    else {
      if (is_array($FORM['u']) && count($FORM['u']) == 1) {
        $username = $DB->escape($FORM['u'][0]);
      }
      else {
        $username = $DB->escape($FORM['u']);
      }

      $this->do_approve($username);
    }

    $TMPL['admin_content'] = $LNG['a_approve_done'];
  }

  function do_approve($username) {
    global $CONF, $DB, $LNG, $TMPL;

    $DB->query("UPDATE {$CONF['sql_prefix']}_servers SET active = 1 WHERE username = '{$username}'", __FILE__, __LINE__);
    $DB->query("UPDATE {$CONF['sql_prefix']}_servers_real SET active = 1 WHERE username = '{$username}'", __FILE__, __LINE__);

    list($TMPL['username'], $TMPL['url'], $TMPL['title'], $TMPL['description'], $TMPL['category'], $TMPL['banner_url'], $TMPL['email'], $TMPL['join_date']) = $DB->fetch("SELECT username, url, title, description, category, banner_url, email, join_date FROM {$CONF['sql_prefix']}_servers WHERE username = '{$username}'", __FILE__, __LINE__);

    if ($CONF['google_friendly_links']) {
      $TMPL['verbose_link'] = "";
    }
    else {
      $TMPL['verbose_link'] = "index.php?a=in&u={$TMPL['username']}";
    }
    $TMPL['link_code'] = $this->do_skin('link_code');

    $LNG['join_welcome'] = sprintf($LNG['join_welcome'], $TMPL['list_name']);

	  $join_email = new skin('join_email');
	  $join_email->send_email($TMPL['email']);
	  
  }
}
?>
