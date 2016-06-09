<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

include_once("$board_skin_path/auction.lib.php");
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
add_javascript('<script src="'.G5_JS_URL.'/viewimageresize.js"></script>');

$info = get_info_auction($wr_id);

// 경매전 -> 시작시간이 지났을 때 -> 경매진행중
if ($info[status] == "0" && $info[start_datetime] <= G5_TIME_YMDHIS) {
    sql_query(" update $write_table set wr_8 = '1' where wr_id = '$wr_id' ");
    $info[status] = "1";
}

// 경매진행중 -> 종료시간이 지났을 때 -> 경매종료
if ($info[status] == "1" && $info[end_datetime] <= G5_TIME_YMDHIS) {
    $result = auction_successful($wr_id);
    if ($result[wr_8] > 1) {
        $info[tender_count] = $result[wr_7];
        $info[status] = $result[wr_8];
        $info[td_id] = $result[wr_9];
        $info[mb_id] = $result[wr_10];
    }
}

// 낙찰
if ($info[status] == "2") {
    $success_member = get_member($info[mb_id]);
}

$end_time = strtotime($info[end_datetime])-G5_SERVER_TIME;

if ($is_admin) {
    // 명수
    $sql = "select count( distinct mb_id ) as cnt from $tender_table where wr_id = '$wr_id' ";
    $row = sql_fetch($sql);

    $tender_mb_id_count = number_format($row[cnt]);


    // 최저로 입찰된 내역을 조회 (현재 1위)
    $row = sql_fetch(" select td_tender_point as point, count(td_tender_point) as cnt from $tender_table where wr_id = '$wr_id' group by td_tender_point order by cnt, td_tender_point limit 1 ");
    $super = array("point"=>$row[point], "count"=>$row[cnt]);

    $qry = sql_query(" select mb_id from $tender_table where td_tender_point = '$row[point]' and wr_id = '$wr_id' ");
    while ($row = sql_fetch_array($qry))
    {
        $super_mb_id[] = $row[mb_id];
    }
}

// 상품이미지 (중)
//print_r2($view[file]);


$file_path = G5_PATH."/data/file/".$bo_table."/".$view[file][1][file];

if (file_exists($file_path)) {
    $img = "{$view[file][1][path]}/{$view[file][1][file]}";
} else {
    $img = $board_skin_url."/img/noimage.gif";
}
?>


