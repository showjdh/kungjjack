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
	<title>:: 미니클럽 관리::</title>

	<script>
	// 자바스크립트에서 사용하는 전역변수 선언
	var g5_url       = "<?php echo G5_URL ?>";
	var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
	var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
	var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
	var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
	var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
	var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
	var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
	var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
	<?php
	if ($is_admin) {
	    echo 'var g5_admin_url = "'.G5_ADMIN_URL.'";'.PHP_EOL;
	}
	?>
	</script>

	<script src="<?=G5_URL?>/js/jquery-1.8.3.min.js"></script>
	<script src="<?=G5_URL?>/js/jquery.menu.js"></script>
	<script src="<?=G5_URL?>/js/common.js"></script>
	<script src="<?=G5_URL?>/js/wrest.js"></script>

</head>



<body>

<?

//---------------- 기본 접근 설정 ------------------//

// 사이트 정회원 이상인가?
if($member[mb_level] < $miniclub_allow_level) {		?><script> alert("접근 권한이 없습니다.."); window.close();</script> <? exit;}

// 관리자급이 아닐 경우 닫는다.(슈퍼,그룹,게시판관리자)
if($miniclub_isadmin=='off') { ?><script> alert("이 페이지는 클럽 매니저 이상만 접근할 수있습니다."); window.close();</script> <? exit; }

// 테이블명 넘어오지 않았을 경우
if(!$tablename) { ?><script> alert("매개변수가 없습니다."); window.close();</script> <? exit;}

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);



//---------------- 리스트 시작 ------------------//

// 회원모집 여부가 파라미터로 넘어왔을 경우 업데이트
if($joincheck=="off") sql_query("UPDATE g5_board SET bo_10 = 'off' where bo_table = '$tablename' "); 
if($joincheck=="on") sql_query("UPDATE g5_board SET bo_10 = 'on' where bo_table = '$tablename' "); 

$miniclub_join = sql_fetch("select * from g5_board where bo_table = '$tablename' ");

// 회원모집/중지 스위치출력
if($miniclub_join[bo_10]=='off') {
	$miniclub_join = "중지 <a href='$_SERVER[PHP_SELF]?joincheck=on&tablename=$tablename'> [변경하기]</a>";
} else {
	$miniclub_join = "<font color=3366cc>진행 중</font> <a href='$_SERVER[PHP_SELF]?joincheck=off&tablename=$tablename'> [변경하기]</a>";
}






// 소스 시작
$sql = "select * from g5_miniclub_member where miniclub_table = '$tablename' order by no desc ";
$qry = sql_query($sql);
?>


<table width=100% border=0 class=club_table>
	<tr>
		<td height=60 colspan=3 align=center><font size=3><b><?=$miniclub[bo_subject]?></b></font></td>
	</tr>
	<tr height=50>
		<td width=250><b>게시판관리자</b> : <?=$miniclub_admin_member[mb_nick]?></td>
		<td width=250><b>신규회원모집</b> : <?=$miniclub_join?></td>
		<? if($subadmin[status]!='부관리자') { ?>
		<form name=invite action='miniclub_member_invite.php' method='post'>
		<input type=hidden name='tablename' value='<?=$tablename?>'>
		<td align=right><b>회원초대하기</b>(ID입력) <input name='invite_id'><input type='submit' value='초대'></td>
		</form>
		<? } ?>
	</tr>
</table>



<?

echo "<table width=800 cellspacing=1 cellpadding=0 border=0 bgcolor=eeeeee class=club_table>";
echo
"<tr bgcolor=cccccc height=29 align=center>
<td><b>순번</b></td>
<td><b>닉네임</b></td>
<td><b>이름</b></td>
<td><b>회원등록일</b></td>
<td><b>휴대폰</b></td>
<td><b>클럽가입신청</b>
</td><td><b>현재상태</b></td>
<td><b>처리</b></td>
</tr>";

$i = 1;
while ($row = sql_fetch_array($qry)) {
	$mb = get_member($row[mb_id]);

	if($row[status]=='1' or $row[mb_id] == $member[mb_id] ) { // 현재 줄의 멤버가 관리자라면? 자기 아이디라면?
		$member_process[0] = ""; $member_process[1] = ""; $member_process[2] = "";
	} else {
		$member_process[0] = "<a href='miniclub_member_modify.php?mode=up&tablename=$tablename&user=$row[mb_id]'>등업</a> ";
		$member_process[1] = "| <a href='miniclub_member_modify.php?mode=down&tablename=$tablename&user=$row[mb_id]' onclick='return confirm(\"정말 이 회원을 강등시키시겠습니까?\");'>강등</a>";
		$member_process[2] = "| <a href='miniclub_member_modify.php?mode=delete&tablename=$tablename&user=$row[mb_id]' onclick='return confirm(\"정말 이 회원을 삭제하겠습니까?\");'>삭제</a>";
	}

	

	// 회원상태에 따른 색상 변경
	$status_temp = $row[status];
	if($row[status] < 3) { $row[bold] = " style='font-weight:bold;'";	} else { $row[bold] = ""; }
	$row[status] = "<font color=".$status_color[$status_temp].$row[bold].">".$status_name[$row[status]]."</font>";
	
	//쪽지 보내기 위해 회원아이디 모아서 붙임
	if($i==1) { $memo_temp = ''; } else  { $memo_temp = ','; }
	$miniclub_memo_list = $miniclub_memo_list.$memo_temp.$mb[mb_id];
	$miniclub_mail_list = $miniclub_mail_list.$memo_temp.$mb[mb_email];

	// 회원상태바
	$mb_view[$i] = get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_email], $mb[mb_homepage],""); 

//	echo "<tr><td>$mb[mb_view]</td></tr>";

	echo "<tr bgcolor=ffffff height=29 align=center>
	<td>$i</td>
	<td>$mb_view[$i]</td>
	<td>$mb[mb_name]</td>
	<td>$mb[mb_datetime]</td>
	<td>$mb[mb_hp]</td>
	<td>$row[join_date]</td>
	<td>$row[status]</td>
	<td width=110>$member_process[0] $member_process[1] $member_process[2]</td>
	</tr>";

	$i++;
}

$member_all = $i - 1;
echo "</table>";



// 전체회원
echo "<div class=club_table>";
echo "<br><b>전체 :  $member_all"."명</b>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

// 정회원 수
$dbresult = sql_query( "select count(*) from g5_miniclub_member where status < 4 and miniclub_table = '$tablename'"); 
$member_rows = mysql_fetch_array( $dbresult ); 
echo "<b>정회원 : $member_rows[0]명</b>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

echo "<br><br><br><br><br><br>";

// 메모
// echo "클럽 회원에게 전체쪽지 보내기 : <a target='_blank' href=$g5[path]/bbs/memo_form.php?me_recv_mb_id=$miniclub_memo_list&clubmode=on>[바로가기]</a>";
echo "<a href='miniclub_group_memo.php?tablename=$tablename&mode=write' onclick=\"window.open(this.href,'',' scrollbars=yes,width=450, height=350'); return false\">[전체메모 발송]</a>&nbsp;&nbsp";
echo "<br><br>";

// 메일
echo "단체메일용 이메일목록 : <font color=cccccc>$miniclub_mail_list</font>";
echo "</div>";

?>



</body>