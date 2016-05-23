<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_JS_URL.'/jquery.bxslider.css">', 1);
add_javascript('<script src="'.G5_THEME_JS_URL.'/jquery.bxslider.min.js"></script>', 10);

$thumb_width  = isset($options['thumb_width']) ? $options['thumb_width'] : $board['bo_gallery_width'];
$thumb_height = isset($options['thumb_height']) ? $options['thumb_height'] : $board['bo_gallery_height'];
$content_length = isset($options['content_length']) ? $options['content_length'] : 30;
?>

<!-- <?php echo $bo_subject; ?> 최신글 시작 { -->
<div class="lt_slide ">

        <ul class="bxslider">
        <?php
        for ($i=0; $i<count($list); $i++) {
            $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height);

            if($thumb['src']) {
                $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
            } else {
                $img_content = '<span style="width:'.$thumb_width.'px;height:'.$thumb_height.'px" class="no_img">no image</span>';
            }
        ?>
            <li class="swiper-slide">
                <?php echo $img_content; ?>
                
            </li>
        <?php }  ?>

        <?php if ($i == 0) { //게시물이 없을 때  ?>
            <li class="no_bd">게시물이 없습니다.</li>
        <?php }  ?>
        </ul>

</div>
<script>
    $(document).ready(function(){
        $('.bxslider').bxSlider();
    });
</script>

<!-- } <?php echo $bo_subject; ?> 최신글 끝 -->