<!-- 게시글 보기 시작 -->
<article id="bo_v" style="width:<?php echo $width; ?>">

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

    <div id="view_tender_title">
        <div style="padding-top:2px; float:left;">
        <?php echo $info[company]; ?> 
        <?php if ($is_admin) echo "<span style=\"color:#888; font-size:11px; font-weight:normal;\">(hit : {$view[link_hit][1]})</span>"; ?>
        | 
        <span style="color:#000;"><?php echo $info[product]; ?></span>
        <?php if ($is_admin) echo "<span style=\"color:#888; font-size:11px; font-weight:normal;\">(hit : {$view[link_hit][2]})</span>"; ?>
        </div>

        <div style="float:right; font-weight:normal; color:#888; font-size:11px; margin-right:10px;">
        hit : <?php echo $view[wr_hit]; ?>
        </div>
    </div>


    <!-- 왼쪽 이미지 -->
    <div id="view_left_img">
        <img src="<?php echo $img?>" width=270 height=228>
    </div>
    <!-- 오른쪽 경매 정보 -->
    <div id="view_right_info">
        <div id="view_right_title">
            <div style="margin-left:10px; margin-top:5px; font-size:15px;">
            <?php if ($view[link][1]) { ?><a href="<?php echo $view[link_href][1]?>" target=_blank style="text-decoration:none;"><?php } ?>
                제목 : <?php echo $info[company]?> 
            <?php if ($view[link][1]) { ?></a><?php } ?>
            </div>
            <div style="margin-left:10px; font-size:12px;">
                <?php if ($view[link][2]) { ?><a href="<?php echo $view[link_href][2]?>" target=_blank style="text-decoration:none;"><?php } ?>
                장소 : <?php echo $info[product]; ?>
                <?php if ($view[link][2]) { ?></a><?php } ?>
            </div>
        </div>
        <table border=0 cellpadding=0 cellspacing=0 width=100% style="margin:18px 0 0 5px;">
            <!--
            <tr>
                <td height=20 style="padding-left:5px;" width=100> 경매시작일시 </td>
                <td style="color:#898989; font-weight:bold;"> <span class=colon>:</span> <?php echo date("Y년 m월 d일 H시 i분", strtotime($info[start_datetime]))?> </td>
            </tr>
            -->
            <tr>
                <td height=20 style="padding-left:5px;"> 모집마감 </td>
                <td style="color:#898989; font-weight:bold;"> <span class=colon>:</span> <?php echo date("Y년 m월 d일 H시 i분", strtotime($info[end_datetime]))?> </td>
            </tr>
            <tr>
                <td height=20 style="padding-left:5px;"> 여행기간 </td>
                <td style="color:#898989; font-weight:bold;"> <span class=colon>:</span> <?php echo date("Y/m/d", strtotime($info[travel_start_time]))?>~<?php echo date("Y/m/d", strtotime($info[travel_end_time]))?> </td>
            </tr>

            
            <?php if ($info[status] == 1) { ?>
            <tr>
                <td height=20 style="padding-left:5px;"> 남은시간 </td>
                <td> <span class=colon>:</span> <span id=end_timer></span>  </td>
            </tr>
            <?php } ?>
            <tr><td height=5 bgcolor="#ffffff" colspan=2></td></tr>
            <tr><td height=1 bgcolor="#dddddd" colspan=2></td></tr>
            <tr><td height=5 bgcolor="#ffffff" colspan=2></td></tr>
            <!--<tr>
                <td height=20 style="padding-left:5px;"> 입찰 번호 </td>
                <td> 
                    <span class=colon>:</span> 
                    <span style="color:#FF2E6E; font-weight:bold;"><?php echo number_format($info[tender_lower])?> ~ <?php echo number_format($info[tender_higher])?></span>
                </td>
            </tr>-->
            <tr>
                <td height=20 style="padding-left:5px;"> 참여자수 </td>
                <td>
                    <span class=colon>:</span> 
                    <span style="color:#3A72A9; font-weight:bold;">
                        <?php if ($is_admin) echo "$tender_mb_id_count 명 "; ?>
                        <!--<?php echo number_format($info[tender_count])?> 회 참여-->
                    </span>
                </td>
            </tr>
            <!--
            <tr>
                <td height=20 style="padding-left:5px;"> 배송비 </td>
                <td>
                    <span class=colon>:</span> 
                    <span style="color:#3A72A9; font-weight:bold;">
                        <?php echo $write[ca_name]?>
                    </span>
                </td>
            </tr>
            -->
            <tr>
                <td height=20 style="padding-left:5px;"> 올린이 </td>
                <td>
                    <span class=colon>:</span> 
                    <span style="color:#3A72A9; font-weight:bold;">
                        <?php echo $view[name]?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td height=20 style="padding-left:5px;"> 모집상태 </td>
                <td> 
                    <?php if ($info[status] == 3) { ?>
                    <span class=colon>:</span> <span style="color:#888; font-weight:bold;">모집이 마감되었습니다.</span>
                    <?php } elseif ($info[status] == 2) { ?>
                    <span class=colon>:</span> <span style="color:#950000; font-weight:bold;">모집이 마감되었습니다.</span>
                    <?php } elseif ($info[status] == 1) { ?>
                    <span class=colon>:</span> <span style="color:#009520; font-weight:bold;">참여가능</span>
                    <?php } else { ?>
                    <span class=colon>:</span> <span style="color:#888; font-weight:bold;">모집전입니다.</span>
                    <?php } ?>
                </td>
            </tr>
            <tr><td height=5 bgcolor="#ffffff" colspan=2></td></tr>
            <tr><td height=1 bgcolor="#dddddd" colspan=2></td></tr>
            <tr><td height=5 bgcolor="#ffffff" colspan=2></td></tr>
        </table>
        <!--
        <div style="color:#000; line-height:20px; font-family:dotum;">
            <div style="margin-left:5px;">* 입찰 참여 시 <b>참여포인트 <?php echo number_format($info[inter_point])?>점</b>이 차감됩니다.</div>
            <div style="margin-left:5px;">* 본 상품은 <b>한 회원이 하루 최대 <?php echo $info[day_limit]?>번</b> 입찰 가능합니다.</div>
        </div>
        -->
    </div>

    <!-- 입찰 부분 시작 -->
    <div style="clear:both; height:54px; background:url(<?php echo $board_skin_url?>/img/tender_bg.gif);">

    <?php if ($info[status] == '0') { ?>
        <div style="float:left; margin-left:100px; padding-top:20px; font-weight:bold;">
            모집은 <u><?php echo date("Y년 m월 d일 H시 i분", strtotime($info[start_datetime]))?></u> 에 시작됩니다.
        </div>
        <?php if ($view[link][2]) { ?><div style="float:left; margin:8px 0 0 20px;"><a href="<?php echo $view[link_href][2]?>" target=_blank><img src="<?php echo $board_skin_url?>/img/btn_buy.gif"></a></div><?php } ?>

    <?php } elseif ($info[status] == '2') { ?>

        <div style="height:54px; background:url(<?php echo $board_skin_url?>/img/tender_result_bg.gif) no-repeat;">
            <?php if ($info[mb_id]) { ?>
                <div style="position:absolute; margin:15px 0 0 100px; line-height:25px;"><?php echo get_sideview($success_member[mb_id], $success_member[mb_nick], $success_member[mb_email], $success_member[mb_homepage])?></div>
                <div style="position:absolute; margin:20px 0 0 270px; color:#888888; font-weight:bold;"><?php echo number_format($info[td_id])?> 포인트</div>
            <?php } ?>
            <?php if ($info[tender_count]) { ?>
                <div style="position:absolute; cursor:pointer; margin:8px 0 0 465px;"><img src="<?php echo $board_skin_url?>/img/btn_people.gif" onclick="tender_list()"></div>
                <?php if ($view[link][2]) { ?><div style="position:absolute; cursor:pointer; margin:8px 0 0 565px;"><a href="<?php echo $view[link_href][2]?>" target=_blank><img src="<?php echo $board_skin_url?>/img/btn_buy.gif"></a></div><?php } ?>
            <?php } ?>
        </div>

    <?php } elseif ($info[status] == '3') { ?>

        <div style="">
            <div style="float:left;"><img src="<?php echo $board_skin_url?>/img/tender_lost.gif"></div>
            <div style="float:left; cursor:pointer; margin-top:8px;"><img src="<?php echo $board_skin_url?>/img/btn_people.gif" onclick="tender_list()"></div>
        </div>

    <?php } else { ?>

        <form name="auction_tender" id="auction_tender" method="post" action="<?php echo $board_skin_url?>/tender.php" style="margin:18px 0 0 0; float:left;">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
            <input type="hidden" name="wr_id" value="<?php echo $wr_id?>">
<<<<<<< HEAD
            <input type="hidden" name="point" id="point" value="<?php echo wr_id?>"  itemname="입찰 번호" style=" border:1px solid #D3D3D3; width:80px; text-align:right; padding-right:10px;">
            
=======
            <!--<input type="text" name="point" id="point" value="" required numeric itemname="입찰 번호" style=" border:1px solid #D3D3D3; width:80px; text-align:right; padding-right:10px;">
            번호를 입찰하겠습니다.-->
>>>>>>> ff86520343171bda086111a6481e7710af66ccf8
        </form>
        <div style="float:left; cursor:pointer; margin:8px 0 0 10px;"><img src="<?php echo $board_skin_url?>/img/btn_participate.gif" onclick="tender_send()"></div>
        <!--
        <div style="float:left; cursor:pointer; margin:8px 0 0 10px;"><img src="<?php echo $board_skin_url?>/img/btn_tender_section.gif" id="btn_tender_section"></div>-->

        <?php if ($info[tender_count]) { ?><div style="float:left; cursor:pointer; margin:8px 0 0 10px;"><img src="<?php echo $board_skin_url?>/img/btn_people.gif" onclick="tender_list()"></div><?php } ?>
        <!--<?php if ($view[link][2]) { ?><div style="float:left; margin:8px 0 0 10px;"><a href="<?php echo $view[link_href][2]?>" target=_blank><img src="<?php echo $board_skin_url?>/img/btn_buy.gif"></a></div><?php } ?>-->


<link rel="stylesheet" href="<?php echo $board_skin_url?>/jquery-ui.css" type="text/css" media="all" />
<!-- script src="<?php echo $board_skin_url?>/jquery-ui.min.js"></script -->
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
<script>
jQuery.fn.center = function () { 
    this.css("position", "absolute"); 
    this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px"); 
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px"); 
    return this; 
}

$(function() {
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $("#btn_tender_section").click(function() {
        $( "#dialog" ).dialog({
            width: 500,
            height: 500,
            modal: true
        });
    });
});
</script>

<?
$lower = $info[tender_lower];
if ($is_member) {
    $sql = " select max(td_tender_point) as max_tender from $tender_table where mb_id = '$member[mb_id]' and wr_id = '$wr_id' ";
    $row = sql_fetch($sql);
    $lower = $row[max_tender] + 1;
}
$higher = $lower + 99;
?>

<script>
$(function() {
    $( "#slider-range" ).slider({
        range: true,
        min: <?php echo $info[tender_lower]?>,
        max: <?php echo $info[tender_higher]?>,
        values: [ <?php echo $lower?>, <?php echo $higher?> ],
        slide: function( event, ui ) { 
            //$( "#amount" ).val( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] );
            $( "#point_min" ).val( ui.values[ 0 ] );
            $( "#point_max" ).val( ui.values[ 1 ] );
        }
    });

    $( "#point_min,#point_max" ).change(function() {
        $( "#slider-range" ).slider({ 
            values: [$( "#point_min" ).val(), $( "#point_max" ).val()]
        });
    });

    //$( "#amount" ).val( "" + $( "#slider-range" ).slider( "values", 0 ) + " - " + $( "#slider-range" ).slider( "values", 1 ) );
    $( "#point_min" ).val( <?php echo $lower?> );
    $( "#point_max" ).val( <?php echo $higher?> );

    
    $("<img id='loading' alt='구간 입찰중 입니다. 작업을 중지하지 마십시오.' src='<?php echo $board_skin_url?>/img/loading.gif'>").appendTo(document.body).hide();
});

function fsection_submit(f) {
    if (confirm("정말 선택하신 구간으로 입찰하시겠습니까?\n\n입찰후에는 되돌릴 수 없으니 신중하게 입찰하시기 바랍니다.\n\n입찰완료 메세지가 출력되기 전까지 다른 작업을 하시면 안됩니다.")) {
        var min = parseInt( document.getElementById('point_min').value );
        var max = parseInt( document.getElementById('point_max').value );
        if (isNaN(min) || isNaN(max)) {
            alert('숫자가 아닙니다.');
            return false;
        }
        var num = max - min;
        if (num > 100) {
            alert('입찰 구간을 100단위로 조정하여 주십시오.');
            return false;
        }
        f.action = "<?php echo $board_skin_url?>/tender_section.php";
        $( "#loading" ).center().show();
        $( "#dialog" ).dialog("close");
        return true;
    }
    return false;
}
</script>

<div id="dialog" title="구간 입찰" style="display:none;">
    <form id="fsection" method="post" onsubmit="return fsection_submit(this);" autocomplete="off">
	<div>
        <p>
            <label for="amount">입찰 번호 구간:</label>
            <input type="text" id="point_min" name="point_min" size="5">번 -
            <input type="text" id="point_max" name="point_max" size="5">번
             <input type="text" id="amount" style="border:0; color:#f6931f; font-weight:bold;" readonly /> 
        </p>

        <div id="slider-range"></div>
        <div style="margin-top:20px;">
            입찰 번호 구간을 슬라이드로 정한 후 낮은 번호부터 순차적으로 구간 입찰을 합니다.
            <br>이미 입찰을 한 번호는 건너뛰며, 참여포인트의 합이 보유포인트 보다 모자르면 진행하지 않습니다.
            <br>입찰이 느려지는 현상을 방지하기 위하여 구간을 100단위로 조정하세요. 
            <br>예) 1~100, 100~200, 123~222
        </div>
        <div style="text-align:center;margin-top:20px;">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
            <input type="hidden" name="wr_id" value="<?php echo $wr_id?>">
            <input type="submit" value="구간 입찰하기">
        </div>
    </div>
    </form>
