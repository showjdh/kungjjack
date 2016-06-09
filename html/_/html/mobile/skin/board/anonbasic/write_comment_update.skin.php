<?php

$anon_name = "익명".substr(md5($wr_name.date("Ymd")),0,7); // 작성자명 익명화

if ($w == 'c') {

    sql_query(" update {$write_table} set mb_id = '', wr_name = '{$anon_name}' where wr_id = '{$comment_id}' ");
    sql_query(" update {$g5['board_new_table']} set mb_id = '' where bo_table = '{$bo_table}' and wr_id = '{$comment_id}' ");


}  else if ($w == 'cu') {
    sql_query(" update {$write_table} set mb_id = '', wr_name = '{$anon_name}' where wr_id = '{$comment_id}' ");
}

?>
