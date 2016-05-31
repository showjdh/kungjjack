<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

include_once($board_skin_path."/auction.lib.php");
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<h2 id="container_title"><?php echo $board['bo_subject'] ?><span class="sound_only"> 목록</span></h2>

<!-- 게시판 목록 시작 -->
<div id="bo_list" style="width:<?php echo $width; ?>">

    <!-- 게시판 카테고리 시작 { -->
    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board['bo_subject'] ?> 카테고리</h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
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
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
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
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">


<div id="tender_list_wrap">

<?php
$mod = $board[bo_gallery_cols];
for ($i=0; $i<count($list); $i++) {
    //if ($i && $i%$mod==0) echo "</tr><tr><td height=10 colspan=2></td></tr><tr>";
    
    auction_successful($list[$i][wr_id]);

    $info = get_info_auction($list[$i][wr_id], $list[$i]);

    switch ($info[status]) {
        case 0: // 진행중
            if (strtotime($info[start_datetime]) > G5_SERVER_TIME) {
                $status = "모집전입니다.";
                $class = "act_status_begin";
                break;
            }
        case 1: // 진행중
            $status = "참여가능 <span style='font-size:11px;'>(".number_format($info[tender_count])."명 참여)</span>";
            $class = "act_status_ok";
            break;
        case 2: // 낙찰
            $status = "모집종료";
            if ($info[mb_id]) {
                $mb = get_member($info[mb_id]);
                $status .= " ($mb[mb_nick])";
            }
            $class = "act_status_end";
            break;
        case 3: // 유찰
            $status = "유찰되었습니다.";
            $class = "act_status_no";
            break;
    }

$thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);

if($thumb['src']) {
    $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="98px" height="98px">';
} else {
    $img_content = '<span style="width:98px;height:98px">no image</span>';
}
                          
?>
    <div class="tender_list">
        <h6>
                <?
                $a1 = $a2 = "";    
                if ($list[$i]['link'][1]) {
                    $a1 = "<a href='{$list[$i][link_href][1]}' target=_blank>";
                    $a2 = "</a>";
                }
                echo "{$a1}<span class='act_subject_text'>제공 : {$info[product]}</span>{$a2}";
                ?>
        </h6> 
        <div style="float:left; width:120px;">
            <a href="<?=$list[$i][href]?>"><?=$img_content?></a>
        </div>
        <div style="float:left;">
            <ul class="tender_info_ul">
                <li><a href="<?=$list[$i][href]?>" style="text-decoration:none;"><span style="cursor:pointer;">장소 : <?=cut_str($info[company],15)?><? if ($list[$i][comment_cnt]) echo " <span style='font-family:Tahoma;font-size:10px;color:#EE5A00; font-weight:normal;'>{$list[$i][comment_cnt]}</span>"; ?></span></a></li>
               <!--<li>입찰 번호 : <?=number_format($info[tender_lower])?>~<?=number_format($info[tender_higher])?></li>-->
                <li>여행날짜 : <?=date("Y/m/d [H:i]",strtotime($info[travel_start_time]))?>~<?=date("Y/m/d [H:i]",strtotime($info[travel_end_time]))?></li>
                <li>모집기간 : <?=date("Y/m/d [H:i]",strtotime($info[start_datetime]))?>~<?=date("Y/m/d [H:i]",strtotime($info[end_datetime]))?></li>
                
                <li>모집상태 : <span class="<?=$class?>"><?=$status?></span></li>
            </ul>
        </div>
    </div>
<?php
    if (($i+1)%$mod!=0) {
    //echo "<hr class=\"tender_list_clear\"></hr>";\
    }
} // end for
?>

<?php
if (count($list) == 0) { 
    echo "<div>게시물이 없습니다.</div>"; 
} 
?>

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
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
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
    <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="frm_input required" size="15" maxlength="20">
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
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./board_list_update.php";
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
<?php } ?>

<!-- 게시판 목록 끝 -->
