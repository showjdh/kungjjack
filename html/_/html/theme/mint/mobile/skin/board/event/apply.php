<?php
include_once('./_common.php');
$ewrite_table = "g5_write_estatus";

$ebo_board = "estatus";

$result = sql_query("SELECT COUNT(*) FROM $ewrite_table where wr_9 = '$wr_9' and mb_id = '$member[mb_id]'");
$count = mysql_result($result, 0, 0);
if($count != 0) alert("이미 신청하습니다.");

 $wr_num = get_next_num($ewrite_table);
   $sql = " insert into $ewrite_table
                set wr_num = '$wr_num',
                     wr_reply = '$wr_reply',
                     wr_comment = 0,
                     ca_name = '{$member['mb_tel']}',
                     wr_option = '$html,secret,$mail',
                     wr_subject = '$wr_subject',
                     wr_content = '{$member['mb_name']}',
                     wr_link1 = 0,
                     wr_link2 = '$wr_link2',
                     wr_link1_hit = 0,
                     wr_link2_hit = 0,
                     wr_hit = 0,
                     wr_good = 0,
                     wr_nogood = 0,
                     mb_id = '{$member['mb_id']}',
                     wr_homepage = '{$member['mb_id']}',
                     wr_password = '$wr_password',
                     wr_name = '{$member['mb_nick']}',
                     wr_email = '{$member['mb_email']}',
                     wr_datetime = '".G5_TIME_YMDHIS."',
                     wr_last = '".G5_TIME_YMDHIS."',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '예약정보' ";
    sql_query($sql);

    $wr_id = mysql_insert_id();

    // 부모 아이디에 UPDATE
    sql_query(" update $ewrite_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

    // 새글 INSERT
    sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$ebo_board}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

    // 게시글 1 증가
    sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$ebo_board}'");		

	goto_url(G5_HTTP_BBS_URL.'/board.php?bo_table=estatus');
    ?>