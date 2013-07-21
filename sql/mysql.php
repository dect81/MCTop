<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

$database = 'MySQL';

class sql_mysql {
  var $dbl;
  var $debug;
  var $num_queries;
  var $queries;

// kdas die('bb');
//var $user = "mctopusr";
//var $password = "WFeQGJ1Hr2Pr5Np3yl3B";
//var $database = "7U3OdRBYG2_mcbd";
// kdas end
  function connect ($host, $user, $password, $database, $debug = 0) {
    $this->dbl = mysql_connect($host, $user, $password)	;
	mysql_query("SET NAMES 'utf8';");
	mysql_query("SET CHARACTER SET 'utf8';");
	mysql_query("SET SESSION collation_connection = 'utf8_general_ci';");
    $db = mysql_select_db($database, $this->dbl);

    $this->num_queries = 0;
    $this->debug = $debug ? 1 : 0;
    $this->queries = array();

    return $db;
  }

  function query($query, $file, $line) {
    global $queries;

    if ($this->debug) { array_push($this->queries, $query); }

    $result = mysql_query($query) or $this->error($file, $line);
    $this->num_queries++;

    return $result;
  }

  // Executes a normal query and fetches the array in one line
  function fetch($query, $file, $line) {
    $result = $this->query($query, $file, $line);
    return $this->fetch_array($result);
  }

  function select_limit($query, $num, $offset, $file, $line) {
    if ($offset) { $limit = ' LIMIT '.$offset.','.$num; }
    else { $limit = ' LIMIT '.$num; }

    return $this->query($query.$limit, $file, $line);
  }

  function fetch_array($result) {
    return mysql_fetch_array($result);
  }

  function num_rows($result) {
    return mysql_num_rows($result);
  }

  function escape($value, $no_html = 0) {
    if (get_magic_quotes_gpc()) {
      $value = stripslashes($value);
    }
    $value = mysql_real_escape_string($value, $this->dbl);

    if ($no_html) {
      $value = strip_tags($value);
    }
    
    return $value;
  }

  function error($file, $line) {
   trigger_error("Database error in &quot;<b>{$file}</b>&quot; on line <b>{$line}</b><br /><br />\n" . @mysql_error($this->dbl), E_USER_ERROR);
	echo '<h2><center>Имеются некоторые проблемы. Скоро все исправим</center></h2>';
  }

  function close() {
    mysql_close($this->dbl);
  }

  // For backups
  function get_table($table, $data = 1) {
    $create_table = $this->fetch("SHOW CREATE TABLE {$table}", __FILE__, __LINE__);
    $create_table = $create_table['Create Table'] . ";\n\n";

    if ($data) {
      $result = $this->query("SELECT * FROM {$table}", __FILE__, __LINE__);

      $table_fields = '';
      $insert_into = '';
      $table_list = '';

      $num_fields = mysql_num_fields($result);
      for($i = 0; $i < $num_fields; $i++) {
        $table_fields .= ($i == 0 ? '' : ', ') . mysql_field_name($result, $i);
      }

      for($i = 0; $data = mysql_fetch_row($result); $i++) {
        $insert_into .= "INSERT INTO {$table} ({$table_fields}) VALUES (";

        for($j = 0; $j < $num_fields; $j++) {
          if($j != 0) { $insert_into .= ', '; }

          if(!isset($data[$j])) { $insert_into .= 'NULL'; }
          elseif(is_numeric($data[$j]) && (intval($data[$j]) == $data[$j])) { $insert_into .= intval($data[$j]); }
          elseif($data[$j] != '') { $insert_into .= "'" . $this->escape($data[$j]) . "'"; }
          else { $insert_into .= "''"; }
        }
        $insert_into .= ");\n";
      }
      $insert_into .= "\n\n";
    }
    else {
      $insert_into = '';
    }

    return $create_table . $insert_into;
  }
}
?>
