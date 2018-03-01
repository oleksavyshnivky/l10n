<?php
/**
 * Призначення: Клас для роботи з БД
*/

class db extends MySQLi {
	var $alive = false;
	//var $query_list = array();
	var $mysql_error = '';
	var $mysql_error_num = 0;
	var $result = false;

	var $query_count = 0;
	var $query_time = 0;
	
	var $dbuser = '';
	var $dbpass = '';
	var $dbname = '';
	var $dbhost = '';

	function __construct($params) {
		$this->dbuser = $params['dbuser'];
		$this->dbpass = $params['dbpass'];
		$this->dbname = $params['dbname'];
		$this->dbhost = $params['dbhost'];
	}

	// Під’єднання
	function dbconnect($show_error = true) {
		$db_location = explode(':', $this->dbhost);
		
		if (isset($db_location[1])) {
			$this->connect($db_location[0], $this->dbuser, $this->dbpass, $this->dbname, $db_location[1]);
		} else {
			$this->connect($db_location[0], $this->dbuser, $this->dbpass, $this->dbname);
		}

		if ($this->connect_errno) {
			printf(_('DB connect failed: %s') . "\n", $this->connect_error);
			exit();
		} else {
			$this->alive = true;
		}

		$this->set_charset('utf8');
		//$this->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
		return true;
	}
	
	function query($query, $show_error = true) {
		if (defined('SPORTPREFIX')) $query = str_replace('{SPORTPREFIX}', SPORTPREFIX, $query);
		if (defined('SPORT')) $query = str_replace('{SPORT}', SPORT, $query);

		$time_before = $this->get_real_time();

		if (!$this->alive) $this->dbconnect();
		if (!($this->result = parent::query($query))) {
			if ($show_error) {
				$this->display_error($query);
			}
		}
			
		$this->query_time += $this->get_real_time() - $time_before;
		$this->query_count++;
		//return $this->result;
	}

	function toarray($query, $multi = false, $col_id = false) {
		$this->query($query);
		if ($multi) {
			$result = array();
			while ($row = $this->result->fetch_assoc()) {
				if ($col_id) {
					$result[$row[$col_id]] = $row;
					// unset($result[$row[$col_id]][$col_id]);
				} else
					$result[] = $row;
			}
			return $result;
		} else {
			return $this->result->fetch_assoc();
		}
	}
	
	function escape($source)	{
		if (!$this->alive) $this->dbconnect();
		return $this->real_escape_string($source);
	}

	function dbclose() {
		if ($this->alive) {
			$this->close();
			$this->alive = false;
		}
	}

	function get_real_time() {
		list($seconds, $microSeconds) = explode(' ', microtime());
		return ((float)$seconds + (float)$microSeconds);
	}

	function get_row() {
		return $this->result->fetch_assoc();
	}

	function num_rows() {
		return $this->result->num_rows;
	}

	function free() {
		$this->result->free();
	}

	// Формування списку "поле = значення" для запитів вставки/оновлення
	function makeSetList($data, $keyfields = array()) {
		$fields = array();
		foreach($data as $key => $value) {
			if ($value !== null and !array_key_exists($key, $keyfields)) {
				if ($value === 'NULL') {
					$fields[] = "`{$key}` = NULL";
				} else {
					$value = $this->escape($value);
					$fields[] = "`{$key}` = '{$value}'";
				}
			}
		}
		return implode(', ', $fields);
	}

	// Формування списку умов для запитів оновлення/видалення
	function makeConditionList($keyfields) {
		$conditions = array();
		foreach ($keyfields as $key => $value) {
			if ($value !== null) {
				$value = $this->escape($value);
				$conditions[] = "`{$key}` = '{$value}'";
			}
		}
		return implode(' AND ', $conditions);
	}

	// Формування і виконання запиту вставки 
	// $odku — для оновлення запису у випадку дублювання ключа
	function insert($table, $data, $odku = false) {
		$fields = $this->makeSetList($data);
		if (!$fields) return false;

		if ($odku) 	$this->query("INSERT INTO `{$table}` SET {$fields} ON DUPLICATE KEY UPDATE {$fields}");
		else 		$this->query("INSERT INTO `{$table}` SET {$fields}");
	}

	// Формування і виконання запиту оновлення
	function update($table, $data, $keyfields) {
		if (!is_array($keyfields)) return false;

		$where_clause = $this->makeConditionList($keyfields);
		if (!$where_clause) return false;

		$fields = $this->makeSetList($data, $keyfields);
		if (!$fields) return false;

		$this->query("UPDATE `{$table}` SET {$fields} WHERE {$where_clause}");
	}

	// Формування і виконання запиту видалення
	function delete($table, $keyfields) {
		if (!is_array($keyfields)) return false;

		$where_clause = $this->makeConditionList($keyfields);
		if (!$where_clause) return false;

		$this->query("DELETE FROM `{$table}` WHERE {$where_clause}");
	}

	// Вставка кількох рядків через один Insert з екрануванням даних
	function insertMulti($url_beginning, $data) {
		$sql = $url_beginning;
		foreach ($data as $i => $tmp) {
			if ($i > 0) $sql .= ', ';

			for ($j = 0; $j < count($tmp); $j++) {
				$tmp[$j] = $this->escape($tmp[$j]);
			}
			
			$sql .= "('" . implode("', '", $tmp) . "')";
			$sql = str_replace("'NULL'", 'NULL', $sql);
		}
		$this->query($sql);
	}

	function display_error($query = '')	{
		$error = $this->error;
		$error_num = $this->errno;

		if($query) {
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
		}

		$query_plaintext = $query;
		$error_plaintext = $error;

		$query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
		$error = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');

		$trace = debug_backtrace();

		$level = 0;
		if ($trace[1]['function'] == 'query') $level = 1;
		if ($trace[2]['function'] == 'toArray') $level = 2;

		// if (isset($_SERVER['DOCUMENT_ROOT'])) $trace[$level]['file'] = substr($trace[$level]['file'], strlen($_SERVER['DOCUMENT_ROOT']));

		echo "MySQL error: {$error_num}: {$error}";
	
		if (file_exists('../app/log/dberror.log')) {
			$f = fopen('../app/log/dberror.log', 'a'); 
			$date = gmdate('D, d M Y H:i:s', time()).' GMT';
			$content = <<<TXT
---------------------------------------------------------------------
$date
File: {$trace[$level]['file']}
Line: {$trace[$level]['line']}
Error Number: {$error_num}

Error:
{$error_plaintext}

Query:
{$query_plaintext}
---------------------------------------------------------------------
TXT;
			fwrite($f, $content); 
			fclose($f);
		}
		exit();
	}
}
