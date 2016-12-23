<?php
// -------------------------------------------------------------------
// エラーハンドラ関数
// 引数：
//   $errorno : エラーレベル
//   $errstr  : エラー種別名（メッセージであることも）
//   $errfile : エラーが発生したファイル名
//   $errline : エラーが発生した行番号
// 戻り値：
//   true : ハンドラ内でエラー処理をした場合
//   false: 通常のエラー処理に遷移させる場合
// 注意：
//   この関数はset_error_handler関数で設定するコールバック関数です。
//   それ以外の用途には使用しないでください。
// -------------------------------------------------------------------
function error_handler($errno,$errstr,$errfile, $errline) {
    switch ($errno) {
        case E_USER_ERROR:
            switch ($errstr) {
                case "NO_LOGIN_METHOD":
                    header("Location: ".BASE_ADDR."/404.html");
                    return true;
                case "BAD_REQUEST":
                    header("Location: ".BASE_ADDR."/404.html");
                    return true;
                default:
                    return false;
            }
        default:
            return false;
    }
}


// -------------------------------------------------------------------
// 二次元配列生成関数
// 引数：
//   $str :　CSV文字列
// 戻り値：
//   成功の場合 : 2次元配列
//   失敗の場合 : false
// -------------------------------------------------------------------
function matrix($str) {
    // 結果配列
    $return = array();
    
    // 改行を削除
    $str = preg_replace('/\n/','',$str);
    
    // ; でSplit
    $lines = split(';',$str);
    
    // 各行の処理
    foreach( $lines as $line ){
        // 各行の両サイドをトリム
        $line = trim($line);
        // 空白のみの行であれば処理を飛ばす
        if( !strlen($line) ) continue;
        // 各行を, でスプリット
        $array = split(',',$line);
        // 各値をトリム
        for( $i = 0 ; $i < count($array) ; $i++){
            $array[$i] = trim($array[$i]);
        }
        // 結果配列にPUSH
        array_push($return,$array);
    }
    return $return;
}

// -------------------------------------------------------------------
// テーブル生成関数
// 引数：
//   $str :　CSV文字列
// 戻り値：
//   成功の場合 : 多次元配列
//   失敗の場合 : false
// -------------------------------------------------------------------
function table($str) {
    // 結果配列
    $return = array();
    
    // 改行を削除
    $str = preg_replace('/^n/','',$str);
    
    // ; でSplit
    $lines = split(';',$str);
    
    // 一行目を取得
    $first = array_shift($lines);
    // タイトル行のの両サイドをトリム
    $first = trim($first);
    // タイトル行行を, でスプリット
    $titles = split(',',$first);
    // タイトルをトリム
    for( $i = 0 ; $i < count($titles) ; $i++ ){
        $titles[$i] = trim($titles[$i]);
    };
    // 各行の処理
    foreach( $lines as $line ){
        // 各行の両サイドをトリム
        $line = trim($line);
        // 空白のみの行であれば処理を飛ばす
        if( !strlen($line) ) continue;
        // 各行を, でスプリット
        $array = split(',',$line);
        // 列数が合うかどうかを確認
        //if( count($array) != count($titles))return false;
        // 名前をつけて保存。
        $table = array();
        for( $i = 0 ; $i < count($array) ; $i++ ){
            $array[$i] = trim($array[$i]);
            $table[$titles[$i]] = $array[$i];
        }
        // 結果配列にPUSH
        array_push($return,$table);
    }

    return $return;
}

// -------------------------------------------------------------------
// テーブル生成関数（縦に項目名が並ぶタイプ）
// 引数：
//   $str :　CSV文字列
// 戻り値：
//   成功の場合 : 多次元配列
//   失敗の場合 : false
// -------------------------------------------------------------------
function htable($str) {
    // 結果配列
    $return = array();
    
    // 改行を削除
    $str = preg_replace('/^n/','',$str);
    
    // ; でSplit
    $lines = split(';',$str);
    
    // 各行を処理
    foreach( $lines as $line ){
        // 空白のみの行であれば処理を飛ばす
        if( !strlen($line) ) continue;
    	// ,でSplit
    	$line_array=split(',',$line);
    	// トリムしてキーにする
    	$key=trim($line_array[0]);
    	for( $i=1 ; $i<count($line_array) ; $i++ ){
    		$return[$key][$i]=trim($line_array[$i]);
    	}
    }
    return $return;
}

// -------------------------------------------------------------------
// テーブル生成関数（縦->横の順で項目名が並ぶタイプ）
// 引数：
//   $str :　CSV文字列
// 戻り値：
//   成功の場合 : 多次元配列
//   失敗の場合 : false
// -------------------------------------------------------------------
function btable($str) {
    // 結果配列
    $return = array();
    
    // 改行を削除
    $str = preg_replace('/^n/','',$str);
    
    // ; でSplit
    $lines = split(';',$str);

    // 一行目を取得
    $first = array_shift($lines);
    // タイトル行のの両サイドをトリム
    $first = trim($first);
    // タイトル行を, でスプリット
    $titles = split(',',$first);
    // タイトルをトリム
    for( $i = 1 ; $i < count($titles) ; $i++ ){
        $titles[$i] = trim($titles[$i]);
    };
    
    // 各行を処理
    foreach( $lines as $line ){
        // 空白のみの行であれば処理を飛ばす
        if( !strlen($line) ) continue;
    	// ,でSplit
    	$line_array=split(',',$line);
    	// トリムしてキーにする
    	$key=trim($line_array[0]);
    	for( $i=1 ; $i<count($line_array) ; $i++ ){
    		$return[$key][$titles[$i]]=trim($line_array[$i]);
    	}
    }
    return $return;
}

