<?php
	require_once DIR_FW."/abstract_validator.php";
	
	// -------------------------------------------------------------------
    // ログインのバリデータクラス
    // -------------------------------------------------------------------
	class login_validator extends abstract_validator {
		// 変数名
		var $ARGNAMES = array(
			'mailaddr' => 'メールアドレス',
		    'passwd' => 'パスワード'
		);
		
		// チェック内容
		var $CHECKLIST = array(
			array('NOTNULL','mailaddr'),
			array('NOTNULL','passwd'),
			array('LENGTH','mailaddr',0,256),
			array('LENGTH','passwd',0,16),
			array('MAILADDR','mailaddr'),
			array('BYTE1','passwd')
		);
	}
?>