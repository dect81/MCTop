<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class create_news extends base {
  function create_news() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['a_add_news_header'];

    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $LNG, $TMPL;

    $TMPL['admin_content'] = <<<EndHTML
<form action="{$list_url}/admin/e/add_news" method="post">
<fieldset>
<legend>{$LNG['a_add_news_header']}</legend>

<label>{$LNG['g_title']}<br />
<input type="text" name="title" size="50" /><br /><br />
</label>
<label>Автор<br />
<input type="text" name="author" size="50" value="MCTop Team" /><br /><br />
</label>
<label>short_text<br />
<textarea style="width: 550px;" rows="3" name="short_text"></textarea><br /><br /></label>
<label>full_text<br />
<textarea cols="90" rows="10" name="full_text"></textarea><br /><br />
<input name="submit" type="submit" value="{$LNG['a_add_news_header']}" />
</fieldset>
</form>
EndHTML;
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    list($id) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_news", __FILE__, __LINE__);
    $TMPL['id'] = $id+1;
    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['author'] = $DB->escape($FORM['author']);
    $TMPL['full_text'] = $DB->escape($FORM['full_text']);
    $TMPL['short_text'] = $DB->escape($FORM['short_text']);
	//active
	$TMPL['date'] = date('Y-m-d', time() + (3600*$CONF['time_offset']));

      $DB->query("INSERT INTO {$CONF['sql_prefix']}_news (id, title, author, short_text, full_text, active, date) VALUES ('{$TMPL['id']}', '{$TMPL['title']}', '{$TMPL['author']}', '{$TMPL['short_text']}', '{$TMPL['full_text']}', '1', '{$TMPL['date']}' )", __FILE__, __LINE__);
 
      $TMPL['admin_content'] = sprintf($LNG['a_create_page_created'], "<a href=\"{$TMPL['list_url']}/feed/post/{$TMPL['id']}\">{$TMPL['list_url']}/feed/post/{$TMPL['id']}</a>");
    }
  }
?>