// -------------------------------------------------------------------
// フロー配列配列生成関数
// 引数：
//   $str :　フロー文字列
// 戻り値：
//   成功の場合 : 2次元配列
//   失敗の場合 : false
// -------------------------------------------------------------------
function flow($str) {
    // 結果配列
    $return = array();
    
    // 改行を削除
    $str = preg_replace('/\n/','',$str);
    
    // ; でSplit
    $lines = split(';',$str);
    
    // 各行の処理
    foreach( $lines as $line ){
        // 単独で配置された（|-以外の）の｜（パイプ）を削除
        $line = preg_replace('/\|[^\-]/','',$line);
        // 各行の両サイドをトリム
        $line = trim($line);
        // 空白のみの行であれば処理を飛ばす
        if( !strlen($line) ) continue;
        // 各行を, でスプリット
        $array = split('--',$line);
        // 各値をトリム
        for( $i = 0 ; $i < count($array) ; $i++){
            $array[$i] = trim($array[$i]);
        }
        // 結果配列にPUSH
        array_push($return,$array);
    }
    return $return;
}

// -------------------------------------------------------------------
// フロー実行関数
// 引数：
//   $flow :　フロー配列
//   $items :  フロー内で用いるパラメータ配列
//   $machines : フロー内で用いる実行オブジェクト配列
// 戻り値：
//   成功の場合 : 最終的なカレントデータ
//   失敗の場合 : false
// -------------------------------------------------------------------
function exec_flow($flow,$items,$machines) {
    // 結果配列
    $return = array();
    // フロー中の値
    $current = "";
    // 一時的なデータ保管配列
    $tempbox = array();
    
    // 各行の処理
    foreach( $flow as $line ){
        // カレントの値を放棄
        unset($current);
        // 各文の処理
        foreach( $line as $context ){
            // |- だった場合
            if( preg_match('/^\|\-(.*)$/',$context)){
                $tokens = split(' ',$context);
                // 条件が真でなければ以降は実行しない
                $left = valueInFlow($tokens[1],$items,$tempbox,$current,false);
                $right = valueInFlow($tokens[3],$items,$tempbox,$current,false);
                eval('$_return = ('.$left.$tokens[2].$right.");");
                if( $_return != 1 ){
                    break;
                } else {
                    // 条件が真なら条件文の後ろの処理をコンテキストとして処理
                    array_shift($tokens);
                    array_shift($tokens);
                    array_shift($tokens);
                    array_shift($tokens);
                    $context = implode(" ",$tokens);
                }
            }
            // 変数だった場合            
            $value = valueInFlow($context,$items,$tempbox,$current);

            if($value != false){
                $current = $value;
                continue;
            //　上記以外だった場合は関数実行として処理  
            }else{
                // 文をトークンに分割
                $tokens = split(' ',$context);
                // オブジェクト名
                $object = $tokens[0];
                // 関数名 
                $method = $tokens[1];
                // パラメータ配列
                $params = array();
                // FUNCだった場合はクラス外のメソッドとして取り扱う
                if($tokens[0] != "FUNC"){
                    // 対応するオブジェクトがマシン配列に対応するか確認
                    if( !array_key_exists($object,$machines)){
                        // エラー処理
                        die("exec_flow: machine name ($object) is not exists.");
                    }
                    // 関数の存在を確認
                    if( !method_exists($object,$method)){
                        // エラー処理
                        die("exec_flow: method name ($method) is not exists.");
                    }
                }
                // 関数を実行
                if( $object == "FUNC" ){

                    $_return = eval('$_return=$method('.$current.");");
                    if($_return)$current = $_return;
                } else {
                    $_return = eval('$_return=$object->$method('.$current.");");
                    if($_return)$current = $_return;
                }
                continue;
            }
        }
    }
    return $current;
}

// local method-------------------------------------------------------

// -------------------------------------------------------------------
// フロー内の変数取得
// 引数：
//   $str :　変数かもしれない文字列
// 戻り値：
//   変数の場合 : 変数の値
//   変数以外の場合 : false
// -------------------------------------------------------------------
function valueInFlow($str,$items,&$tempbox,$current,$input_flg =true){
    // [hoge]だった場合 
    if(preg_match('/^\[(.*)\]$/',$str,$matches)){
        $return = $items[$matches[1]];
        return $return;
    // (hoge)だった場合
    }elseif(preg_match('/^\((.*)\)$/',$str,$matches)){
        // すでに一時データが存在しない場合は値を保管
        if( !isset($tempbox[$matches[1]])){        
            if( $input_flg )$tempbox[$matches[1]] = $current;
        }
        $return = $tempbox[$matches[1]];
        return $return;
    }elseif(preg_match('/^#(.*)#$/',$str,$matches)){
        return $matches[1];
    }
    return false;
}

// -------------------------------------------------------------------
// 指定したキーの要素のみを取り出して新しい配列を返す
// 引数：
//   $src :　元の配列
//	 $targets: 添え字の値の配列
// 戻り値：
//   元の配列から対象のもののみを取り出した配列
// -------------------------------------------------------------------
function array_cutout($src,$targets) {
	$res = array();
	foreach($targets as $target){
		if( !isset($src[$target]) ){
			continue;
		}
		$res[$target]=$src[$target];
	}
	return $res;
}
?>