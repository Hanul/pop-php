<?php
$contextPath = '../..';
include "$contextPath/lib/pop.php";
include "$contextPath/inc/mysql-config.php";

foreach ($mysql->get("SELECT * FROM user_info;") as $result) {
	echo $result->username;
	echo '<br>';
}

?>