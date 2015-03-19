<?php
/**
 * POP.php v0.1.0
 * http://code.google.com/p/pop-php/
 *
 * Copyright 2011, Mr. 하늘 (http://mr.hanul.co, mr@hanul.co)
 * Licensed under the GPL Version 3 licenses.
 * http://www.gnu.org/licenses/gpl.html
 *
 * Date: 오후 12:43 2011-08-28
 *
 * - Mysql config
 * $mysql->server = '[WRITE MYSQL SERVER URL]';
 * $mysql->username = '[WRITE MYSQL USERNAME]';
 * $mysql->password = '[WRITE MYSQL PASSWORD]';
 * $mysql->database = '[WRITE MYSQL DATABASE NAME]';
 * 
 * - v0.2.0에서
 * 쿠키 유틸리티를 추가할 예정
 * 인증 처리 유틸리티를 추가할 예정
 */

// ip
$ip = $_SERVER['REMOTE_ADDR'];

// 업데이트 과정인지 판단하는 변수 초기화
$isUpdate = false;

// 파라미터 매핑
foreach ($_REQUEST as $key => $data) {
	if ($key != "PHPSESSID") {
		if ($key == '_POP_IS_UPDATE_PARAMETER' && $data == 'true') {
			$isUpdate = true;
		} else {
			$param->$key = $data;
		}
	}
}

// isUpdate form input 출력하기
function isUpdate() {
	global $isUpdate;
	if ($isUpdate) {
		echo '<input type="hidden" name="_POP_IS_UPDATE_PARAMETER" value="true">';
	}
	echo "\n";
}

// Method 매핑
$method->get = $_SERVER['REQUEST_METHOD'] == 'GET';
$method->post = $_SERVER['REQUEST_METHOD'] == 'POST';

// redirect
function redirect($url) {
	header("Location: $url");
}

// pop lib's validate class
class POP_VALIDATE_CLASS {

	public $ok = true;
	public $no = false;
	public $msg;
	public $target;

	function target($target) {
		$this->target = $target;
		return $this;
	}

	// not null
	function notNull ($msg) {
		global $param;
		$this->validate($param->{$this->target} != null, $msg);
		return $this;
	}
	// size
	function size ($min, $max, $msg) {
		global $param;
		$len = mb_strlen($param->{$this->target}, "UTF-8");
		$this->validate($min <= $len && $len <= $max, $msg);
		return $this;
	}
	// range
	function range ($min, $max, $msg) {
		global $param;
		$this->validate(is_numeric($param->{$this->target}) && $min <= $param->{$this->target} && $param->{$this->target} <= $max, $msg);
		return $this;
	}
	// max
	function max ($max, $msg) {
		global $param;
		$this->validate(is_numeric($param->{$this->target}) && $param->{$this->target} <= $max, $msg);
		return $this;
	}
	// min
	function min ($min, $msg) {
		global $param;
		$this->validate(is_numeric($param->{$this->target}) && $min <= $param->{$this->target}, $msg);
		return $this;
	}
	// email
	function email ($msg) {
		global $param;
		$this->validate(ereg('^[a-z0-9_-]+[a-z0-9_.-]*@[a-z0-9_-]+[a-z0-9_.-]*\.[a-z]{2,5}$', $param->{$this->target}), $msg);
		return $this;
	}

	// validate
	function validate ($expression, $msg) {
		if (!$expression) {
			$this->ok = false;
			$this->no = true;
			if ($this->msg->{$this->target} == null) {
				$this->msg->{$this->target} = $msg;
			}
		}
		return $this;
	}
	// show message
	function msg ($s, $key, $e) {
		if ($this->msg->$key != null) {
			echo $s . $this->msg->$key . $e . "\n";
		}
	}
}
// validate object
$validate = new POP_VALIDATE_CLASS;

// pop lib's validate class
class POP_MYSQL_CLASS {

	public $server;
	public $username;
	public $password;
	public $database;

	// 결과를 배열로 가져옵니다.
	function get($query) {
		$conn = mysql_connect($this->server, $this->username, $this->password);
		mysql_select_db($this->database, $conn);

		$result = mysql_query($query, $conn);

		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			foreach ($row as $key => $data) {
				$return[$i]->$key = $data;
			}
			$i++;
		}

		mysql_close($conn);
		return $return;
	}

	// 결과를 하나만 가져옵니다.
	function one($query) {
		$conn = mysql_connect($this->server, $this->username, $this->password);
		mysql_select_db($this->database, $conn);

		$result = mysql_query($query, $conn);
		$row = mysql_fetch_array($result);

		foreach ($row as $key => $data) {
			$return->$key = $data;
		}

		mysql_close($conn);
		return $return;
	}

	// 결과를 가져오지 않습니다.
	function exe($query) {
		$conn = mysql_connect($this->server, $this->username, $this->password);
		mysql_select_db($this->database, $conn);
		mysql_query($query, $conn);
		mysql_close($conn);
	}
}
$mysql = new POP_MYSQL_CLASS;

?>