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





//-------------------- 가입 시작 --------------------//

// 이미 가입된게 맞다면?
if($miniclub_user[mb_id]) {
	?><script> alert("이미 가입되어 있습니다"); window.close();</script><? 

// 아니라면 작업 들어감
} else {

	// 현재시간 기록하며 인서트
	sql_query("insert into g5_miniclub_member (no,miniclub_table,mb_id,join_date) values ('','$tablename','$member[mb_id]','$insert_time')");




	//----- 메모발송

	$wr[mb_id]  = $miniclub[bo_admin]; // 받는사람

	if ($is_member){ // 보내는 사람
		$smember_id = $member[mb_id]; 
	} else{ 
		$smember_id = "admin"; //손님에게 코멘트 허용시 관리자 또는 테스트용 의 아이디를 입력할것(쪽지보기에서 유령? 때문에 ㅠㅠ) 
	} 
	
	//원글의 제목과 쪽지내용의 항목을 만들고 링크를 완성 
	$memo_content = get_text(stripslashes("-------------------------------\n클럽 회원가입신청 안내\n-------------------------------\n\n")); 

	$memo_content .= " $miniclub[bo_subject] 클럽에 {$member[mb_nick]}님이 새로 가입신청하셨습니다."; 

 //쪽지번호만들기 
      $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g5[memo_table] "); 
      $me_id = $tmp_row[max_me_id] + 1; 

 //쪽지 날리기 
      $sql = " insert into $g5[memo_table] 
			set me_id ='$me_id',
			me_recv_mb_id = '$wr[mb_id]',
			me_send_mb_id = '$smember_id', 
			me_send_datetime = '$insert_time', 
			me_memo = '$memo_content\n\n' "; 
      sql_query($sql); 

  //쪽지도착 알람넣기 
      $sql = " update $g5[member_table] 
            set mb_memo_call = '$smember_id' 
                where mb_id = '$wr[mb_id]' "; 
        sql_query($sql); 








// 완료 메시지
?><script> alert("정상적으로 가입신청 되었습니다. 관리자의 등업처리를 기다려 주시기 바랍니다."); parent.opener.location.reload();window.close();</script><?

}

?>