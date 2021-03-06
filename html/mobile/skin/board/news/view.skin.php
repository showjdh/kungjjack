<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');


?>

<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css">
<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>


<div id="content">

    <!-- 게시물 상단 버튼 시작 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
         ?>
        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php if ($prev_href) { ?><li><a href="<?php echo $prev_href ?>" class="btn_b01">이전글</a></li><?php } ?>
            <?php if ($next_href) { ?><li><a href="<?php echo $next_href ?>" class="btn_b01">다음글</a></li><?php } ?>
        </ul>
        <?php } ?>

        <ul class="bo_v_com">
            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01">수정</a></li><?php } ?>
            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01" onclick="del(this.href); return false;">삭제</a></li><?php } ?>
            <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">복사</a></li><?php } ?>
            <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">이동</a></li><?php } ?>
            <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>" class="btn_b01">검색</a></li><?php } ?>
            <li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li>
            <?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b01">답변</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 게시물 상단 버튼 끝 -->


			<section class="sec_board">
		<header class="top_area">
				<div class="top_img">
					<h1></h1>
				</div>
				<p class="crumb">
					<a href="/"><em>Home</em></a>
					<strong><?php echo $board['bo_subject'];?></strong>
				</p>
			</header>
		
				
				<section class="sec_view">
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

<article class="atc_view">
						<header>
							<h1>
            <?php
            if ($category_name) echo $view['ca_name'].' | '; // 분류 출력 끝
            echo "제목 : ".cut_str(get_text($view['subject']), 70); // 글제목 출력
            ?>

							</h1>
							<dl class="info">
								<dt></dt>
								<dd class="file"></dd>
								<dt>작성일</dt>
								<dd><?php echo $view['wr_2']; ?></dd>
								<dt>작성자</dt>
								<dd><?=$view['wr_1']?></dd>
							</dl>
						</header>
						<div class="cnt">
    <?php
    if ($view['file']['count']) {
        $cnt = 0;
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                $cnt++;
        }
    }
     ?>

    <?php if($cnt) { ?>
    <!-- 첨부파일 시작 { -->
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <?php
        // 가변 파일
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
         ?>
            <li>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <img src="<?php echo $board_skin_url ?>/img/icon_file.gif" alt="첨부">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong>
                    <?php echo $view['file'][$i]['bf_content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                </a>
                <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?>회 다운로드</span>
                <span>DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    <?php } ?>




							<?php
							// 파일 출력
							$v_img_count = count($view['file']);
							if($v_img_count) {
								echo "<div id=\"bo_v_img\">\n";

								for ($i=0; $i<=count($view['file']); $i++) {
									if ($view['file'][$i]['view']) {
										//echo $view['file'][$i]['view'];
										echo get_view_thumbnail($view['file'][$i]['view']);
									}
								}

								echo "</div>\n";
							}
							 ?>

							<!-- 본문 내용 시작 { -->
							<div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
							<?php//echo $view[rich_content]; // {이미지:0} 과 같은 코드를 사용할 경우 ?>
							<!-- } 본문 내용 끝 -->

							<?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>


						</div>
					<?php
					include_once(G5_SNS_PATH."/view.sns.skin.php");
					?>
    <?php
    // 코멘트 입출력
    include_once('./view_comment.php');
     ?>

					</article>




    <!-- 링크 버튼 시작 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 링크 버튼 끝 -->
	
					
				</section>
			</section>
		</div>






<!-- 게시물 읽기 시작 { -->

<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                }
            }
        }, "json"
    );
}
</script>
<!-- } 게시글 읽기 끝 -->
