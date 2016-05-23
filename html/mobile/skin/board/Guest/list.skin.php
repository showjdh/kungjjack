<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 회원 레벨이 글쓰기 레벨 이상이면, 쓰기 페이지로 보낸다.
if ($member['mb_level'] >= $board['bo_write_level']) {
	if ( basename($_SERVER["PHP_SELF"]) == "board.php" ) {
		goto_url('./write.php?bo_table='.$bo_table."&page=".$page);
		exit();
	}
}

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;

if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

// 조회수 보여주느냐 마느냐 - "0" 이나 "" 이면 안보여주고, 그외의 값이면 보여준다.
$is_hit_view = "0";
if ($is_hit_view) $colspan--;

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>


<!-- 게시판 목록 시작 { -->
<div id="bo_list" style="width:<?php echo $width; ?>">

	<!-- 게시판 카테고리 시작 { -->
	<?php if ($is_category) { ?>
	<form name="fcategory" id="fcategory" method="get">
	<nav id="bo_cate">
		<h2><?php echo $board['bo_subject'] ?> 카테고리</h2>
		<ul id="bo_cate_ul">
			<?php echo $category_option ?>
		</ul>
	</nav>
	</form>
	<?php } ?>
	<!-- } 게시판 카테고리 끝 -->

	<!-- 게시판 페이지 정보 및 버튼 시작 { -->
	<div class="bo_fx">
		<div id="bo_list_total">
			<span>Total <?php echo number_format($total_count) ?>건</span>
			<?php echo $page ?> 페이지
		</div>

		<?php if ($rss_href || $write_href) { ?>
		<ul class="btn_bo_user">
			<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01">RSS</a></li><?php } ?>
			<?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin">관리자</a></li><?php } ?>
			<!-- <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?> -->
		</ul>
		<?php } ?>
	</div>
	<!-- } 게시판 페이지 정보 및 버튼 끝 -->

	<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="spt" value="<?php echo $spt ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">
	<input type="hidden" name="sw" value="">

	<div class="tbl_head01 tbl_wrap">
		<table>
		<caption><?php echo $board['bo_subject'] ?> 목록</caption>
		<thead>
		<tr>
			<th scope="col">번호</th>
			<?php if ($is_checkbox) { ?>
			<th scope="col">
				<label for="chkall" class="sound_only">현재 페이지 게시물 전체</label>
				<input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
			</th>
			<?php } ?>
			<th scope="col">글쓴이</th>
			<th scope="col"><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>날짜</a></th>
			<th scope="col">내용</th>
			<?php if ($is_hit_view) { ?><th scope="col"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>조회</a></th><?php } ?>
			<?php if ($is_good) { ?><th scope="col"><?php echo subject_sort_link('wr_good', $qstr2, 1) ?>추천</a></th><?php } ?>
			<?php if ($is_nogood) { ?><th scope="col"><?php echo subject_sort_link('wr_nogood', $qstr2, 1) ?>비추천</a></th><?php } ?>
		</tr>
		</thead>
		<tbody>
		<?php
		for ($i=0; $i<count($list); $i++) {
 // << 돼지코구뇽님의 문의글에서 발췌
	$wr_id= $list[$i][wr_id];

        $write = sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");
        $view = get_view($write, $board, $board_skin_path, 255);
        if (strstr($sfl, "subject"))
                $view[subject] = search_font($stx, $view[subject]);

        $html = 0;
        if (strstr($view[wr_option], "html1"))
                $html = 1;
        else if (strstr($view[wr_option], "html2"))
                $html = 2;

        $view[content] = conv_content($view[wr_content], $html);
        if (strstr($sfl, "content"))
                $view[content] = search_font($stx, $view[content]);
        $view[content] = preg_replace("/(\<img )([^\>]*)(\>)/i", "\\1 name='target_resize_image[]' onclick='image_window(this)' style='cursor:pointer;' \\2 \\3", $view[content]);

 
        $view[rich_content] = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "view_image(\$view, '\\1', '\\2')", $view[content]);   
// 돼지코구뇽님의 문의글에서 발췌 >>
		?>
		<tr class="<?php if ($list[$i]['is_notice']) echo "bo_notice"; ?><?php if ($board[1]) echo "bo_sideview"; ?>">
			<td class="td_num">
			<?php
			if ($list[$i]['is_notice']) // 공지사항
				echo '<strong>공지</strong>';
			else if ($wr_id == $list[$i]['wr_id'])
				echo $list[$i]['num'];
			?>
			</td>
			<?php if ($is_checkbox) { ?>
			<td class="td_chk">
				<label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
				<input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
			</td>
			<?php } ?>
			<td class="td_name sv_use"><?php echo $list[$i]['name'] ?></td>
			<td class="td_datetime"><?php echo $list[$i]['wr_datetime'] ?></td>
			<td class="td_subject">
				<?php
				echo $list[$i]['icon_reply'];
				if ($is_category && $list[$i]['ca_name']) {
				?>
				<a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
				<?php } ?>

				<?php if ($is_admin) { ?><a href="<?php echo $list[$i]['href'] ?>"><?php } ?>
					<?php echo $list[$i]['subject'] ?>
                    <?php if ($list[$i]['comment_cnt']) { ?><span class="sound_only">댓글</span><?php echo $list[$i]['comment_cnt']; ?><span class="sound_only">개</span><?php } ?>
                    <?php
				// if ($list[$i]['link']['count']) { echo '['.$list[$i]['link']['count']}.']'; }
				// if ($list[$i]['file']['count']) { echo '<'.$list[$i]['file']['count'].'>'; }
				// if (isset($list[$i]['icon_new'])) echo $list[$i]['icon_new'];
				// if (isset($list[$i]['icon_hot'])) echo $list[$i]['icon_hot'];
				// if (isset($list[$i]['icon_file'])) echo $list[$i]['icon_file'];
				// if (isset($list[$i]['icon_link'])) echo $list[$i]['icon_link'];
				// if (isset($list[$i]['icon_secret'])) echo $list[$i]['icon_secret'];

					?>
				</a>
                <br />
                    <!-- 비밀글 표시 및 내용추가 시작 -->
					<? 
					if ( strstr($list[$i][wr_option], "secret") && $is_guest ) {
					echo "<span class='small' style='color:#ff6600;'>비밀글 입니다</span>";
					}
					else  { ?>
                    <a onFocus="blur()" onClick="this.innerHTML=(this.nextSibling.style.display=='none')?'[닫기]':'[열기]';this.nextSibling.style.display=(this.nextSibling.style.display=='none')?'block':'none';" href=			"javascript:void(0)" ;>[열기]</a><div style="DISPLAY: none">
					<?php echo $list[$i]['content'] ?>
       				 </div>
        			<? } ?>
                    <table align="right" width="450px">
                    <tr><td>
<!-- 코멘트 리스트 돼지코구뇽님의 문의글에서 발췌-->
<?
$cosql = " select * from $write_table where wr_parent = '$wr_id' and wr_is_comment = 1 order by wr_comment, wr_comment_reply ";
$coresult = sql_query($cosql);


for ($ii=0; $corow=sql_fetch_array($coresult); $ii++)
{
    $colist[$ii] = $corow;


    $cotmp_name = get_text(cut_str($corow[wr_name], $config[cf_cut_name])); // 설정된 자리수 만큼만 이름 출력
    if ($board[bo_use_sideview])
        $colist[$ii][name] = get_sideview($corow[mb_id], $cotmp_name, $corow[wr_email], $corow[wr_homepage]);
    else
        $colist[$ii][name] = "<span class='".($corow[mb_id]?'member':'guest')."'>$cotmp_name</span>";






    // 공백없이 연속 입력한 문자 자르기 (way 보드 참고. way.co.kr)
    $c_secret_f= 1;
    $colist[$ii][content] = $colist[$ii][content1]= "Secret is";
    if (!strstr($corow[wr_option], "secret") ||
       $is_admin ||
       ($write[mb_id]==$member[mb_id] && $member[mb_id]) ||
       ($corow[mb_id]==$member[mb_id] && $member[mb_id])) {
	$c_secret_f= 0;
        $colist[$ii][content1] = $corow[wr_content];
        $colist[$ii][content] = conv_content($corow[wr_content], 0, 'wr_content');
        $colist[$ii][content] = search_font($stx, $colist[$ii][content]);
    }


    $colist[$ii][trackback] = url_auto_link($corow[wr_trackback]);
    $colist[$ii][datetime] = substr($corow[wr_datetime],0,16);


    // 관리자가 아니라면 중간 IP 주소를 감춘후 보여줍니다.
    $colist[$ii][ip] = $corow[wr_ip];
    if (!$is_admin)
        $colist[$ii][ip] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", "\\1.♡.\\3.\\4", $corow[wr_ip]);


    $colist[$ii][is_reply] = false;
    $colist[$ii][is_edit] = false;
    $colist[$ii][is_del]  = false;
    


	if ($is_comment_write || $is_admin)
    {
        if ($member[mb_id])
        {
            if ($corow[mb_id] == $member[mb_id] || $is_admin)
            {
                $colist[$ii][del_link]  = "./delete_comment.php?bo_table=$bo_table&comment_id=$corow[wr_id]&cwin=$cwin&page=$page".$qstr;
                $colist[$ii][is_edit]   = true;
                $colist[$ii][is_del]    = true;
            }
        }
        else
        {
            if (!$corow[mb_id]) {
                $colist[$ii][del_link] = "./password.php?w=x&bo_table=$bo_table&comment_id=$corow[wr_id]&cwin=$cwin&page=$page".$qstr;
                $colist[$ii][is_del]   = true;
            }
        }


        if (strlen($corow[wr_comment_reply]) < 5)
            $colist[$ii][is_reply] = true;
    }


    // 05.05.22
    // 답변있는 코멘트는 수정, 삭제 불가
    if ($ii > 0 && !$is_admin)
    {
        if ($corow[wr_comment_reply])
        {
            $tmp_comment_reply = substr($corow[wr_comment_reply], 0, strlen($corow[wr_comment_reply]) - 1);
            if ($tmp_comment_reply == $colist[$ii-1][wr_comment_reply])
            {
                $colist[$ii-1][is_edit] = false;
                $colist[$ii-1][is_del] = false;
            }
        }
    }




    $t_name= $colist[$ii][wr_name];
    $t_content = nl2br(strip_tags($colist[$ii][content]));
    $t_date= $colist[$ii][datetime];
    $t_id= $colist[$ii][wr_id];


    for ($tc= 0; $tc< strlen($colist[$ii][wr_comment_reply]); $tc++) echo "";


	if ($corow[mb_id] == $member[mb_id] || $is_admin)
    {
         $colist[$ii][del_link]  = "./delete_comment.php?bo_table=$bo_table&comment_id=$corow[wr_id]&cwin=$cwin&page=$page".$qstr;
         $colist[$ii][is_edit]   = true;
         $colist[$ii][is_del]    = true;


        echo "<span class='c_name'><NOBR>$t_name</NOBR></span>&nbsp;&nbsp;<span class='c_date'>$t_date</span>";
		echo "<div style='padding:8px 0 15px 0;'>$t_content</div>";
   } else { 
	    echo "<span class='c_name'><NOBR>$t_name</NOBR></span>&nbsp;&nbsp;<span class='c_date'>$t_date</span>";
        echo "<div style='padding:8px 0 15px 0;'>$t_content</div>";
   }
}
?>
<!-- 코멘트 리스트 끝 돼지코구뇽님의 문의글에서 발췌-->
                    </td></tr></table>
         <!-- 비밀글 표시 및 내용추가 끝 -->
			</td>
			<?php if ($is_hit_view) { ?><td class="td_num"><?php echo $list[$i]['wr_hit'] ?></td><?php } ?>
			<?php if ($is_good) { ?><td class="td_num"><?php echo $list[$i]['wr_good'] ?></td><?php } ?>
			<?php if ($is_nogood) { ?><td class="td_num"><?php echo $list[$i]['wr_nogood'] ?></td><?php } ?>
		</tr>
		<?php } ?>
		<?php if (count($list) == 0) { echo '<tr><td colspan="'.$colspan.'" class="empty_table">게시물이 없습니다.</td></tr>'; } ?>
		</tbody>
		</table>
	</div>

	<?php if ($list_href || $is_checkbox || $write_href) { ?>
	<div class="bo_fx">
		<?php if ($is_checkbox) { ?>
		<ul class="btn_bo_adm">
			<li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"></li>
			<li><input type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"></li>
			<li><input type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"></li>
		</ul>
		<?php } ?>

		<?php if ($list_href || $write_href) { ?>
		<ul class="btn_bo_user">
			<?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li><?php } ?>
			<!-- <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?> -->
		</ul>
		<?php } ?>
	</div>
	<?php } ?>
	</form>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<!-- 페이지 -->
