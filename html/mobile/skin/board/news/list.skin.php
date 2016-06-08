<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;

if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

?>

<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css">

<div id="content">
	<section class="sec_press">
		<header class="top_area">
				<div class="top_img">
					<h1></h1>
				</div>
				<p class="crumb">
					<a href="/"><em>Home</em></a>
					<strong><?php echo $board['bo_subject'];?></strong>
				</p>
			</header>
		
		<section class="lst_press">
			<div class="tit_area">
				<h1><?php echo $board['bo_subject']?></h1>
				<div class="box_search">
				<form name="fsearch" method="get">
				<fieldset>
				<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
				<input type="hidden" name="sca" value="<?php echo $sca ?>">
				<input type="hidden" name="sop" value="and">
				<input type="hidden" name="sfl" value="wr_subject||wr_content">
				<input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required  >
				<button type="submit">검색</button>
				</fieldset>
				</form>

				</div>
			</div>

    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">


			<ul>
        <?php
        for ($i=0; $i<count($list); $i++) {

                        $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], 125, 150);

                        if($thumb['src']) {
                            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'">';
                        } else {
                            $img_content = '';
                        }

         ?>
			<li>
				 <dl>

				 <dd class="thmb"> <a href="<?php echo $list[$i]['href'] ?>"><?=$img_content?></a></dd>

				 <dt> <a href="<?php echo $list[$i]['href'] ?>"><?=$list[$i]['subject']?></a></dt>
				 <dd class="cite"><span><?=$list[$i]['wr_1']?></span><time><?=$list[$i]['wr_2']?></time></dd>
				 <dd class="smr"><a href="<?php echo $list[$i]['href'] ?>"><?=cut_str(strip_tags($list[$i]['wr_content']),200)?></a></dd>
				 </dl>
			</li>
		<?}?>
        <?php if (count($list) == 0) { echo '<li>게시물이 없습니다.</p></li>'; } ?>
			</ul>
			
			<?php echo $write_pages;  ?>

		</section>

    <?php if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="bo_fx">


        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php } ?>


	</form>
	</section>

</div>





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
<?php } ?>
<!-- } 게시판 목록 끝 -->
