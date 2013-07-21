<?php 
class adv extends base {
	
	function adv() {
		global $CONF, $DB, $FORM, $LNG, $TMPL;
			if(isset($_POST['id'])) {
				$id = intval($_POST['id']);
				$query = "UPDATE {$CONF['sql_prefix']}_adv SET views=views+1  WHERE id='$id' LIMIT 1";
				//$query = $this->clear_query($query);
				$res = $DB->query($query, __FILE__, __LINE__);
				$query = "UPDATE {$CONF['sql_prefix']}_admin SET ads_views_today=ads_views_today+1 LIMIT 1";
				//$query = $this->clear_query($query);
				$res = $DB->query($query, __FILE__, __LINE__);
				die('ok');
			}
	}
}