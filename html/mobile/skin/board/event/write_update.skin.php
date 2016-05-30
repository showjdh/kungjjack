<?
$wr_2 = $start_date."~".$end_date;
sql_query("update $write_table set wr_2 = '$wr_2' where wr_id = '$wr_id' ");
?>