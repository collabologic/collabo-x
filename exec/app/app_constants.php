<?php
	require_once(DIR_FW."/fw_common.php");
	class CONSTANT{

		// 一般メッセージ
		public static $MSG=array(
			"ERR_LOGIN"=>"ログインに失敗しました",
			"RGSTD"=>"登録しました"
		);

		// バリデータエラーメッセージ
		public static $RSRC_STR=array(
    		'ERR_NOTNULL' => 'は必須項目です',
    		'ERR_LENGTH_TOLONG'  => 'が長過ぎます',
        	'ERR_LENGTH_TOSHORT' => 'が短すぎます',
        	'ERR_ISNUMBER' => 'の値が不正です',
			'ERR_ISNUMBER_SPAN' => 'の値が不正です',
        	'ERR_ISDATETIME' => 'は「YYYY-MM-DD」の形式で入力してください',
    		'ERR_ISSMALLER' => 'が大きすぎます',
    		'ERR_ISLARGER' => 'が小さすぎます',
    		'INVALID_VALIDATOR_METHOD' => 'システムエラーです。不明なメソッドがコールされました。',
    		'ERR_ISMAILADDR' => 'が正しい書式ではありません。',
    		'ERR_ISBYTE1' => 'には全角文字は使えません。',
			'ERR_EQUAL'	 => 'が一致しません',
			'ERR_TEL'	=>	'に半角数字及び-（ハイフン）以外の文字が入力されました。',
			'ERR_KYABAAD_SHORT'	=>'が短すぎます',
			'ERR_KYABAAD_INVALID_DOT'	=>'に不正な.が含まれています',
			'ERR_KYABAAD_INVALID_CHAR'	=>'に不正な文字が含まれています',
			'ERR_KYABAAD_INVALID_USERNAME'	=>	'には使用できない文字列が使われています',
			'ERR_KYABAAD_REGSTED_ADDR'	=>'はすでに使用されています。他のアドレスを選択してください',
			'ERR_HKANA'			=> 'には仮名のみが入力できます。',
			'ERR_INVALID_MOBILEADDR'=>'に有効ではない携帯電話アドレスが入力されました',
        );
                // 都道府県
        public static $PREF_STAY=array(
            '1'	=>	'北海道',
			'2'	=>	'青森県',
			'3'	=>	'岩手県',
			'4'	=>	'宮城県',
			'5'	=>	'秋田県',
			'6'	=>	'山形県',
			'7'	=>	'福島県',
			'8'	=>	'茨城県',
			'9'	=>	'栃木県',
			'10'	=>	'群馬県',
			'11'	=>	'埼玉県',
			'12'	=>	'千葉県',
			'13'	=>	'東京都',
			'14'	=>	'神奈川県',
			'15'	=>	'新潟県',
			'16'	=>	'富山県',
			'17'	=>	'石川県',
			'18'	=>	'福井県',
			'19'	=>	'山梨県',
			'20'	=>	'長野県',
			'21'	=>	'岐阜県',
			'22'	=>	'静岡県',
			'23'	=>	'愛知県',
			'24'	=>	'三重県',
			'25'	=>	'滋賀県',
			'26'	=>	'京都府',
			'27'	=>	'大阪府',
			'28'	=>	'兵庫県',
			'29'	=>	'奈良県',
			'30'	=>	'和歌山県',
			'31'	=>	'鳥取県',
			'32'	=>	'島根県',
			'33'	=>	'岡山県',
			'34'	=>	'広島県',
			'35'	=>	'山口県',
			'36'	=>	'徳島県',
			'37'	=>	'香川県',
			'38'	=>	'愛媛県',
			'39'	=>	'高知県',
			'40'	=>	'福岡県',
			'41'	=>	'佐賀県',
			'42'	=>	'長崎県',
			'43'	=>	'熊本県',
			'44'	=>	'大分県',
			'45'	=>	'宮崎県',
			'46'	=>	'鹿児島県',
			'47'	=>	'沖縄県'
        );
  	}
?>