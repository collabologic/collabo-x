<?php
  require_once DIR_FW."/smarty/Smarty.class.php";
  require_once DIR_LIB."/regstr.php";  
  // -------------------------------------------------------------------
  // データベースアクセスオブジェクトの基本クラス
  // 使い方
  //   EXEC、FIND、SELECTの関数にテンプレート名と引数を渡す。
  // -------------------------------------------------------------------
  class basic_dao {
  	// DBアクセスオブジェクト
	var $DB = '';

    // -------------------------------------------------------------------
    // SQLの生成
    // 引数
    //   $tmpl : テンプレート名
    //   $ARGS : 引き渡しパラメータの配列
    // 戻り値
    //   SQL文
    // -------------------------------------------------------------------
    protected function query_generate($tmpl,$ARGS){
    	// Smartyテンプレートとして対応
    	$this->smarty->clear_all_assign();
    	foreach ($ARGS as $key => $value){
    		//$value=preg_replace('/[^\']\'/',"\'",$value);
           $this-> smarty -> assign ($key,$value);
        }
        // ソートキーとリミットオフセット、降順昇順を削除して、一覧データを登録
        if( isset($ARGS['srt_key']) ){
        	unset($ARGS['srt_key']);
        }
    	if( isset($ARGS['limit']) ){
         	unset($ARGS['limit']);
        }
    	if( isset($ARGS['ofset']) ){
        	unset($ARGS['ofset']);
        }
    	if( isset($ARGS['ascdesc']) ){
        	unset($ARGS['ascdesc']);
        }
        $this->smarty->assign('ARGS',$ARGS);

        $SQL = $this -> smarty -> FETCH($tmpl.".sql");
        return $SQL;
    }

    // -------------------------------------------------------------------
    // データベースコネクションの引き渡し
    // 引数
    //   なし
    // 戻り値
    //  DBオブジェクト
    // -------------------------------------------------------------------    
    public function getDBConn(){
    	return $this->DB;
    }

    
    // -------------------------------------------------------------------
    // コンストラクタ
    // 引数
    //   $DB : データベースアクセスオブジェクト（必須ではない）̾
    // 戻り値
    //  DAOオブジェクト
    // -------------------------------------------------------------------    
  	public function __construct($db = null){
  		if( !$db){
	        $this->DB = mysql_connect(DB_HOST,DB_USER,DB_PASSWD);
  		} else {
  			$this->DB = $db;
  		}
  		if (! $this->DB){
  			die("Can't connect:".mysql_connect_error());
  		}
  		if (! mysql_select_db ( DB_DBNAME ) ){
  			die("Can't select database:".mysql_error($this->DB));
  		}

		$query = mysql_query("SET NAMES utf8");
  		$this -> smarty = new Smarty();
  		$this -> smarty -> template_dir = DIR_DBMODULES."/template/";
        $this -> smarty -> compile_dir = DIR_DBMODULES."/template_c/";
        $this -> smarty -> config_dir = DIR_DESIGN."/config/";
        $this -> smarty -> cache_dir = DIR_DESIGN."/cache/";
        
        $this -> smarty -> register_function('reg_str',array($this,'reg_str'));
  	}

 	
  	// -------------------------------------------------------------------
    // データ操作メソッド
    // 引数
    //	 $tmpl	 : テンプレート名
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   影響行数
    // -------------------------------------------------------------------
  	public function EXC($tmpl,$ARGS){
  		$SQL = $this->query_generate($tmpl,$ARGS);
  		$query = mysql_query($SQL);
  	    if($query === FALSE){
  			die("Can't INSERT:".mysql_error($this->DB)."  query:".$SQL);
  		}
  		return $query;
  	}
  	// -------------------------------------------------------------------
    // GETメソッド
    // 引数
    //	 $tmpl	 : テンプレート名
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   一行分の検索結果
    // -------------------------------------------------------------------    
    public function GET($tmpl,$ARGS){
    	$SQL = $this->query_generate($tmpl,$ARGS);
  		
  		$query = mysql_query($SQL);
        if($query === FALSE) {
  		  	die("Can't FIND:".mysql_error($this->DB)."  query:".$SQL);
  		}    	
  		$res = mysql_fetch_array($query);
  		if( is_array($res) ) {
	  		return $res;
  		}else{
  			$res;
  		}
    }

  	// -------------------------------------------------------------------
    // GETLISTメソッド
    // 引数
    //	 $tmpl	 : テンプレート名
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------    
    public function GETLIST($tmpl,$ARGS){
        $SQL = $this->query_generate($tmpl,$ARGS);

  		$query = mysql_query($SQL);
        if($query === FALSE) {
  		  	die("Can't SELECT:".mysql_error($this->DB)."  query:".$SQL);
  		}
  		$array = array();
  		while( $obj = mysql_fetch_array($query) ){
  			array_push($array,$obj);
  		}
  		return $array;
    }
    
  	public function reg_str($params,&$smarty){
	  	return RegStr($params,$smarty);
 	}
 }
?>