</div>

<?php } ?>

    </div>
    <!-- 입찰 부분 끝 -->

<?php
if ($is_member) { 

$row2 = sql_fetch(" select count(mb_id) as cnt from $tender_table where td_datetime like '".G5_TIME_YMD."%' and mb_id = '$member[mb_id]' and wr_id = '$wr_id' ");
$tender_count = $row2[cnt];

?>
    <!--<div style="margin-top:5px; padding:10px; color:#888; border:1px solid #ddd;">
    <b><?php echo $member[mb_nick]?></b>님께서는 <b><?php echo number_format($member[mb_point])?> 포인트</b>를 가지고 계십니다. (현재 <?php echo $tender_count?>번 입찰)
    </div>-->
<?php } ?>

<?php if ($is_admin && $super[point] && $info[status] == '1') { ?>
    <div style="padding:10px; border:1px solid #ddd; margin-top:10px;">
    (1위: <?php echo number_format($super[point])?> 포인트, <?php echo number_format($super[count])?> 회)
    <?php 
    foreach($super_mb_id as $mb_id) { 
        $mb = get_member($mb_id);
        echo get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_email], $mb[mb_homepage]);
    }
    ?>
    </div>
<?php } ?>

    <!--<div style="margin-top:5px; padding:10px; color:#336699; border:1px solid #ddd;">
    코멘트 남기지 않으시면 낙찰이 취소될 수 있습니다. ^_____^<br>
    낙찰시 해외배송은 하지 않으므로 상품을 배송 받을 한국 주소를 알려주시기 바랍니다.
    </div>-->

    <div id=writeContents style="margin:20px 0 20px 0;">
        <?php echo $view[content]?>
    </div>

    <?php
    include_once(G5_SNS_PATH."/view.sns.skin.php");
    ?>

    <?php
    // 코멘트 입출력
    include_once('./view_comment.php');
     ?>

    <!-- 링크 버튼 시작 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 링크 버튼 끝 -->

