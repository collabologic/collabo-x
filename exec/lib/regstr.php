<?php
function RegStr($params,&$smarty) {
	static $kana, $kigo, $sep;
	$str = $params['str'];
	$opt = $params['opt'];
$sep = "|";
	if( !$kana ){
		$kana = array(
			/* 平仮名のみ */
			 "hira"=>array(
			 	 0=>"なにぬねのまみむめもらりるれろゐゑをん"
			 )
			/* 濁音/半濁音(半濁音と対応させる為にハ行を先頭に持ってくる) */
			,"daku"=>array(
				 0=>"はひふへほかきくけこさしすせそたちつてと"
				,1=>"ばびぶべぼがぎぐげござじずぜぞだぢづでど"
				,2=>"ぱぴぷぺぽ"
			)
			/* 小さい文字があるもの */
			,"sml"=>array(
				 0=>"あいうえおつやゆよわ"
				,1=>"ぁぃぅぇぉっゃゅょゎ"
			)
		);

		/* 特殊記号類(mb_convert_kanaで変換されないもの,正規表現の特殊記号) */
		$kigo = array(
			/* 正規表現の特殊記号(0:半角,1:全角,2:マッチングする場合の文字列) */
			 "reg"=>array(
				 0=>"^.[]$()|*?{}\\'\""
				,1=>"＾．［］＄（）｜＊？｛｝￥’”"
				,2=>"(\\^)|(\\.)|(\\[)|(\\])|(\\$)|(\\()|(\\))|(\\|)|(\\*)|(\\?)|(\\{)|(\\})|(\\\\)|(\\')|(\\\")"
			)
			// ↓多分足りないと思うので適宜追加してください
			/* mb_convert_kana関数で変換されないもの(0:半角,1:全角) */
			,"nonconv"=>array(
				 0=>"\\\"'~ﾞﾟ"
				,1=>"￥”’～゛゜"
			)
		);
	}

	$str0 = &$str;
	/* 文字のチェックを容易にする為、ASCII文字は半角に、片仮名は全角平仮名にする */
	$str1 = mb_convert_kana($str, "acsHV","UTF-8");

	/* 文字数を取得(半角カタカナの濁音等は2文字になるので変換後の文字列から) */
	$len = mb_strlen($str1,"UTF-8");

	/* 前方一致の場合、先頭に"^"をつける */
	$regstr = (ereg("f", $opt)?"^":"");

	/* 正規表現文字列作成(1文字単位で) */
	for($pos0=0,$pos1=0;$pos1<$len;$pos0+=mb_strlen($cur0,"UTF-8"),$pos1++) {
		/* 操作対象文字切り出し */
		$cur0 = mb_substr($str0, $pos0, 1,"UTF-8");	/* 元の文字 */
		$cur1 = mb_substr($str1, $pos1, 1,"UTF-8");	/* 検索用文字 */
//if( $pos1 == 1)return("AAA][".$pos0);
		$wkstr = $cur0;
		/* 英数字,記号の場合 */
		if ( strlen($cur1) == 1 ) {
			/* アルファベットの場合 */
			if ( ereg("[A-Za-z]", $cur1) ) {
				/* 大文字/小文字を区別しない場合 */
				if ( !ereg("c", $opt) ) {
					$wkstr = mb_strtoupper($wkstr,"UTF-8").$sep.mb_strtolower($wkstr,"UTF-8").$sep;
				}
				/* 半角/全角を区別しない場合 */
				if ( !ereg("a", $opt) ) {
					$wkstr = mb_convert_kana($wkstr, "r","UTF-8").mb_convert_kana($wkstr, "R","UTF-8");
				}
			}
			/* 数字の場合 */
			else if ( ereg("[0-9]", $cur1) ) {
				/* 半角/全角を区別しない場合 */
				if ( !ereg("n", $opt) ) {
					$wkstr = mb_convert_kana($wkstr, "n","UTF-8").$sep.mb_convert_kana($wkstr, "N","UTF-8").$sep;
				}
			}
			/* 特殊文字(正規表現で使用される記号)の場合 */
			else if ( ereg($kigo["reg"][2], $cur1) ) {
				/* 半角/全角を区別しない場合 */
				if ( !ereg("n", $opt) ) {
					$curpos = strpos($kigo["reg"][0], $wkstr);
					$wkstr = $wkstr.$sep.mb_substr($kigo["reg"][1], $curpos, 1,"UTF-8").$sep;
				}
				$wkstr = "\\".$wkstr;
			}
			/* mb_convert_kanaで変換されない記号の場合 */
			else if ( ereg("[".$kigo["nonconv"][0]."]", $cur1) ) {
				/* 半角/全角を区別しない場合 */
				if ( !ereg("n", $opt) ) {
					$curpos = strpos($kigo["nonconv"][0], $wkstr);
					$wkstr .= mb_substr($kigo["nonconv"][1], $curpos, 1,"UTF-8").$sep;
				}
			}
			/* その他の記号の場合 */
			else {
				/* 半角/全角を区別しない場合 */
				if ( !ereg("s", $opt) ) {
					$wkstr = mb_convert_kana($wkstr, "a","UTF-8").$sep.mb_convert_kana($wkstr, "A","UTF-8");
				}
			}

			
		}
		/* 全角文字の場合 */
		else {
			/* 現在の文字が仮名文字かをチェック */
			unset($posary);
			foreach ( $kana as $k => $v ) {
				for($i=0;$i<count($v);$i++) {
					if ( ($wkpos=mb_strpos(" ".$v[$i], $cur1,0,"UTF-8")-1) > -1 ) { $posary[$k] = $wkpos; }
				}
			}

			/* 仮名文字の場合 */
			if ((isset($posary)) && ( is_array($posary) )) {

				/* 半角カナの濁音/半濁音の場合、濁点/半濁点も一緒に取る */
				if ( (isset($posary["daku"]))
					&& (($wkstr=mb_substr($str0, $pos0+1, 1,"UTF-8")) == "ﾞ") || ($wkstr == "ﾟ") ) {
					$cur0 .= $wkstr;
				}
				/* この段階では全角平仮名で文字列を作成しておく(後で元の文字に合わせて修正する) */
				$wkstr = $cur1.$sep;
				/* 実際の文字種別を取得 */
				if ( $cur0 == $cur1 ) { $ctype = ""; }								/* 平仮名 */
				else if ( $cur0 == mb_convert_kana($cur1, "C","UTF-8") ) { $ctype = "C"; }	/* 片仮名 */
				else { $ctype = "h"; }												/* 半角カナ */

				/* マッチング文字列作成 */
				/* 濁音/半濁音グループの場合 */
				if ( isset($posary["daku"]) ) {
					/* 濁音を識別しない場合 */
					if ( !ereg("d", $opt) ) {
						$curpos="";
						$work_str="";
						$wkstr = mb_substr($kana["daku"][0], $posary["daku"], 1,"UTF-8").$sep			/* 清音 */
							.mb_substr($kana["daku"][1], $posary["daku"], 1,"UTF-8").$sep;				/* 濁音 */
						$work_str=mb_substr($kana["daku"][2], $posary["daku"], 1,"UTF-8");	/* 半濁音 */
						if (!empty($work_str)){
							$wkstr = $wkstr . $work_str.$sep;
						}
					}
				}
				/* 小さい文字があるものの場合 */
				if ( isset($posary["sml"]) ) {
					/* 小さい文字を識別しない場合 */
					if ( !ereg("y", $opt) ) {
						$wkstr = (mb_strlen($wkstr,"UTF-8")>1?$wkstr:mb_substr($kana["sml"][0], $posary["sml"], 1,"UTF-8"))
							.mb_substr($kana["sml"][1], $posary["sml"], 1,"UTF-8")
							.$sep;
					}
				}
				/* 平仮名/片仮名/半角カナを識別しない場合 */
				if ( !ereg("[hk]", $opt) ) {
					$wkstr .= mb_convert_kana($wkstr, "C","UTF-8").mb_convert_kana($wkstr, "h","UTF-8");
				}

				/* 平仮名/片仮名を識別しない場合 */
				else if ( !ereg("h", $opt) ) {
					$wkstr .= mb_convert_kana($wkstr, "C","UTF-8");
				}
				/* 全角片仮名と半角カナを識別しない(平仮名の場合は関係ない) */
				else if ( ($ctype) && (!ereg("k", $opt)) ) {
					$wkstr = mb_convert_kana($wkstr, "C","UTF-8").mb_convert_kana($wkstr, "h","UTF-8");
				}
				/* 平仮名/片仮名/半角カナを識別する場合 */
				else if ( $ctype ) {
					$wkstr = mb_convert_kana($wkstr, $ctype,"UTF-8");
				}
				/* 元の文字が平仮名の場合はなにもしない */

				/* 半角カナ濁音/半濁音がある場合は()で囲む */

				$wkrep = mb_convert_kana($cur1, "h","UTF-8");
				$wkstr = ereg_replace("(".$wkrep."ﾞ)|(".$wkrep."ﾟ)", "(\\0)", $wkstr);
			}
			/* 特殊記号類の場合(mb_convert_kanaで変換できなかったもの) */
			else if ( ereg($cur1, $kigo["nonconv"][1]) ) {
				/* 半角/全角を区別しない場合 */
				if ( !ereg("n", $opt) ) {
					$wkpos = mb_strpos($kigo["nonconv"][1], $wkstr,"UTF-8");
					$cur1 = mb_substr($kigo["nonconv"][0], $wkpos, 1,"UTF-8");
					$wkstr .= (ereg($kigo["reg"][2], $cur1)?"\\":"").$cur1.$sep;
				}
			}
			/* それ以外はそのまま */

		}
		// 最後文字がセパレータならば取り除く
		if (mb_substr($wkstr, -1, 1,"UTF-8") == "|"){
			$wkstr = substr($wkstr, 0, strlen($wkstr)-1);
		}
		/* 正規表現文字列作成 */
		if ( mb_strlen($wkstr,"UTF-8") > 1 ) { $regstr .= "(".$wkstr.")"; }
		else { $regstr .= $wkstr; }
	}

	/* 後方一致の場合 */
	if ( ereg("b", $opt) ) { $regstr .= "$"; }
	return ($regstr);
}

?>