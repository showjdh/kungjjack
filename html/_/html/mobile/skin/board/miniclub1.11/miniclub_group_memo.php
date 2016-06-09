<?
header("Content-Type: text/html; charset=utf-8");
include "../../../common.php";
// if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

// 클럽 설정파일 불러오기
include_once "miniclub_config.php";





//-------------------- 기본 접근 설정 --------------------//


// 접근권한이 있나?
if($member[mb_level] < $miniclub_allow_level) {	?><script> alert("접근 권한이 없습니다.."); window.close();</script> <? }

// 테이블명 넘어오지 않았을 경우
if(!$tablename) { ?><script> alert("매개변수가 없습니다."); window.close();</script><? }

// 관리자급이 아닐 경우 닫는다.(슈퍼,그룹,게시판관리자)
if($miniclub_isadmin=='off') { ?><script> alert("이 페이지는 클럽 매니저 이상만 접근할 수있습니다."); window.close();</script> <? exit; }



//-------------------- 메모 작성 폼 --------------------//

if($mode == 'write') {


	echo "<form name='write' method='post' action='$_SERVER[PHP_SELF]'>";
	echo " - 미니클럽 전체메모 내용 작성 - <br><br>";
	echo "<textarea name='memo_message' cols='50' rows='15'></textarea>";
	echo "<input type=hidden name='mode' value='update'>";
	echo "<input type=hidden name='tablename' value='$tablename'><br>";
	echo "<input type='button' value ='완료' onclick = 'this.form.submit();'>";


//-------------------- 전체 메모 시작 --------------------//


} elseif ( $mode == 'update' ) {

	$query = "select * from g5_miniclub_member where miniclub_table = '$tablename' order by no desc ";
	$result = sql_query($query);
 
	echo "쪽지 발송 시작...<br>";

	for ($i=0; $row = sql_fetch_array($result); $i++) {

	$smember_id = $miniclub[bo_admin]; // 보내는 사람. 미니클럽 관리자.
	
	//원글의 제목과 쪽지내용의 항목을 만들고 링크를 완성 
	$memo_content = get_text(stripslashes("-------------------------------\n 미니클럽 전체 공지 \n from $miniclub[bo_subject] \n-------------------------------\n\n\n")); 
	$memo_content .= $memo_message;

	 //쪽지번호만들기 
      $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g5[memo_table] "); 
      $me_id = $tmp_row[max_me_id] + 1; 

	 //쪽지 날리기 
      $sql = " insert into $g5[memo_table] 
			set me_id ='$me_id',
			me_recv_mb_id = '$row[mb_id]',
			me_send_mb_id = '$smember_id', 
			me_send_datetime = '$insert_time', 
			me_memo = '$memo_content\n\n' "; 
      sql_query($sql); 

	  //쪽지도착 알람넣기 
      $sql = " update $g5[member_table] 
            set mb_memo_call = '$smember_id' 
                where mb_id = '$row[mb_id]' "; 
		sql_query($sql); 

	echo $row[mb_id]."<br>";		

	}
	echo "총 ".$i."명<br>";
	?> <script> alert("전체메모 발송 완료되었습니다."); window.close();</script> <?

} else {

	?> <script> alert("잘못된 접근입니다."); window.close();</script> <?

}

?>




