<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class manage_news extends base {
  function manage_news() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['a_man_news_header'];

    $result = $DB->select_limit("SELECT id, title, short_text, views from rtt_news ORDER BY id ASC", 1, 0, __FILE__, __LINE__);
	$result = $DB->fetch_array($result);

      var_dump($result);

    $TMPL['admin_content'] .= <<<EndHTML
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
</script>

<form action="{$TMPL['list_url']}/admin/e/delete_page" method="post" name="manage">
<table class="darkbg" cellpadding="1" cellspacing="1" width="100%">
<tr class="mediumbg">
<td></td>
<td align="center" width="1%">{$LNG['a_man_rev_id']}</td>
<td width="100%">{$LNG['table_title']}</td>
<td align="center" colspan="2">{$LNG['a_man_actions']}</td>
</tr>
EndHTML;

    $alt = '';
    $num = 0;
    $result = $DB->select_limit("SELECT id, title, content FROM {$CONF['sql_prefix']}_custom_news WHERE id >= '{$start}' ORDER BY id ASC", $num_list, 0, __FILE__, __LINE__);
    while (list($id, $title, $content) = $DB->fetch_array($result)) {
      $TMPL['admin_content'] .= <<<EndHTML
<tr class="lightbg{$alt}">
<td><input type="checkbox" name="id[]" value="{$id}" id="checkbox_{$num}" /></td>
<td align="center">{$id}</td>
<td width="100%"><a href="{$TMPL['list_url']}/admin/e/edit_page/{$id}">{$title}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/{$id}">Show</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/e/edit_page/{$id}">{$LNG['a_man_edit']}</a></td>
<td align="center"><a href="{$TMPL['list_url']}/admin/e/delete_page/{$id}">{$LNG['a_man_delete']}</a></td>
</tr>
EndHTML;

      if ($alt) { $alt = ''; }
      else { $alt = 'alt'; }
      $num++;
    }

    $TMPL['admin_content'] .= <<<EndHTML
</table><br />
<a href="javascript:void;" onclick="check('manage', 'id[]', true)">{$LNG['a_man_all']}</a> | 
<a href="javascript:void;" onclick="check('manage', 'id[]', false)">{$LNG['a_man_none']}</a><br /><br />
<input type="submit" value="{$LNG['a_man_del_sel']}" />
</form>
EndHTML;

      $TMPL['content'] = 'test';
  }
}
?>
