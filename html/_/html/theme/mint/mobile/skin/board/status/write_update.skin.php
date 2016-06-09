<?php 
if($wr_10 == "참가가능" && $wr_link1 != 1) {

	if($wr_7 != 1 && $wr_6 != 2) $wr_4 = $wr_4 - $wr_4*($wr_8/100); 
	if($wr_7 == 1 && $wr_6 != 2) $wr_4 = $wr_4 - $wr_8;
	
	if($config['cf_use_point'] == 1 ) {
		if($wr_link2 == "" ) {
			if($wr_4 <= $$mypoint) {
				$point = $mypoint - $wr_4;
				$wr_4 = 0;
			}
			if($wr_4 > $mypoint) {
				$wr_4 = $wr_4 - $mypoint;
				$point = 0;
			}
		}
		if($wr_link2 != "" ) {
			if($mypoint > $wr_link2) {
				$wr_4 = $wr_4 - $wr_link2; 
				$point = $mypoint - $wr_link2;
			}
			if($mypoint <= $wr_link2) {
				$wr_4 = $wr_4 - $mypoint; 
				$point = 0;
			}
		}
		if($wr_4 < 0) $wr_4 =0;
		
		sql_query("update $write_table set wr_4 = '$wr_4' where wr_id = '$wr_id' " );
		sql_query("update $write_table set wr_6 = '1' where wr_id = '$wr_id' " );


		$add_point = $point + $wr_3; 
		sql_query("update g5_member set mb_point = '$add_point' where mb_id = '$wr_11' " );
	}
	if($config['cf_use_point'] != 1) {
		if($wr_4 < 0) $wr_4 =0;
		sql_query("update $write_table set wr_4 = '$wr_4' where wr_id = '$wr_id' " );
		sql_query("update $write_table set wr_6 = '1' where wr_id = '$wr_id' " );

		$add_point = $mypoint + $wr_3; 
		sql_query("update g5_member set mb_point = '$add_point' where mb_id = '$wr_11' " );
	}
		sql_query("update $write_table set wr_link1 = '1' where wr_id = '$wr_id' " );
}
		sql_query("update g5_write_estatus set wr_homepage = '$wr_11' where wr_id = '$wr_id' " );
?>

