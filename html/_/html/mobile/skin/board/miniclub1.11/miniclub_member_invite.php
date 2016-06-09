<?
header("Content-Type: text/html; charset=utf-8");
include "../../../common.php";
// if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

// 클럽 설정파일 불러오기
include_once "miniclub_config.php";






// --------------- 기본 접근 설정 -----------------//

// 접근권한이 있나?
if($member[mb_level] < $miniclub_allow_level) {	?><script> alert("접근 권한이 없습니다.."); window.close();</script> <? }

// 관리자가 아닐 경우 닫는다.(슈퍼,그룹,게시판관리자)
if($miniclub_isadmin == 'no' ) { ?><script> alert("관리자가 아닙니다."); window.close();</script> <? exit; }

// 테이블명 넘어오지 않았을 경우
if(!$tablename) { ?><script> alert("매개변수가 없습니다."); window.close();</script><? }






// ---------------- 아이디 처리 -----------------//

// 넘어온 아이디에서 공백 제거
$invite_id = preg_replace("/\s+/","",$invite_id); // 공백제거

// 넘어온 아이디가 실제 그누보드 회원 맞나? 아니면 닫는다.
$temp = sql_fetch("select * from g5_member where mb_id = '$invite_id' ");
if(!$temp[mb_id]) { ?><script> alert("회원아이디가 잘못 입력되었습니다."); history.back(-1);</script> <? exit; }

// 해당 아이디의 클럽가입정보 불러오기
$invite_info = sql_fetch("select * from g5_miniclub_member where miniclub_table = '$tablename' and mb_id = '$invite_id' ");

// 이미 가입된게 맞다면?
if($invite_info[mb_id]) { 	?><script> alert("이미 가입된 회원입니다."); history.back(-1);</script><? exit; }

// 미가입 상태면 인서트
sql_query("insert into g5_miniclub_member (no,miniclub_table,mb_id,status,join_date) values ('','$tablename','$invite_id','3','$insert_time')");




// 메모발송

	$wr[mb_id]  = $invite_id; // 받는사람

	if ($is_member){ // 보내는 사람
		$smember_id = $miniclub[bo_admin]; 
	} else{ 
		$smember_id = "admin"; //손님에게 코멘트 허용시 관리자 또는 테스트용 의 아이디를 입력할것(쪽지보기에서 유령? 때문에 ㅠㅠ) 
	} 
	
	//원글의 제목과 쪽지내용의 항목을 만들고 링크를 완성 
	$memo_content = get_text(stripslashes("-------------------------------\n클럽회원 초대 안내\n-------------------------------\n\n")); 

	$memo_content .= " {$member[mb_nick]}님은 $miniclub[bo_subject] 클럽에 정회원으로 초대되셨습니다."; 

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




?>


<script>
	alert("정상적으로 초대 되었습니다.");
	location.href='miniclub_member_list.php?tablename=<?=$tablename?>';
</script>

