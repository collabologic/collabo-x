<?php
    require_once "constant.php";
    require_once DIR_FW."/fw_common.php";
    require_once DIR_FW."/smarty/Smarty.class.php";

    // -------------------------------------------------------------------
    // ビューコレクションオブジェクトの基底クラス
    // -------------------------------------------------------------------
    abstract class abstract_views {
        // Smartyインスタンス
        var $smarty = "";
        // 呼出しもとコントローラ名
        var $controller = "";

        // -------------------------------------------------------------------
        // コンストラクタ
        // 引数：
        //   $controller：ビューの所属するコントローラの名称
        // -------------------------------------------------------------------
        public function __construct($controller){
            $this -> smarty = new Smarty();           
            $this -> smarty -> template_dir = DIR_DESIGN."/template/";
            $this -> smarty -> compile_dir = DIR_DESIGN."/template_c/";
            $this -> smarty -> config_dir = DIR_DESIGN."/config/";
            $this -> smarty -> cache_dir = DIR_DESIGN."/cache/";
            $this -> controller = $controller;
            $this -> smarty -> php_handling=SMARTY_PHP_ALLOW;
            $this -> smarty -> register_modifier('mb_truncate', array($this,'mb_truncate'));
            $this -> smarty -> register_function('geturl',array($this,'geturl'));
            $this -> smarty -> register_function('assign_select',array($this,'assign_select'));
            $this -> smarty -> register_function('assign_find',array($this,'assign_find'));
            $this -> smarty -> register_modifier('cutting',array($this,'cutting'));
            $this -> smarty -> register_function('assign_find_byid',array($this,'assign_find_byid'));
            $this -> smarty -> register_function('assign_constants_list',array($this,'assign_constants_list'));
            $this -> smarty -> register_function('assign_constants_one',array($this,'assign_constants_one'));
            $this -> smarty -> register_function('assign_pager',array($this,'assign_pager'));
            $this -> smarty -> register_function('print_constants_one',array($this,'print_constants_one'));
            $this -> smarty -> register_function('pagecomment',array($this,'pagecomment'));
			$this -> smarty -> register_function('assign_SQL',array($this,'assign_SQL'));
			$this -> smarty -> register_function('assign_pager_SQL',array($this,'assign_pager_SQL'));
            $this -> smarty -> register_function('print_r', array($this,'print_r'));
            $this -> smarty -> register_function('date_select', array($this,'date_select'));
            $this -> smarty -> register_function('dselect2date', array($this,'dselect2date'));
            $this -> smarty -> register_function('array_reverse', array($this,'array_reverse'));
            $this -> smarty -> register_function('assign_array_count',array($this.'assign_array_count'));
            $this -> smarty -> register_function('print_array_count',array($this.'print_array_count'));
			$this -> smarty ->register_outputfilter(array($this,'utf2sjis'));
			$this -> smarty -> left_delimiter = '{/';
			$this -> smarty -> right_delimiter = '/}';
        }

        // -------------------------------------------------------------------
        // Smarty拡張関数（日付セレクトボックス値を日付に変換）
        // 引数：
        //   $ARGS   : レンダリングに使う値の配列（ハッシュ）
        // -------------------------------------------------------------------
       	public function dselect2date($params,&$smarty){
       		$month=array('01','02','03','04','05','06','07','08','09','10','11','12');
        	$days=array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
        	$this->smarty->assign($params['name'],$params['year']."/".$params['month']."/".$params['day']);
       	}

        // -------------------------------------------------------------------
        // Smarty拡張関数（日付セレクトボックス）
        // 引数：
        //   $ARGS   : レンダリングに使う値の配列（ハッシュ）
        // -------------------------------------------------------------------
       	public function date_select($params,&$smarty){
        	$years = array();
       		for( $i=$params['ystart']; $i<=$params['yend']; $i++){
        		array_push($years,$i);
        	}
        	if( !isset($params['default']) || $params['default']=="NOW"){
        		$params['default']=date("Y/m/d");
        	}

        	$month=array('01','02','03','04','05','06','07','08','09','10','11','12');
        	$days=array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
        	$this->smarty->assign("years",$years);
        	$this->smarty->assign("month",$month);
        	$this->smarty->assign("days",$days);
        	$this->smarty->assign("yname",$params['name']."[y]");
        	$this->smarty->assign("mname",$params['name']."[m]");
        	$this->smarty->assign("dname",$params['name']."[d]");
      	    $this->smarty->assign("default_y",substr($params['default'],0,4));
      	    $this->smarty->assign("default_m",substr($params['default'],5,2));
        	$this->smarty->assign("default_d",substr($params['default'],8,2));
        	return $this->smarty->fetch("common/date_select.tpl");
       	}
        // -------------------------------------------------------------------
        // AutoTemplateのビューメソッド
        // 引数：
        //   $ARGS   : レンダリングに使う値の配列（ハッシュ）
        // -------------------------------------------------------------------
        public function at(&$params){
        	$templ = $params['PARAM']['TEMPLATE'];
        	$params['PARAM']['action']=$params['GET']['action'];
        	unset($params['GET']['controller']);
        	unset($params['GET']['action']);
        	$this->render_template($templ,$params);
        }

        // -------------------------------------------------------------------
        // テンプレートのレンダリング
        // 引数：
        //   $template：テンプレートのファイル名
        //   $ARGS   : レンダリングに使う値の配列（ハッシュ）
        // 例外：
        //   エラー終了"Can't renderT" レンダリング失敗
        // -------------------------------------------------------------------
        protected function render_template($template,$ARGS){
            //必ず必要なパラメータをセット
            $ARGS['base_addr'] = BASE_ADDR;
            $ARGS['ssl_base_addr'] = SSL_BASE_ADDR;
            $ARGS['controller'] = $this->controller;

            foreach ($ARGS as $key => $value){
                $this-> smarty -> assign ($key,$value);
            }
            $this -> smarty -> display($template.".tpl");
        }

            // -------------------------------------------------------------------
        // Smartyプラグイン関数　テスト用PRINT_R
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function print_r($params,&$smarty){
			return "<pre>".print_r($params['ARRAY'],true)."</pre>";
        }

        // -------------------------------------------------------------------
        // Smartyプラグイン関数　URL合成
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function geturl($params,&$smarty){
            if(empty($params['controller'])){
                return BASE_ADDR."/".$this->controller."/".$params['action']."?guid=on&";
				//return BASE_ADDR."/?controller=".$this->controller."&action=".$params['action']."&";
            } else {
                return BASE_ADDR."/".$params['controller']."/".$params['action']."?guid=on&";
				//return BASE_ADDR."/?controller=".$params['controller']."&action=".$params['action']."&";
            }
        }

    // -------------------------------------------------------------------
        // Smartyプラグイン関数　データベースアサイン（任意のSQL）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_SQL($params,&$smarty){
        	require_once DIR_FW."/basic_dao.php";
            $dao = new basic_dao();

	        if(isset($params['DATA']) && !is_array($params['DATA'])){
        		$params['DATA']=eval("return ".$params['DATA']);
	        }
            if(!$params['DATA']){
            	$params['DATA']=array();
            }
            $res = $dao->GETLIST($params['SQL'],$params['DATA']);
            $smarty->assign($params['NAME'],$res);
            return "";
        }

        // -------------------------------------------------------------------
        // Smartyプラグイン関数　データベースアサイン（SELECT）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_select($params,&$smarty){
        	if(isset($params['WHERE']) && !is_array($params['WHERE'])){
        		$params['WHERE']=eval("return ".$params['WHERE']);
        	}
        	if(isset($params['WHERE_VLIST'])){
        		if(isset($params['WHERE_VLIST']) && !is_array($params['WHERE_VLIST'])){
	        		$params['WHERE_VLIST']=eval("return ".$params['WHERE_VLIST']);
        		}
        		$params['WHERE']=array_cutout($params['WHERE'],$params['WHERE_VLIST']);
        	}
        	if(is_array($params['ASCDESC'])){
        		$params['ASCDESC']=$params['ASCDESC']['ASCDESC'];
        	}
        	if(is_array($params['ORDER'])){
        		$params['ORDER']=$params['ORDER']['ORDER'];
        	}
        	if(is_array($params['LIMIT'])){
        		$params['LIMIT']=$params['LIMIT']['LIMIT'];
        	}
	        if(is_array($params['OFFSET'])){
        		$params['OFFSET']=$params['OFFSET']['OFFSET'];
        	}
        	require_once DIR_FW."/standard_dao.php";
            $sdao = new standard_dao();
            $res = $sdao->SELECT($params['VLIST'],$params['TABLE'],$params['WHERE'],$params['ORDER'],$params['ASCDESC'],$params['LIMIT'],$params['OFFSET']);
            $smarty->assign($params['NAME'],$res);
            return "";
        }

        // -------------------------------------------------------------------
        // Smartyプラグイン関数　データベースアサイン（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_FIND($params,&$smarty){
      		if(isset($params['WHERE']) && !is_array($params['WHERE'])){
        		$params['WHERE']=eval("return ".$params['WHERE']);
        	}
        	if(isset($params['WHERE_VLIST'])){
        		if(isset($params['WHERE_VLIST']) && !is_array($params['WHERE_VLIST'])){
	        		$params['WHERE_VLIST']=eval("return ".$params['WHERE_VLIST']);
        		}
        		$params['WHERE']=array_cutout($params['WHERE'],$params['WHERE_VLIST']);
        	}
		    if(isset($params['ORDER']) && !is_array($params['ORDER'])){
        		$params['ORDER']=eval("return ".$params['ORDER']);
		    }
            if(isset($params['ORDER_VLIST']) && !is_array($params['ORDER_VLIST'])){
	        		$params['ORDER_VLIST']=eval("return ".$params['ORDER_VLIST']);
        	}
	        if(isset($params['ORDER']) && isset($params['ORDER_VLIST'])){
        		if(count(array_cutout($params['ORDER'],$params['ORDER_VLIST']))){
		           	$params['ORDER']=array(array_cutout($params['ORDER'],$params['ORDER_VLIST']));
        		}
        	}
        	        	require_once DIR_FW."/standard_dao.php";
            $sdao = new standard_dao();
            $res = $sdao->FIND($params['VLIST'],$params['TABLE'],$params['WHERE'],$params['ORDER']);
            $smarty->assign($params['NAME'],$res);
            return "";
        }

        // -------------------------------------------------------------------
        // Smartyプラグイン修飾子　配列から指定したキーの値のみを抜き出す
        // 引数：
        //   $SRC：元の配列
        //   $targets: 取り出すキーをスペース区切りにしたもの
        // -------------------------------------------------------------------
        public function cutting($SRC,$TARGETS){
        	$array = split(" ",$TARGETS);
        	return array_cutout($SRC,$array);
        }

        // -------------------------------------------------------------------
        // Smartyプラグイン関数　簡易なデータのアサイン（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_find_byid($params,&$smarty){
        	if( isset($params['IDNAME'])){
	        	$params['WHERE'] = array($params['IDNAME']=>$params['ID']);
        	}else{
        		$params['WHERE'] = array('ID'=>$params['ID']);
        	}
        	require_once DIR_FW."/standard_dao.php";
            $sdao = new standard_dao();
            $res = $sdao->FIND(array(),$params['TABLE'],$params['WHERE'],array());
            $smarty->assign($params['NAME'],$res);
            return "";
        }

	    // -------------------------------------------------------------------
        // Smartyプラグイン関数　CONSTANTSオブジェクトのアサイン（SELECT）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_constants_list($params,&$smarty){
        	require_once DIR_APP."/app_constants.php";
        	$res=CONSTANT::${$params['LIST']};
            $smarty->assign($params['NAME'],$res);
            return "";
        }
            // -------------------------------------------------------------------
        // Smartyプラグイン関数　配列件数修飾子
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_array_count($params,&$smarty){
        	if( is_array($params['ARRAY']) ){
				$smarty->assign($params['NAME'],count($params['ARRAY']));
			}else{
				$smarty->assign($params['NAME'],"ERROR");
			}
			return "";
        }
    // -------------------------------------------------------------------
        // Smartyプラグイン関数　配列件数修飾子
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function print_array_count($params,&$smarty){
      return "TST";
        	if( is_array($params['ARRAY']) ){
				return "aaaa".count($params['ARRAY']);
			}else{
				return "ERROR";
			}

        }
    	// -------------------------------------------------------------------
        // Smartyプラグイン関数　CONSTANTSオブジェクトのアサイン（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_constants_one($params,&$smarty){
        	require_once DIR_APP."/app_constants.php";
        	$res=CONSTANT::${$params['LIST']}[$params['KEY']];
            $smarty->assign($params['NAME'],$res);
            return "";
        }

        // -------------------------------------------------------------------
        // Smartyプラグイン関数　ページャオブジェクトのアサイン（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_pager($params,&$smarty){
        	if(isset($params['WHERE']) && !is_array($params['WHERE'])){
        		$params['WHERE']=eval("return ".$params['WHERE']);
        	}
        	if(isset($params['WHERE_VLIST'])){
        		if(isset($params['WHERE_VLIST']) && !is_array($params['WHERE_VLIST'])){
	        		$params['WHERE_VLIST']=eval("return ".$params['WHERE_VLIST']);
        		}
        		$params['WHERE']=array_cutout($params['WHERE'],$params['WHERE_VLIST']);
        	}
	        if(is_array($params['OFFSET'])){
        		$params['OFFSET']=$params['OFFSET']['OFFSET'];
        	}
        	if(!isset($params['OFFSET'])){
        		$params['OFFSET']=0;
        	}
	        if(is_array($params['LIMIT'])){
        		$params['LIMIT']=$params['LIMIT']['LIMIT'];
        	}
        	if(!isset($params['LIMIT'])){
        		$params['LIMIT']=5;
        	}
        	require_once DIR_FW."/standard_dao.php";
            require_once DIR_LIB."/pgng_cnsl_gnrtr.php";
        	$sdao = new standard_dao();
            $count = $sdao->COUNT($params['TABLE'],$params['WHERE']);
            $pgng = new pgng_cnsl_gnrtr();
			$res = $pgng->getPgngCnsl($count,$params['LIMIT'],$params['OFFSET']);
            $smarty->assign($params['NAME'],$res);
            $smarty->assign($params['NAME']."_count",$count);
            return "";
        }

		// -------------------------------------------------------------------
		// Smartyプラグイン関数　ページャオブジェクトのアサイン（SQL）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function assign_pager_SQL($params,&$smarty){
        	if(!isset($params['DATA']['OFFSET'])){
        		$params['DATA']['OFFSET']=0;
        	}
        	if(!isset($params['DATA']['LIMIT'])){
        		$params['DATA']['LIMIT']=5;
        	}
        	require_once DIR_FW."/basic_dao.php";
            require_once DIR_LIB."/pgng_cnsl_gnrtr.php";
        	$sdao = new basic_dao();
            $count = $sdao->GET($params['SQL'],$params['DATA']);
            $pgng = new pgng_cnsl_gnrtr();
			$res = $pgng->getPgngCnsl($count['count'],$params['DATA']['LIMIT'],$params['DATA']['OFFSET']);
            $smarty->assign($params['NAME'],$res);
            $smarty->assign($params['NAME']."_count",$count['count']);
            return "";
		}
    	// -------------------------------------------------------------------
        // Smartyプラグイン関数　CONSTANTSオブジェクトの出力（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function print_constants_one($params,&$smarty){
        	require_once DIR_APP."/app_constants.php";
        	$res=CONSTANT::${$params['LIST']}[$params['KEY']];
            return $res;
        }
        // -------------------------------------------------------------------
        // Smartyプラグイン関数　配列の反転
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function array_reverse($params,&$smarty){
        	$data=array_reverse($params['value']);
        	$smarty->assign($params['var'],$data);
        }

    	// -------------------------------------------------------------------
        // Smartyプラグイン関数　マルチバイトトランケイト修飾子（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function mb_truncate($string, $length = 80, $etc = '...', $flag) {
 			if ($length == 0) {return '';}
    		if (strlen($string) > $length) {
				mb_language('Japanese');
				mb_internal_encoding('UTF-8');
				$string=urlencode($string);
    			$string=mb_substr($string, 0, $length*4,'UTF-8');
    			$string=urldecode($string);
    			$string=preg_replace('/\%[0-9a-zA-Z]*?$/',"",$string);
    			$string=preg_replace('/&[0-9a-zA-Z]*?$/',"",$string);
		        return $string.$etc;
    		} else {
        		return $string;
    		}
		}

    // -------------------------------------------------------------------
        // Smartyプラグイン関数　マルチバイトトランケイト修飾子（FIND）
        // 引数：
        //   $params：パラメータの連想配列
        //   &$smarty : Smartyインスタンスへの参照
        // -------------------------------------------------------------------
        public function utf2sjis($tpl_output,&$smarty) {
			mb_language('Japanese');
			mb_internal_encoding('UTF-8');
			return mb_convert_encoding($tpl_output,'SJIS','UTF-8');
		}

    }
?>
