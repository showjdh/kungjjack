<?
header("Content-Type: text/html; charset=utf-8");
include "../../../common.php";
// if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

// 클럽 설정파일 불러오기
include_once "miniclub_config.php";
?>


<head>
	<meta http-equiv="Context-Type" context="text/html;charset=UTF-8" />
	<link rel='stylesheet' href='<?=G5_URL?>/css/default.css' type='text/css'>
	<link rel='stylesheet' href='<?=$board_skin_url?>miniclub/style.css' type='text/css'>
</head>



<body>
<?

/*	 ------------- 접근권한 등 체크 ----------------   */

// 회원 맞나? 접근권한이 있나?
if($member[mb_level] < $miniclub_allow_level) {		?><script> alert("접근 권한이 없습니다.."); window.close();</script> <? exit;}

// 테이블명 넘어오지 않았을 경우
if(!$tablename or !$user or !$mode) { ?><script> alert("매개변수가 없습니다."); window.close();</script> <? exit;}

// 관리자가 아닐 경우 닫는다.(슈퍼,그룹,게시판관리자)
if($miniclub_isadmin == 'no' and (!$miniclub_user[status] or $miniclub_user[status] > 2 )) { ?><script> alert("매니저 등급이 아닙니다."); window.close();</script> <? exit; }






/*	 ------------- 업데이트 시작 ----------------   */


// 바꿀 대상자의정보 불러오기
$moqry = sql_fetch("select * from g5_miniclub_member where miniclub_table = '$tablename' and mb_id = '$user' ");



// 모드에 따라 변경할 데이터를 정한다.
if($mode=='up') {
	$status_change = $moqry[status] - 1;
	if($status_change < 2) {	$status_change = 2; } // 매니저까지는 등업 불가
} elseif($mode=='down') {
	$status_change = $moqry[status] + 1;
	if($status_change > 4) {	$status_change = 4; } // 가입대기 이하는 없으니까
}



// 부관리자일 경우 일부 권한 제한
if($miniclub_user[status]==2) {

	if($mode=='up' and $status_change <= 2) {
		?><script> alert("권한이 없습니다."); history.back(-1);</script> <? exit;
	} 

	if($mode=='down' and $status_change > 4) {
		?><script> alert("권한이 없습니다."); history.back(-1);</script> <? exit;
	} 

	if($mode=='delete') {
		?><script> alert("권한이 없습니다."); history.back(-1);</script> <? exit;
	}

}


// 쿼리실행. 업데이트나 삭제
if($mode=='up' or $mode=='down') {
	sql_query("UPDATE g5_miniclub_member SET status = '$status_change' where mb_id = '$user' and miniclub_table = '$tablename' ");
} elseif($mode=='delete') {
	sql_query("DELETE from g5_miniclub_member where mb_id = '$user' and miniclub_table = '$tablename' ");
}



// 메모 발송하기 (등업일 때만)

	if($mode=='up') {

	$wr[mb_id]  = $user; // 받는사람

	if ($is_member){ // 보내는 사람
		$smember_id = $miniclub[bo_admin]; // 매니저
	} else{ 
		$smember_id = "admin"; //손님에게 코멘트 허용시 관리자 또는 테스트용 의 아이디를 입력할것(쪽지보기에서 유령? 때문에 ㅠㅠ) 
	} 
	
	//원글의 제목과 쪽지내용의 항목을 만들고 링크를 완성 
	$memo_content = get_text(stripslashes("-------------------------------\n클럽 안내메시지\n-------------------------------\n\n")); 

	$memo_content .= " $user 님은 $miniclub[bo_subject] 클럽의 $status_name[$status_change] (으)로 등업되셨습니다."; 

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
	}






?>

<script>
//	alert("정상적으로 변경 되었습니다.");
	location.href='miniclub_member_list.php?tablename=<?=$tablename?>';
</script>

