<?php
	require_once DIR_APP."/app_constants.php";

	// -------------------------------------------------------------------
    // バリデータオブジェクトの基底クラス
    // 使い方
    //   EXTENDでしたクラスで書きを追加
    //       XXXX_validate() 各メソッドごとのバリデート処理
    //       $ARGNAMES　パラメータの名前リスト
    //            ex:$ARGNAMES=array('title'=>'タイトル','contents'=>'内容');
    //       $CHECKLIST checkAllで実行する場合のチェック内容
    //			  ex:$CHECKLIST=array(
    //					array('成約メソッド名','パラメータ名','成約メソッドの引数',...)
    //                )
    // -------------------------------------------------------------------
	class standard_validator {
		// -------------------------------------------------------------------
    	// バリデータと関連のあるパラメータのみを抜き出すメソッド
    	// 引数
    	//   $ARGS : バリデートパラメータを含む連想配列
    	// 戻り値
    	//   $ARGSのうちARGNAMESに存在するキーの要素
    	// -------------------------------------------------------------------
		public function cutting($ARGS,$ARGNAMES){
			foreach($ARGNAMES as $key => $name){
				// サニタイイング
				if(isset($ARGS[$key])){
					$return[$key] = $ARGS[$key];
					$return[$key] = str_replace("&","&amp",$return[$key]);
					$return[$key] = str_replace("<","&lt",$return[$key]);
					$return[$key] = str_replace(">","&gt",$return[$key]);
					$return[$key] = str_replace("'","''",$return[$key]);
				}
			}
			return $return;
		}

		// -------------------------------------------------------------------
    	// $CHECKLISTを利用してのチェック
    	// 引数
    	//   $ARGS 		: パラメータ連想配列
    	//	 $checklist	: table形式の値チェックリスト
    	//	 $valnames	: 値の日本語名一覧
    	//	 $msg		: エラーメッセージを返す配列
    	// 戻り値
    	//   メッセージ配列（エラーが無い場合はtrue)
    	// -------------------------------------------------------------------
		public function checkAll(&$ARGS,$checklist,$valnames,&$msg){
			$msg = array();
			$this->ARGNAMES=$valnames;
			foreach( $checklist as $line ){
				$res=$this->{"check_".$line['method']}($ARGS,$line['target'],$line['param1'],$line['param2']);
				if($res!=1){
					array_push($msg,$res);
				}
			}
			if( count($msg) > 0){
				return false;
			}else{
				return true;
			}
		}
		// -------------------------------------------------------------------
    	// 単独値のチェック
    	// 引数
    	//   $ARGS 		: パラメータ値
    	//	 $checkdata	: チェックの連想配列
    	//	 $valname	: 値の日本語名
    	//	 $msg		: エラーメッセージを追加配列
    	// 戻り値
    	//   メッセージ配列（エラーが無い場合はtrue)
    	// -------------------------------------------------------------------
		public function checkOne(&$value,$checkdata,$valname,&$msg){
			$this->ARGNAMES[$checkdata['target']]=$valname;
			$res=$this->{"check_".$checkdata['method']}($value,$checkdata['target'],$checkdata['param1'],$checkdata['param2']);
			if($res!=1){
				array_push($msg,$res);
			}
			return $res;
		}
		// -------------------------------------------------------------------
    	// NOTNULL制約チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
		protected function check_notnull($ARGS,$param){
			global $RSRC_STR;
			if ( array_key_exists($param,$ARGS) === false || strlen($ARGS[$param])==0){
				return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_NOTNULL'];
			} else {
				return true;
			}
		}

		// -------------------------------------------------------------------
    	// 文字列長制約チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	//   $minlength:最短文字列長
    	//   $minlength:最長文字列長（＋１）
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
		protected function check_length (&$ARGS,$param,$minlength,$maxlength){
			global $RSRC_STR;
			if( mb_strlen($ARGS[$param])<1 ){
				return true;
			}
			if ( mb_strlen($ARGS[$param]) <= $minlength ) {
				return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_LENGTH_TOSHORT'];
			} else if ( strlen($ARGS[$param]) > $maxlength ) {
				$ARGS[$param]=mb_strcut($ARGS[$param],0,$maxlength,"EUC-JP");
				return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_LENGTH_TOLONG'];
			}
			return true;
		}

		// -------------------------------------------------------------------
    	// 範囲付き数値制約チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	//	 $minvalue: 最小値
    	//   $maxvalue: 最大値
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
		protected function check_number_span($ARGS,$param,$minvalue,$maxvalue){
			global $RSRC_STR;
			if ( preg_match('/^[0-9]+$/',$ARGS[$param]) ){
				if( $minvalue <= $ARGS[$param] && $ARGS[$param] < $maxvalue ){
					return true;
				}
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_ISNUMBER_SPAN'];
		}

		// -------------------------------------------------------------------
    	// パラメータ間対比メソッド(比較対象より大きい）
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	//   $param2: 比較対象パラメータのキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
		protected function check_larger_than($ARGS,$param,$param2){
			global $RSRC_STR;
			if ( strlen($ARGS[$param]) == 0 || strlen($ARGS[$param2])==0 ){
				return true;
			}
			if ( $ARGS[$param] >= $ARGS[$param2] ){
				return true;
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_ISLARGER'];
		}

		// -------------------------------------------------------------------
    	// パラメータ間対比メソッド(比較対象より小さい）
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	//   $param2: 比較対象パラメータのキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
		protected function check_smaller_than($ARGS,$param,$param2){
			global $RSRC_STR;
			if ( !isset($ARGS[$param]) || !isset($ARGS[$param2]) ){
				return true;
			}
			if ( isset($ARGS[$param]) && strlen($ARGS[$param]) == 0 || isset($ARGS[$param2]) && strlen($ARGS[$param2])==0 ){
				return true;
			}
			if (isset($ARGS[$param]) &&  $ARGS[$param] <= $ARGS[$param2] ){
				return true;
			}
			return $this->ARGNAMES[$param].','.$this->ARGNAMES[$param2].CONSTANT::$RSRC_STR['ERR_ISSMALLER'];
		}

		// -------------------------------------------------------------------
    	// 数値制約チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
		protected function check_number($ARGS,$param){
			global $RSRC_STR;
			if ( (isset($ARGS[$param]) && (is_numeric($ARGS[$param])) || strlen($ARGS[$param])==0 || !isset($ARGS[$param])) ){
				return true;
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_ISNUMBER'];
		}

		// -------------------------------------------------------------------
    	// 日付制約チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
    	protected function check_datetime($ARGS,$param){
			global $RSRC_STR;
			if ( !isset($ARGS[$param])){
				return true;
			}
			if ( isset($ARGS[$param]) && strlen($ARGS[$param]) == 0 ){
				return true;
			}
			if ( isset($ARGS[$param]) && preg_match("/^\d{4}\/\d{2}\/\d{2}$/",$ARGS[$param]) > 0 ){
				return true;
			} else if ( isset($ARGS[$param]) && preg_match("/^\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2}$/",$ARGS[$param]) > 0){
				return true;
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_ISDATETIME'];
		}

		// -------------------------------------------------------------------
    	// メールアドレスチェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
    	protected function check_mailaddr($ARGS,$param){
			global $RSRC_STR;
			if ( isset($ARGS[$param]) && strlen($ARGS[$param]) == 0 ){
				return true;
			}
			if ( isset($ARGS[$param]) && preg_match("/^(.*?)@(.*?)/",$ARGS[$param]) > 0 ){
				return true;
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_ISMAILADDR'];
		}
		// -------------------------------------------------------------------
    	// 電話番号チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
    	protected function check_tel($ARGS,$param){
			global $RSRC_STR;
			if ( isset($ARGS[$param]) && strlen($ARGS[$param]) == 0 ){
				return true;
			}
			if ( isset($ARGS[$param]) && preg_match("/^[0-9-]+$/",$ARGS[$param]) > 0 ){
				return true;
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_TEL'];
		}


		// -------------------------------------------------------------------
    	// 半角チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
    	protected function check_byte1($ARGS,$param){
			global $RSRC_STR;
			if( !isset($ARGS[$param])){
				return true;
			}

			if ( isset($ARGS[$param]) && strlen($ARGS[$param]) == 0 ){
				return true;
			}
			if ( isset($ARGS[$param]) && preg_match("/^[ -~]+$/",$ARGS[$param]) > 0 ){
				return true;
			}
			return $this->ARGNAMES[$param].CONSTANT::$RSRC_STR['ERR_ISBYTE1'];
		}
		// -------------------------------------------------------------------
    	// 同一値チェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
    	protected function check_equal($ARGS,$param1,$param2){
			global $RSRC_STR;
			if ( $ARGS[$param1] == $ARGS[$param2]  ){
				return true;
			}
			return $this->ARGNAMES[$param1]."と".$this->ARGNAMES[$param2].CONSTANT::$RSRC_STR['ERR_EQUAL'];
		}
		// -------------------------------------------------------------------
    	// ひらがなチェックメソッド
    	// 引数
    	//   $ARGS : パラメータ連想配列
    	//   $param : ARGSのチェック対象のキー
    	// 戻り値
    	//   異常：array(<パラメータ和名>,<エラー内容>);
    	//   正常：true
    	// -------------------------------------------------------------------
    	protected function check_hkana($ARGS,$param1,$param2){
			global $RSRC_STR;
			if ( preg_match('/^[あ-ん].*?$/',$ARGS[$param1]) ){
				return true;
			}
			return $this->ARGNAMES[$param1].CONSTANT::$RSRC_STR['ERR_HKANA'];
		}
	}
?>