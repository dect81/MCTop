<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class create_adm_news extends base {
  function create_adm_news() {
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
<form action="{$list_url}/admin/e/add_adm_news" method="post">
<fieldset>
<legend>Tweet админам</legend>

<label>Тема твита<br />
<input type="text" name="title" size="50" /><br /><br />
</label>
<label>В двух словах<br />
<textarea style="width: 550px;" rows="3" name="short_text"></textarea><br /><br /></label>
<label>Подробнее<br />
<textarea cols="90" rows="10" name="full_text"></textarea><br /><br />
<input name="submit" type="submit" value=". . . : Tweet : . . ." />
</fieldset>
</form>
EndHTML;
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    list($id) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_adm_news", __FILE__, __LINE__);
    $TMPL['id'] = $id+1;
    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['author'] = $DB->escape($FORM['author']);
    $TMPL['full_text'] = $DB->escape($FORM['full_text']);
    $TMPL['short_text'] = $DB->escape($FORM['short_text']);
	//active
	$TMPL['time'] = time()+3600;
	$query = "INSERT INTO {$CONF['sql_prefix']}_adm_news (id, title, short_text, full_text, active, time) VALUES ('{$TMPL['id']}', '{$TMPL['title']}', '{$TMPL['short_text']}', '{$TMPL['full_text']}', '1', '{$TMPL['time']}' )";
    $DB->query($query, __FILE__, __LINE__);
 
      $TMPL['admin_content'] = sprintf("Успешно твитнули, <a href=\"{$TMPL['list_url']}/cp/news/{$TMPL['id']}\">{$TMPL['list_url']}/cp/news/{$TMPL['id']}</a>");
    }
  }
?>