<?php echo $write_pages;  ?>

<!-- 게시판 검색 시작 { -->
<fieldset id="bo_sch">
	<legend>게시물 검색</legend>

	<form name="fsearch" method="get">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sop" value="and">
	<label for="sfl" class="sound_only">검색대상</label>
	<select name="sfl" id="sfl">
		<option value="wr_subject"<?php echo get_selected($sfl, 'wr_subject', true); ?>>제목</option>
		<option value="wr_content"<?php echo get_selected($sfl, 'wr_content'); ?>>내용</option>
		<option value="wr_subject||wr_content"<?php echo get_selected($sfl, 'wr_subject||wr_content'); ?>>제목+내용</option>
		<option value="mb_id,1"<?php echo get_selected($sfl, 'mb_id,1'); ?>>회원아이디</option>
		<option value="mb_id,0"<?php echo get_selected($sfl, 'mb_id,0'); ?>>회원아이디(코)</option>
		<option value="wr_name,1"<?php echo get_selected($sfl, 'wr_name,1'); ?>>글쓴이</option>
		<option value="wr_name,0"<?php echo get_selected($sfl, 'wr_name,0'); ?>>글쓴이(코)</option>
	</select>
	<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required  class="frm_input required" size="15" maxlength="15">
	<input type="submit" value="검색" class="btn_submit">
	</form>
</fieldset>
<!-- } 게시판 검색 끝 -->
<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
	var f = document.fboardlist;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]")
			f.elements[i].checked = sw;
	}
}

function fboardlist_submit(f) {
	var chk_count = 0;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
			chk_count++;
	}

	if (!chk_count) {
		alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택복사") {
		select_copy("copy");
		return;
	}

	if(document.pressed == "선택이동") {
		select_copy("move");
		return;
	}

	if(document.pressed == "선택삭제") {
		if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다"))
			return false;
	}

	return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
	var f = document.fboardlist;

	if (sw == "copy")
		str = "복사";
	else
		str = "이동";

	var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

	f.sw.value = sw;
	f.target = "move";
	f.action = "./move.php";
	f.submit();
}
</script>
<script type="text/javascript">
function comment_delete(url)
{
    if (confirm("Are you sure to delete the comment?")) location.href = url;
}
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
