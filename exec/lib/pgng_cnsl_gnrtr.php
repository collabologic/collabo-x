<?php
	require_once DIR_FW."/fw_common.php";
	/** -------------------------------------------------------------------
  	 * ページングコンソール生成クラス
  	 */
  class pgng_cnsl_gnrtr {
  		/** -------------------------------------------------------------------
  	 	 * コンソール生成関数
  	 	 * @param  integer	$total_line 	全データ件数
  	 	 * @param  integer	$limit_line 	1ページのデータ件数
  	 	 * @param  integer	$current_ofset 	表示中のオフセット
  	 	 * @return string ページングコンソール向け配列
  	 	 */
	  	function getPgngCnsl($total_line, $limit_line, $current_ofset){
  			$links = array();
  			// ページ計算に変換
  			$current_page = floor($current_ofset/$limit_line);
  			$total_page = ceil($total_line/$limit_line);

	  		// 現在ページが1ページ目以外の場合
	  		if( $current_page != 0){
	  			array_push($links,array("caption"=>"先頭","ofset"=>0));
  			}
  			
  			// ２ページ以降なら＞＞を追加
  			if( $current_ofset >0 ){
  				array_push($links,array("caption"=>"＜＜","ofset"=>($current_page-1)*$limit_line));
  			}
  			
  			// ５ページ以内しか存在しない場合
  			if( $total_page < 5 ){
  				for( $i = 0 ; $i < $total_page ; $i++ ){
  					array_push($links,array("caption"=>$i+1,"ofset"=>$i*$limit_line));
  				}
  			}
  			// ５ページ以上存在し、現在ページが3ページ以内の場合
  			elseif( $current_page < 3){
  				for( $i = 0 ; $i < 5 ; $i++ ){
  					array_push($links,array("caption"=>$i+1,"ofset"=>$i*$limit_line));
   				}
   			}
  			// ５ページ以上存在し、現在ページが最大ページー２ページ以上の場合
  			elseif( $current_page > $total_page-2){
   				for( $i = $total_page -4 ; $i < $total_page ; $i++ ){
  					array_push($links,array("caption"=>$i+1,"ofset"=>$i*$limit_line));
  				}
  			}
  			
  			// ５ページ以上存在し、現在ページが４ページ以上最大ページ−２ページ以内の場合
	  		elseif( 3 <= $current_page && $current_page <= $total_page-2){
  				for( $i = $current_page-2 ; $i <= $current_page+2 ; $i++ ){
  					array_push($links,array("caption"=>$i+1,"ofset"=>$i*$limit_line));
   				}
  			}
  			
	  		// 最終ページより前なら＞＞を追加
  			if( $current_page+1 < $total_page ){
  				array_push($links,array("caption"=>"＞＞","ofset"=>($current_page+1)*$limit_line));
  			}
  			
	  		// 現在ページが最後のページ目以外の場合
	  		if( $current_page+1 != $total_page){
				array_push($links,array("caption"=>"末尾","ofset"=>($total_page-1)*$limit_line));
  			}
  			
  			if( count($links) >1){
	  			return $links;
  			}
  			return;
  		}
    }
?>