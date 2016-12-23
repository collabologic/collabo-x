<?php
require_once DIR_FW."/abstract_controller.php";
require_once DIR_APP."/app_constants.php";

class TEST_controller extends abstract_controller{
	var $default_view = 'index';
    protected function execute(&$params){
    	header("Content-Type: text/html; charset=Shift_JIS");
    	// 入力のUTF8変換
    	mb_convert_variables("UTF8","SJIS",$params['POST']);
    	// インフォメーション配列のセット
    	if(!isset($params['PARAM']['INF'])){
			 $params['PARAM']['INF']=array();
		}
    	if(!isset($params['PARAM']['PGINF'])){
			 $params['PARAM']['PGINF']=array();
		}

    	// アクション名に対応するアクションシナリオ関数の存在を確認
		if( !isset($params['GET']['action']) ){
			$params['GET']['action']=$this->default_view;
		}
    	if( method_exists($this,$params['GET']['action']) ){

    		// アクション名に対応するアクションシナリオ関数を呼出し
    		$this->{$params['GET']['action']}($params);
    		return true;
    	}
        return false;
    }

}
?>