<?php

class to extends base {
  function to() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

		$id=$_GET['id'];
		if($id)
		{
		$row=$DB->fetch("SELECT `url` FROM {$CONF['sql_prefix']}_adv WHERE id='$id' LIMIT 1", __FILE__, __LINE__);
		if($row['url'])
		{
		$res=$DB->query("UPDATE {$CONF['sql_prefix']}_adv SET clicks=clicks+1 WHERE id='$id' LIMIT 1", __FILE__, __LINE__);
		$res=$DB->query("UPDATE {$CONF['sql_prefix']}_admin SET ads_clicks_today=ads_clicks_today+1 LIMIT 1", __FILE__, __LINE__);
		header("Location: ".$row['url']);
		}
		else
		{
		$TMPL['content'] = $this->do_skin('adv_error');
		}
		}
		else
		{
		$TMPL['content'] = $this->do_skin('adv_error');
		}

}
}
?>