</article>


<script language="JavaScript">
function file_download(link, file) {
    <?php if ($board[bo_download_point] < 0) { ?>if (confirm("'"+file+"' 파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board[bo_download_point])?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?"))<?}?>
    document.location.href=link;
}

function tender_send() {
    //var p = document.getElementById("point").value;

    //if (!p) {
    //    alert("포인트를 입력해주세요.");
    //    return;
    //}

    if (confirm("정말 신청하시겠습니까?")) {
        document.auction_tender.submit();
    }
}

function tender_list() {
    tender_list_win = window.open("<?php echo $board_skin_url?>/tender_list.php?bo_table=<?php echo $bo_table?>&wr_id=<?php echo $wr_id?>","tender_list","width=500, height=800, scrollbars=1");
    tender_list_win.focus();
}
</script>

<?php if ($info[status] == 1 && $end_time > 0) {?>

<script language="JavaScript">

var end_time = <?php echo $end_time?>;

function run_timer() {
    var timer = document.getElementById("end_timer");

    dd = Math.floor(end_time/(60*60*24));
    hh = Math.floor((end_time%(60*60*24))/(60*60));
    mm = Math.floor(((end_time%(60*60*24))%(60*60))/60);
    ii = Math.floor((((end_time%(60*60*24))%(60*60))%60));

    var str = "";

    if (dd > 0) str += dd + "일 ";
    if (hh > 0) str += hh + "시간 ";
    if (mm > 0) str += mm + "분 ";
    str += ii + "초 ";

    timer.style.color = "red";
    timer.style.fontWeight = "bold";
    timer.innerHTML = str;

    end_time--;

    if (end_time < 0) clearInterval(tid);
}

run_timer();

tid = setInterval('run_timer()', 1000); 

</script>

<?php } ?>

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
</script>

