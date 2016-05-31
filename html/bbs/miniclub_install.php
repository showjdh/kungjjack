<?php
header("Content-type: text/html; charset=utf-8");
include_once ('../common.php');


// 처음 한번만 실행해 주시면 됩니다.

$install_result = sql_query("
CREATE TABLE IF NOT EXISTS `g5_miniclub_member` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `miniclub_table` varchar(20) NOT NULL,
  `mb_id` varchar(20) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT '4',
  `join_date` datetime NOT NULL,
  KEY `no` (`no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

echo "미니클럽 설치가 완료되었습니다!<br>";

?>