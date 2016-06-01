<?php
/**************************
@Filename: navigation.extend.php
@Version : 0.1
@Author  : Freemaster
@Date  : 2016/04/12 화요일 오전 11:43:20
@Content : PHP by Editplus
**************************/
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function get_top_navi()
{
    global $g5;

    $bMenuSql = "";
    $bMenuResult = "";
    $bMenuRow = array();

    $mMenuSql = "";
    $mMenuResult = "";

    //메뉴 배열 저장
    $bMenuSql = " SELECT * FROM ".$g5['menu_table']." WHERE me_use = '1' AND LENGTH(me_code) = '2' ORDER BY me_order, me_id ";
    $bMenuResult = sql_query($bMenuSql,false);
    for($i=0; $bMenuRow = sql_fetch_array($bMenuResult); $i++) {
        $bMenu[$i] = $bMenuRow;

        $mMenuSql = " SELECT * FROM ".$g5['menu_table']." WHERE me_use = '1' AND LENGTH(me_code) = '4' AND SUBSTRING(me_code, 1, 2) = '".$bMenuRow['me_code']."' ORDER BY me_order, me_id ";
        $mMenuResult = sql_query($mMenuSql);
        for($j=0; $mMenuRow = sql_fetch_array($mMenuResult); $j++) {
            $bMenu[$i][$j] = $mMenuRow;
        }
        $bMenu[$i]['cnt'] = $j; //중메뉴 개수
    }
    $bMenu['cnt'] = count($bMenu); //대메뉴 개수
    $bMenu['mns'] = $bMenu['cnt']?"mn".$bMenu['cnt']:"mn5";

    return $bMenu;
}

function get_middle_navi()
{
    global $g5, $group, $board, $co;
    global $gr_id, $bo_table, $co_id;

    //중단 메뉴 정의
    if($gr_id || $bo_table || $co_id) {
        if($gr_id) {
            $gRow = "";
            $pRow = array();
            $lMenuCnt = 0;
            $meLink = "gr_id=".$gr_id;
            if($bo_table)
                $meLink = "bo_table=".$bo_table;
        }
        if($co_id) {
            $gRow = "";
            $pRow = array();
            $lMenuCnt = 0;
            $meLink = "co_id=".$co_id;
        }

        //그룹메뉴
        $gSql = "SELECT * FROM ".$g5['menu_table']." WHERE me_use = '1' AND me_link LIKE ('%".$meLink."%') ";
        $gRow = sql_fetch($gSql);
        $gLen = strlen($gRow['me_code']);

        if($gLen > 2) { //그룹으로 구한 값이 1차메뉴가 아닐때
            $gSql = "SELECT * FROM ".$g5['menu_table']." WHERE me_use = '1' AND LENGTH(me_code) = '2' AND LEFT(me_code, 2) = '".substr($gRow['me_code'],0,2)."' ORDER BY me_order, me_id ";
            $gRow = sql_fetch($gSql);
        }

        $pSql = " SELECT * FROM ".$g5['menu_table']." WHERE me_use = '1' AND LENGTH(me_code) = '4' AND SUBSTRING(me_code, 1, 2) = '".$gRow['me_code']."' ORDER BY me_order, me_id ";
        $pResult = sql_query($pSql);
        for($i=0; $pRow = sql_fetch_array($pResult); $i++) {
            $lMenu[$i] = $pRow;
        }
        $lMenu['cnt'] = count($lMenu);
    } else {
        if(basename($_SERVER['PHP_SELF']) == "search.php")
            $gRow['me_name'] = "검색";
    }

    $lMenu['gTitle'] = $group['gr_subject']?$group['gr_subject']:$gRow['me_name'];
    $lMenu['pTitle'] = $board['bo_subject']?$board['bo_subject']:$co['co_subject'];
    $lMenu['gLink'] = $gRow['me_link']?$gRow['me_link']:"";
    if($lMenu['cnt'])
        $lMenu['lmWidth'] = (int)100/$lMenu['cnt'];

    return $lMenu;
}