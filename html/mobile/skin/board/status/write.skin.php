<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<?php
// 포인트 가져 오기
$query5 = "select mb_point from g5_member where mb_id = '$homepage' ";
$result5 = sql_query($query5);
while($row  = sql_fetch_array($result5)) {
	$mypoint = $row[mb_point]; 
}
?>
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css">

<section id="bo_w">
    <h2 id="container_title">예약 현황</h2>

    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>">
    <input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">

    <input type="hidden" name="wr_subject" value="<?php echo $subject ?>">
    <input type="hidden" name="mypoint" value="<?php echo $mypoint ?>">

    <input type="hidden" name="wr_content" value="<?php echo $content ?>">
    <input type="hidden" name="wr_link1" value="<?php echo $write['wr_link1'] ?>">
    <input type="hidden" name="wr_link2" value="<?php echo $write['wr_link2'] ?>">
    <input type="hidden" name="ca_name" value="<?php echo $write['ca_name'] ?>">
    <input type="hidden" name="wr_1" value="<?php echo $wr_1 ?>">
    <input type="hidden" name="wr_2" value="<?php echo $wr_2 ?>">
    <input type="hidden" name="wr_3" value="<?php echo $wr_3 ?>">
    <input type="hidden" name="wr_4" value="<?php echo $wr_4 ?>">
    <input type="hidden" name="wr_5" value="<?php echo $wr_5 ?>">
    <input type="hidden" name="wr_6" value="<?php echo $wr_6 ?>">
    <input type="hidden" name="wr_7" value="<?php echo $wr_7 ?>">
    <input type="hidden" name="wr_8" value="<?php echo $wr_8 ?>">
    <input type="hidden" name="wr_9" value="<?php echo $wr_9 ?>">
    <input type="hidden" name="wr_10" value="<?php echo $wr_10 ?>">
	<input type="hidden" name="wr_11" value="<?php echo $homepage ?>">

	<?php
    $option = '';
    $option_hidden = '';
    if ($is_notice || $is_html || $is_secret || $is_mail) {
        $option = '';
        if ($is_notice) {
            $option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">공지</label>';
        }

        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">html</label>';
            }
        }

        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= "\n".'<input type="checkbox" id="secret" name="secret" value="secret" checked>'." \n".'<label for="secret">비밀글</label>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }

        if ($is_mail) {
            $option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
        }
    }

    echo $option_hidden;
    ?>

    <div class="tbl_frm01 tbl_wrp">
        <table>
        <tbody>
        <?php if ($option && $member['mb_level'] > 7) { ?>
        <tr>
            <th scope="row">옵션</th>
            <td><?php echo $option ?></td>
        </tr>
        <?php } ?>

        <tr>
            <th scope="row"><label for="wr_content">신청인<strong class="sound_only">필수</strong></label></th>
            <td><?php echo $content ?>(<?php echo $name ?>/<?php echo $homepage ?>)</td>
        </tr>

        <tr>
            <th scope="row"><label for="wr_email">이메일</label></th>
			<?php  if($wr_10 != "참가가능") { ?>
            <td><input type="text" name="wr_email" value="<?php echo $email ?>" id="wr_email" class="frm_input email" size="50" maxlength="100"></td>
			<? } else { ?>
			            <td>
             <?php echo $email ?>
            </td>
			<? } ?>
        </tr>

        <tr>
            <th scope="row"><label for="ca_name ">연락처</label></th>
            <td><input type="text" name="ca_name" value="<?php echo $write['ca_name'] ?>" id="wr_link2_hit" class="frm_input" size="30"></td>
        </tr>


        <tr>
            <th scope="row"><label for="wr_subject">제목<strong class="sound_only">필수</strong></label></th>
            <td>
             <?php echo $subject ?>
            </td>
        </tr>
       <tr>
            <th scope="row"><label for="wr_1">주최<strong class="sound_only">필수</strong></label></th>
            <td>
             <?php echo $wr_1 ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="wr_2">행사기간<strong class="sound_only">필수</strong></label></th>
            <td>
             <?php echo $wr_2 ?>
            </td>
        </tr>
<?php if($wr_6 != 2) { ?>	 <th scope="row"><label for="wr_6">특별 할인<strong class="sound_only">필수</strong></label></th> <td><?php echo $wr_8 ?><?php if($wr_7 == 1) echo "원"; else echo "%"; ?></td></tr> <? } ?>
<?php if($config['cf_use_point'] == 1) { 		
	if($write['wr_link2'] == "" ) 	$point = $mypoint;
	else $point = 	$write['wr_link2'];
		?>	        <tr>
            <th scope="row"><label for="wr_3">내 마일리지<strong class="sound_only"></strong></label></th>
            <td>
             <?php echo $point ?>원
            </td>
        </tr> <? } ?>
        <tr>
