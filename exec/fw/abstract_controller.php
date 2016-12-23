<?php
    require_once "constant.php";
    // -------------------------------------------------------------------
    // コントローラオブジェクトの基底クラス
    // 使い方：
    //   EXTENDしたクラスに下記のを追記すること
    //    　protected function execute($array_post,$array_get,$array_session)
    //       引数：
    //         $array_post : クライアントから送られたPOST情報配列
    //         $array_get  : クライアントから送られたGET（クエリ）情報配列
    //         $array_session : セッション情報の配列
    //       戻り値：
    //         アクションを処理した場合はTRUE処理しなかった場合はFALSE
    //      ※デフォルト処理のみを行う場合はfalseをリターンするだけのものを用意すること。
    //     $default_view アクション指定が不正または無かった場合に実行するビューメソッド名
    // -------------------------------------------------------------------
    abstract class abstract_controller {
        // -------------------------------------------------------------------
        // 個別アクションハンドラの仮想関数
        // 引数：
        //  $params : 'GET','POST','SESSION'をキーとして各パラメータを保管した配列
        // 戻り値：
        //  アクションを処理した場合はTRUE処理しなかった場合はFALSE
        //  ※デフォルト処理のみを行う場合はfalseをリターンするだけのものを用意すること。
        // -------------------------------------------------------------------
        abstract protected function execute(&$params);
                
        // -------------------------------------------------------------------
        // 基本アクションハンドラ
        // 引数：
        //  $params : 'GET','POST','SESSION'をキーとして各パラメータを保管した配列
        // -------------------------------------------------------------------
        public function call(&$params){
            // 個別コントローラの処理を実行
            if (!$this->execute($params)){
                $this->exec_view($params['GET']['action'],$params);
            }
        }
        // -------------------------------------------------------------------
        // ビューメソッド実行関数
        // 引数：
        //  $method : コールするビューメソッド名
        //  $params : 'GET','POST','SESSION','PARAM'をキーとして各パラメータを保管した配列
        // -------------------------------------------------------------------
        protected function exec_view( $method, &$params){
			if( !isset($params['GET']['action']) ){
				//アクション名がセットされていなければデフォルトアクションをセット
				$params['GET']['action'] = $this->default_view;
			}
        	if( file_exists(DIR_APP."/".$params['GET']['controller']."/views/".$method."_view.php")){
                // 個別ビュークラスがあった場合
                require_once DIR_APP."/".$params['GET']['controller']."/views/".$method."_view.php";
                $class = $method."_view";
                $action = new $class($params['GET']['controller']);
                $action -> display($params);                
            }elseif( file_exists(DIR_APP."/".$params['GET']['controller']."/".$params['GET']['controller']."_views.php")){
                // 個別ビューがなかった場合、メソッドコレクションからaction名に対応したビュークラスを開く
                require_once DIR_APP."/".$params['GET']['controller']."/".$params['GET']['controller']."_views.php";
                $class = $params['GET']['controller']."_views";
                $actions = new $class($params['GET']['controller']);
                if ( method_exists($actions,$method) ){
                    // ビューコレクションにメソッドがあった場合
                    $actions->{$method}($params);
                } elseif( file_exists(DIR_DESIGN."/template/".$params['GET']['controller']."/".$method.".tpl")){
                	// テンプレートディレクトリのコントローラ名のディレクトリに同名のテンプレートがあった場合
                	$params['PARAM']['TEMPLATE']=$params['GET']['controller']."/".$method;
                	$actions->at($params);
                } elseif( file_exists(DIR_DESIGN."/template/at/".$method.".tpl")){
                	// テンプレートディレクトリのオートテンプレート専用ディレクトリに同名のテンプレートがあった場合
                	$params['PARAM']['TEMPLATE']="at/".$method;
                	$actions->at($params);
                } elseif( 	file_exists(DIR_DESIGN."/template/".$method.".tpl")){
					// テンプレートディレクトリ直下に同名のテンプレートがあった場合
                	$params['PARAM']['TEMPLATE']=$method;
                	$actions->at($params);
                }else {
                    //ビューコレクションにメソッドがなかった場合
                    trigger_error("BAD_REQUEST",E_USER_ERROR);
                }
            } else {
                // 個別クラスもメソッドコレクションもなかった場合
                trigger_error("BAD_REQUEST",E_USER_ERROR);
            }
        }
        // -------------------------------------------------------------------
        // オペレーションメソッド実行関数
        // 引数：
        //  $method : コールするオペレーションメソッド名
        //  $params : 'GET','POST','SESSION'をキーとして各パラメータを保管した配列
        //            オペレーションメソッド内で'PARAM'を追加することがある。
        // 戻り値：
        //  結果スイッチ
        // -------------------------------------------------------------------
        protected function exec_operation( $method, &$params){
            if ( file_exists(DIR_APP."/".$params['GET']['controller']."/operations/".$method."_operation.php")){
                // 個別オペレーションクラスがあった場合
                require_once DIR_APP."/".$params['GET']['controller']."/operations/".$method."_operation.php";
                $class = $method."_operation";
                $action = new $class($params['GET']['controller']);
                return $action -> exec($params);                
            }elseif( file_exists(DIR_APP."/".$params['GET']['controller']."/".$params['GET']['controller']."_operations.php")){
                // 個別オペレーションがなかった場合、メソッドコレクションからaction名に対応したオペレーションクラスを開く
                require_once DIR_APP."/".$params['GET']['controller']."/".$params['GET']['controller']."_operations.php";
                $class = $params['GET']['controller']."_operations";
                $actions = new $class($params['GET']['controller']);
                if ( method_exists($actions,$method) ){
                    // オペレーションコレクションにメソッドがあった場合
                	return $actions->{$method}($params);
                } else {
                    // オペレーションコレクションにメソッドがなかった場合
                    return false;
                }
            } else {
                // 個別クラスもメソッドコレクションもなかった場合
                return false;
            }
        }
    }
?>