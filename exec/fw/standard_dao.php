<?php
  require_once DIR_FW."/smarty/Smarty.class.php";
  require_once DIR_LIB."/regstr.php";  
  require_once DIR_FW."/basic_dao.php";
  // -------------------------------------------------------------------
  // 標準的なデータベースアクセスオブジェクト
  // 使い方
  //   インスタンス作成後、SELECT・FIND・MAX･DELETE・INSERT・UPDATEなどのメソッドを実行
  // -------------------------------------------------------------------
  class standard_dao extends basic_dao {
  	// -------------------------------------------------------------------
    // SELECTメソッド
    // 引数
    //   $VLIST : 出力する属性名（NULLまたは空配列の場合は＊）
    //	 $FROM	: 検索対象のテーブル名またはビュー名
    //	 $WHERE : 検索条件
    //	 $ORDER : ソート条件配列 array(array('key'=>$key,'ascdesc'=>$ascdesc)...)の形式
    //	 $LIMIT	: LIMIT句の中に入る文字列
    //	 $OFFSET: LIMIT句の先に入る分
    // 戻り値
    //   ARRAY　：　結果連想配列の配列
    // -------------------------------------------------------------------
    public function SELECT($VLIST,$FROM,$WHERE=array(),$ORDER,$ASCDESC,$LIMIT,$OFFSET){
    	// REGEXP 全文検索対応
    	foreach($WHERE as $key => $value){
    		if(preg_match('/.*?_TEXTSRCH/',$key)){
    			$value=str_replace('　',' ',$value);
   		 		$strs = split(' ',$value);
   		 		$trgtstr = str_replace('_TEXTSRCH','',$key);
    			$trgts = split('-',$trgtstr);
    			$text = array();
    			foreach( $trgts as $trgt ){
    				$text[$trgt]=$strs;
    			}
    			unset($WHERE[$key]);
    			$WHERE['TEXTSRCH']=$text;
    		}
    	}
    	
    	$ARGS=array(
    		"VLIST" => $VLIST,
    		"TABLE"	=> $FROM,
    		"WHERE"	=> $WHERE,
    		"ORDER"	=> $ORDER,
    		"ASCDESC"=> $ASCDESC,
    		"LIMIT" =>$LIMIT,
    		"OFFSET" =>$OFFSET
    	);
    	return $this->GETLIST("standard/SELECT",$ARGS);
    }
    // -------------------------------------------------------------------
    // FINDメソッド（検索結果の一行目のみを返す）
    // 引数
    //   $VLIST : 出力する属性名（NULLまたは空配列の場合は＊）
    //	 $FROM	: 検索対象のテーブル名またはビュー名
    //	 $WHERE : 検索条件
    //	 $ORDER : ソート条件配列 array(array('key'=>$key,'ascdesc'=>$ascdesc)...)の形式
    // 戻り値
    //   ARRAY　：　結果連想配列の配列
    // -------------------------------------------------------------------
    public function FIND($VLIST,$FROM,$WHERE,$ORDER){
    	$ARGS=array(
    		"VLIST" => $VLIST,
    		"TABLE"	=> $FROM,
    		"WHERE"	=> $WHERE,
    		"ORDER"	=> $ORDER
    	);
    	return $this->GET("standard/FIND",$ARGS);
    }
    // -------------------------------------------------------------------
    // INSERTメソッド
    // 引数
    //   $TABLE : テーブル名
    //	 $ARGS　　：　挿入されるデータの配列 array(array(属性名=>値)...)の形式
    // 戻り値
    //   ARRAY　：　影響行数
    // -------------------------------------------------------------------
    public function INSERT($TABLE,$ARGS){
    	$ARGS=array(
    		"TABLE" => $TABLE,
    		"VALUES"	=> $ARGS
    	);
    	return $this->EXC("standard/INSERT",$ARGS);
    }
    
    // -------------------------------------------------------------------
    // UPDATEメソッド
    // 引数
    //   $TABLE : テーブル名
    //	 $ARGS　　：　変更されるデータの配列 array(array(属性名=>値)...)の形式
    //	 $WHERE : 変更条件の配列（SELECT同様）
    // 戻り値
    //   ARRAY　：　実行結果
    // -------------------------------------------------------------------
    public function UPDATE($TABLE,$ARGS,$WHERE){
    	$ARGS=array(
    		"TABLE" => $TABLE,
    		"VALUES"	=> $ARGS,
    		"WHERE"	=> $WHERE
    	);
    	return $this->EXC("standard/UPDATE",$ARGS);
    }
    
    // -------------------------------------------------------------------
    // DELETEメソッド
    // 引数
    //   $TABLE : テーブル名
    //	 $WHERE : 変更条件の配列（SELECT同様）
    // 戻り値
    //   ARRAY　：　実行結果
    // -------------------------------------------------------------------
    public function DELETE($TABLE,$WHERE){
    	$ARGS=array(
    		"TABLE" => $TABLE,
    		"WHERE"	=> $WHERE
    	);
    	return $this->EXC("standard/DELETE",$ARGS);
    }
      
    // -------------------------------------------------------------------
    // MAXメソッド
    // 引数
    //   $TABLE : テーブル名
    //	 $WHERE : 変更条件の配列（SELECT同様）
    //	 $KEY   ：　対象となる属性名
    // 戻り値
    //   ARRAY　：　指定属性の最大値
    // -------------------------------------------------------------------
    public function MAX($TABLE,$WHERE,$KEY){
    	$ARGS=array(
    		"TABLE" => $TABLE,
    		"WHERE"	=> $WHERE,
    		"KEY"	=> $KEY
    	);
    	$line=$this->GET("standard/MAX",$ARGS);
    	return $line['max'];
    }
    
    // -------------------------------------------------------------------
    // COUNTメソッド
    // 引数
    //   $TABLE : テーブル名
    //	 $WHERE : 変更条件の配列（SELECT同様）
    // 戻り値
    //   ARRAY　：　指定属性の最大値
    // -------------------------------------------------------------------
    public function COUNT($TABLE,$WHERE){
    	$ARGS=array(
    		"TABLE" => $TABLE,
    		"WHERE"	=> $WHERE
    	);
    	$line=$this->GET("standard/COUNT",$ARGS);
    	return $line['count'];
    }
  }
?>