<?php 
if($wr_10 != "참가가능") {
	if($wr_7 != 1 && $wr_6 != 0) $wr_4 = $wr_4 - $wr_4*($wr_8/100); 
	if($wr_7 == 1 && $wr_6 != 0) $wr_4 = $wr_4 - $wr_8;
	
	if($config['cf_use_point'] == 1) {
		if($write['wr_link2'] == "" ) {
			if($wr_4 <= $mypoint) $wr_4 = 0;
			else $wr_4 = $wr_4 - $mypoint;
		}
		else {
			if($mypoint > $write['wr_link2']) 		$wr_4 = $wr_4 - $write['wr_link2']; 
			else $wr_4 = $wr_4 - $mypoint; 
		}
	}
		}

?>
            <th scope="row"><label for="price">참가비<strong class="sound_only">필수</strong></label></th>
            <td>
             <?php echo $wr_4 ?>원
<!--			 <input type="hidden" name="wr_4" value="<?php echo $wr_4 ?>"y> -->
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="wr_5">신청 인원<strong class="sound_only"></strong></label></th>
            <td>
             <?php echo $wr_5 ?>명
            </td>
        </tr>
        <?php if ($option && $member['mb_level'] > 7) { ?>
        <tr>
            <th scope="row"><label for="wr_10">상태<strong class="sound_only">필수</strong></label></th>
            <td>
			<?php  if($wr_10 == "예약정보" || $wr_10 == "") $a = "checked"; ?>
			<?php  if($wr_10 == "참가가능" || $wr_10 == "") $b = "checked"; ?>
			<?php  if($wr_10 == "입금대기" || $wr_10 == "") $c = "checked"; ?>
			<?php  if($wr_10 == "미입금" || $wr_10 == "") $d = "checked"; ?>
			<?php  if($wr_10 == "취소" || $wr_10 == "") $e = "checked"; ?>

                    <input type="radio" name="wr_10" value="예약정보" <?php echo $a; ?> id="wr_10"  class="frm_input required" >  예약(승인 대기 중)     <input type="radio" name="wr_10" value="참가가능" id="wr_10" <?php echo $b; ?>  class="frm_input required"> 참가가능(할인&마일리지 적용)   <input type="radio" name="wr_10" value="입금대기" id="wr_10" <?php echo $c; ?>  class="frm_input required"> 입금대기   <input type="radio" name="wr_10" value="미입금" id="wr_10" <?php echo $d; ?>  class="frm_input required"> 거절(미입금)   <input type="radio" name="wr_10" value="취소" id="wr_10" <?php echo $e; ?>  class="frm_input required"> 거절(취소)
            </td>
        </tr>
		<? } else { ?>
        <tr>
            <th scope="row"><label for="wr_10">상태<strong class="sound_only">필수</strong></label></th>
            <td>
			<?php  if($wr_10 == "예약정보") $wr_10 = "예약"; ?>
             <?php echo $wr_10 ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="wr_10">결제 정보<strong class="sound_only">필수</strong></label></th>
            <td>
			<?php   include("account.php"); ?>
            </td>
        </tr>
		<? }  ?>


        <?php if ($is_guest) { //자동등록방지  ?>
        <tr>
            <th scope="row">자동등록방지</th>
            <td>
                <?php echo $captcha_html ?>
            </td>
        </tr>
        <?php } ?>

        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
	     <input type="submit" value="확인" id="btn_submit" accesskey="s" class="btn_submit"> 
        <a href="./board.php?bo_table=<?php echo $bo_table ?>" class="btn_cancel">목록</a>
    </div>
    </form>

    <script>
    <?php if($write_min || $write_max) { ?>
    // 글자수 제한
    var char_min = parseInt(<?php echo $write_min; ?>); // 최소
    var char_max = parseInt(<?php echo $write_max; ?>); // 최대
    check_byte("wr_content", "char_count");

    $(function() {
        $("#wr_content").on("keyup", function() {
            check_byte("wr_content", "char_count");
        });
    });

    <?php } ?>
    function html_auto_br(obj)
    {
        if (obj.checked) {
            result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
            if (result)
                obj.value = "html2";
            else
                obj.value = "html1";
        }
        else
            obj.value = "";
    }

    function fwrite_submit(f)
    {
        <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
            type: "POST",
            data: {
                "subject": f.wr_subject.value,
                "content": f.wr_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });

        if (subject) {
            alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
            f.wr_subject.focus();
            return false;
        }

        if (content) {
            alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
            if (typeof(ed_wr_content) != "undefined")
                ed_wr_content.returnFalse();
            else
                f.wr_content.focus();
            return false;
        }

        if (document.getElementById("char_count")) {
            if (char_min > 0 || char_max > 0) {
                var cnt = parseInt(check_byte("wr_content", "char_count"));
                if (char_min > 0 && char_min > cnt) {
                    alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                    return false;
                }
                else if (char_max > 0 && char_max < cnt) {
                    alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
        }

        <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>
</section>
<!-- } 게시물 작성/수정 끝 -->