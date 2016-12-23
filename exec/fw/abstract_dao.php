<?php
  require_once DIR_FW."/smarty/Smarty.class.php";
  		
  // -------------------------------------------------------------------
  // データベースアクセスオブジェクトの規定クラス
  // 使い方
  //   EXTENDしたクラスで下記を追加
  //    $DAONAME
  //       DAOの名称同名のディレクトリをDAOディレクトリ配下に作成し、SQLテンプレートを置くこと
  // -------------------------------------------------------------------
  abstract class abstract_dao {
  	// DBアクセスオブジェクト
	var $DB = '';

    // -------------------------------------------------------------------
    // SQLの生成
    // 引数
    //   $func : メソッド名(INSERT,DELETE,UPDATE,FIND,SELECT,GETMAX)
    //   $ARGS : 引き渡しパラメータの配列
    // 戻り値
    //   SQL文
    // -------------------------------------------------------------------
    protected function query_generate($func,$ARGS){
    	// Smartyテンプレートとして対応
    	foreach ($ARGS as $key => $value){
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
        
        $SQL = $this -> smarty -> FETCH($this->DAONAME."/".$func.".sql");
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
		mysql_query("SET NAMES utf8");
  		$this -> smarty = new Smarty();
  		$this -> smarty -> template_dir = DIR_DBMODULES."/template/";
        $this -> smarty -> compile_dir = DIR_DBMODULES."/template_c/";
        $this -> smarty -> config_dir = DIR_DESIGN."/config/";
        $this -> smarty -> cache_dir = DIR_DESIGN."/cache/";
        $this -> smarty -> register_function('dateadd',array($this,'dateadd'));
        $this -> smarty -> register_function('datesub',array($this,'datesub'));
		$this -> smarty -> register_function('datesub_pre',array($this,'datesub_pre'));
		$this -> smarty -> register_function('datesub_aftr',array($this,'datesub_aftr'));
  	
  	}
  	
  	// -------------------------------------------------------------------
    // BEGIN TRANSACTIONメソッド
    // 引数
    //   なし
    // 戻り値
    //   なし
    // -------------------------------------------------------------------
  	public function BEGIN(){
  		$query = mysql_query("start transaction;");
  	    if($query === FALSE){
  			die("Can't START TRANSACTION:".mysql_error($this->DB)."  query:".$SQL);
  		}
  	}
  	
  	// -------------------------------------------------------------------
    // ROLLBACKメソッド
    // 引数
    //   なし
    // 戻り値
    //   なし
    // -------------------------------------------------------------------
  	public function ROLLBACK(){
  		$query = mysql_query("ROLLBACK;");
  	    if($query === FALSE){
  			die("Can't ROLLBACK:".mysql_error($this->DB)."  query:".$SQL);
  		}
  	}
  	
  // -------------------------------------------------------------------
    // COMMITメソッド
    // 引数
    //   なし
    // 戻り値
    //   なし
    // -------------------------------------------------------------------
  	public function COMMIT(){
  		$query = mysql_query("COMMIT;");
  	    if($query === FALSE){
  			die("Can't COMMIT:".mysql_error($this->DB)."  query:".$SQL);
  		}
  	}
  	
    // -------------------------------------------------------------------
    // 楽観的排他SELECTメソッド
    // 引数
    //   &$SESS:セッションデータへの参照
    //   $id : 対象テーブルのID 
    // 戻り値
    //   なし
    // -------------------------------------------------------------------
  	public function SELECT_FOR_UPDATE(&$SESS, $id){
  		$query = mysql_query("SELECT_FOR_UPDATE;",$id);
  	    if($query === FALSE){
  			die("Can't SELECT_FOR_UPDATE:".mysql_error($this->DB)."  query:".$SQL);
  		}
  		$result = mysql_fetch_array($query);
  		$SESS["LCK_".$DAONAME][$id]= $result['updt_cnt'];
  	}
  	
  // -------------------------------------------------------------------
    // 楽観的排他CHECKメソッド
    // 引数
    //   &$SESS:セッションデータへの参照
    //   $id : 対象テーブルのID 
    // 戻り値
    //   TRUE : 変更可能
    //   FALSE: 排他中
    // -------------------------------------------------------------------
  	public function CHECK_FOR_UPDATE(&$SESS, $id){
  		$query = mysql_query("CHECK_FOR_UPDATE;",$id);
  	    if($query === FALSE){
  			die("Can't CHECK_FOR_UPDATE:".mysql_error($this->DB)."  query:".$SQL);
  		}
  		$result = mysql_fetch_array($query);
  		if( $SESS["LCK_".$DAONAME][$id] && $SESS["LCK_".$DAONAME][$id] < $result['updt_cnt'] ){
  			return false;
  		}
  		return true;
  	}
  	
  	// -------------------------------------------------------------------
    // INSERT用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------
  	public function INSERT($ARGS){
  		$SQL = $this->query_generate("INSERT",$ARGS);
  		$query = mysql_query($SQL);
  	    if($query === FALSE){
  			die("Can't INSERT:".mysql_error($this->DB)."  query:".$SQL);
  		}
  		return true;
  	}

  	// -------------------------------------------------------------------
    // DELETE用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------
   	public function DELETE($ARGS){
		$SQL = $this->query_generate("DELETE",$ARGS);
  		$query = mysql_query($SQL);
  	    if($query === FALSE){
  			die("Can't DELETE:".mysql_error($this->DB)."  query:".$SQL);
  		}  		
  		return true;
  	}

  	// -------------------------------------------------------------------
    // UPDATE用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
  	// -------------------------------------------------------------------
    public function UPDATE($ARGS){
      	$SQL = $this->query_generate("UPDATE",$ARGS);
  		$query = mysql_query($SQL);
  	    if($query === FALSE){
  			die("Can't UPDATE:".mysql_error($this->DB)."  query:".$SQL);
  		}  
  		return true;  	
    }
    
  	// -------------------------------------------------------------------
    // FIND用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------    
    public function FIND($ARGS){
    	$SQL = $this->query_generate("FIND",$ARGS);
  		
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
    // SELECT用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------    
    public function SELECT($ARGS){
        $SQL = $this->query_generate("SELECT",$ARGS);

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
    
  	// -------------------------------------------------------------------
    // GETMAX用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------    
    public function GETMAX($ARGS){
    	$SQL = $this->query_generate("GETMAX",$ARGS);
  		
  		$query = mysql_query($SQL);
        if($query === FALSE) {
  		  	die("Can't GETMAX:".mysql_error($this->DB)."  query:".$SQL);
  		}    	
  		$res= mysql_fetch_array($query);
  		return $res['count'];
    }
  
    
  	// -------------------------------------------------------------------
    // GETNEW用標準メソッド
    // 引数
    //   $ARGS   : 引き渡しパラメータ
    // 戻り値
    //   なし
    // -------------------------------------------------------------------    
    public function GETNEW($ARGS){
    	$SQL = $this->query_generate("GETNEW",$ARGS);
  		
  		$query = mysql_query($SQL);
        if($query === FALSE) {
  		  	die("Can't GETNEW:".mysql_error($this->DB)."  query:".$SQL);
  		}    	
  		$res= mysql_fetch_array($query);
  		return $res['max'];
    }
   
	// -------------------------------------------------------------------
    // Smartyプラグイン関数　日付計算
    // 引数：
    //   $params：パラメータの連想配列
    //   &$smarty : Smartyインスタンスへの参照
    // 戻り値：
    //	日付計算式
    // -------------------------------------------------------------------
    public function dateadd($params,&$smarty){
    	$left = $params['left'];
    	$right = $params['right'];
    	return "DATE_ADD(DATE_ADD($left,INTERVAL DATE_FORMAT($right,'%c') MONTH),INTERVAL DATE_FORMAT($right,'%e') DAY)";
    }
 
  	// -------------------------------------------------------------------
    // Smartyプラグイン関数　日付計算
    // 引数：
    //   $params：パラメータの連想配列
    //   &$smarty : Smartyインスタンスへの参照
    // 戻り値：
    //	日付計算式
    // -------------------------------------------------------------------
    public function datesub($params,&$smarty){
    	$left = $params['left'];
    	$right = $params['right'];
    	return "DATE_SUB(DATE_SUB($left,INTERVAL DATE_FORMAT($right,'%c') MONTH),INTERVAL DATE_FORMAT($right,'%e') DAY)";
    }
    
    	// -------------------------------------------------------------------
    // Smartyプラグイン関数　日付計算
    // 引数：
    //   $params：パラメータの連想配列
    //   &$smarty : Smartyインスタンスへの参照
    // 戻り値：
    //	日付計算式
    // -------------------------------------------------------------------
    public function datesub_pre($params,&$smarty){
    	return "DATE_SUB(DATE_SUB(";
    }

    
    
    	// -------------------------------------------------------------------
    // Smartyプラグイン関数　日付計算
    // 引数：
    //   $params：パラメータの連想配列
    //   &$smarty : Smartyインスタンスへの参照
    // 戻り値：
    //	日付計算式
    // -------------------------------------------------------------------
    public function datesub_aftr($params,&$smarty){
    	$right = $params['right'];
    	return ",INTERVAL DATE_FORMAT($right,'%c') MONTH),INTERVAL DATE_FORMAT($right,'%e') DAY)";
    }
 }
?>