<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class edit_news extends base {
  function edit_news() {

    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['a_edit_page_header'];

    $id = $DB->escape($FORM['id']);
    list($TMPL['id']) = $DB->fetch("SELECT id FROM {$CONF['sql_prefix']}_news WHERE id = '{$id}'", __FILE__, __LINE__);
    if ($TMPL['id']) {
      if (!isset($FORM['submit'])) {
        $this->form();
      }
      else {
        $this->process();
      }
    }
    else {
      //$this->error($LNG['a_del_page_invalid_id'], 'admin');
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;

    list($TMPL['title'], $TMPL['content']) = $DB->fetch("SELECT title, content FROM {$CONF['sql_prefix']}_custom_pages WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);

$TMPL['admin_content'] = <<<EndHTML
<form action="{$list_url}/admin/e/edit_news.php" method="post">
<fieldset>
<legend>{$LNG['a_add_news_header']}</legend>

<label>{$LNG['g_title']}<br />
<input type="text" name="title" size="50" value="{$TMPL['title']}"/><br /><br />
</label>
<label>short_text<br />
<textarea style="width: 550px;" rows="3" value="{$TMPL['short_text']}"></textarea><br /><br /></label>
<label>full_text<br />
<textarea cols="90" rows="10" value="{$TMPL['full_text']}"></textarea><br /><br />
<input name="submit" type="submit" value="{$LNG['a_add_news_header']}" />
</fieldset>
</form>
EndHTML;
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['content'] = $DB->escape($FORM['content']);

    $DB->query("UPDATE {$CONF['sql_prefix']}_custom_pages SET title = '{$TMPL['title']}', content = '{$TMPL['content']}' WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
 
    $TMPL['admin_content'] = $LNG['a_edit_page_edited'];
  }
}
?>
