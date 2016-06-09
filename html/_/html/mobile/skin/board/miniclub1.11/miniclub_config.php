<?
header("Content-Type: text/html; charset=utf-8");


//////////////////////////////////////////////////////////
/////////////// 미니클럽 1.0 기본설정 ////////////////
/////////////////////////////////////////////////////////

// 미니클럽 이용 가능 레벨은 몇부터?. 아래 줄에서 숫자를 수정하시면 됩니다. 기본은 2
$miniclub_allow_level = 2;

// 각 등급의 이름. 매니저~가입대기 부분을 수정하시면 돼요
$status_name = array("","매니저","부매니저","정회원","가입대기");

// 각등급별 색깔 1~4. cc0066에서 999999부분을 수정하시면 돼요
$status_color = array("","cc0066","99cc00","3366cc","999999");

// 여기까지만 수정하시면 됩니다. 아래 줄부터는 건드리시면 안돼요.







//--------------------------------------------------------
//       접근 기본 설정
//--------------------------------------------------------

// 클럽명도 없고 테이블명도 없다면
if(!$tablename and !$bo_table) { alert("잘못된 접근입니다"); }

// 테이블 네임 설정
if(!$tablename and $bo_table) { $tablename = $bo_table; }



//--------------------------------------------------------
//       클럽 기본정보 불러오기
//--------------------------------------------------------

// 게시판관리자 얻기
$miniclub = sql_fetch("select * from g5_board where bo_table = '$tablename' ");
$miniclub_admin = sql_fetch("select * from g5_miniclub_member where miniclub_table = '$tablename' and mb_id = '$miniclub[bo_admin]' ");

// 회원모집여부가 빈칸이라면 디폴트로 on
if(!$miniclub[bo_10] or ($miniclub[bo_10]!= 'on' and $miniclub[bo_10]!='off') ) sql_query("UPDATE g5_board SET bo_10 = 'on' where bo_table = '$tablename' "); 

// 게시판관리자의 닉네임 등 상세정보 얻어오기
$miniclub_admin_member = get_member($miniclub[bo_admin]);
$miniclub_admin_view = get_sideview($miniclub_admin_member[mb_id], $miniclub_admin_member[mb_nick], $miniclub_admin_member[mb_email], $miniclub_admin_member[mb_homepage],""); 


// 현재시각
$insert_time = date("Y-m-d H:i:s",time());



//--------------------------------------------------------
//       매니저 기본설정
//--------------------------------------------------------

// 게시판 관리자가 지정된 상태일때 클럽매니저 정보 자동설정
if($miniclub[bo_admin]) {

	// 매니저 가입이 안되어 있다면? 매니저 정보 삽입
	if(!$miniclub_admin[mb_id]) { 
		// sql_query("insert into g5_miniclub_member (no,miniclub_table,mb_id,status,join_date) values ('','$tablename','$miniclub[bo_admin]','1','$insert_time')");
		sql_query(
			"insert into g5_miniclub_member 
				set no = '',
				miniclub_table = '$tablename',
				mb_id = '$miniclub[bo_admin]',
				status = '1',
				join_date = '$insert_time'");

	}

	// 매니저 아이디는 있는데 매니저 등급은 아닐 경우 등업, 즉 게시판 관리자가 바뀐 경우
	if($miniclub_admin[mb_id] and $miniclub_admin[status] != '1') { 
		sql_query("UPDATE g5_miniclub_member SET status = '3' where miniclub_table = '$board[bo_table]' and status = '1' "); // 기존 관리자는 정회원으로
		sql_query("UPDATE g5_miniclub_member SET status = '1' where miniclub_table = '$board[bo_table]' and mb_id = '$miniclub_admin[mb_id]' "); // 현 관리자는 매니저로
		
	}	

	// 현재 게시판 관리자 외에도 매니저가 있을 경우 정회원으로 자동 강등
	sql_query("UPDATE g5_miniclub_member SET status = '3' where miniclub_table = '$board[bo_table]' and status = '1' and mb_id <> '$miniclub_admin[mb_id]'"); 

}

//--------------------------------------------------------
//       자신에 대한 정보 얻기
//--------------------------------------------------------

// 클럽에 대한 본인의 가입정보 얻어오기
$miniclub_user = sql_fetch("select * from g5_miniclub_member where miniclub_table = '$tablename' and mb_id = '$member[mb_id]' ");
$miniclub_user_now = $status_name[$miniclub_user[status]]; // 현재 클럽내 등급
if(!$miniclub_user[status]) $miniclub_user_now = "클럽 미가입";

// 그룹관리자인지 얻기
$group_info = sql_fetch("select * from g5_group where gr_id = '$miniclub[gr_id]' ");

// 본인 관리자 여부에 대한 변수 출력
if($is_admin or $miniclub[bo_admin]==$member[mb_id] or $group_info[gr_admin]==$member[mb_id] or ($miniclub_user[status] and $miniclub_user[status] < 3) ) {
	$miniclub_isadmin = 'yes';
}  else {
	$miniclub_isadmin = 'no';
}





?>

