<?php
	require_once "constant.php";
	require_once DIR_FW."/fw_common.php";
	if(session_id()==""){
		session_start();
	}
	set_error_handler("error_handler");
	if( !isset($_GET['controller']) || $_GET['controller'] == ""){
		$_GET['controller'] = DEF_CONTROLLER;
	}
	if( file_exists(DIR_APP."/".$_GET['controller']."/".$_GET['controller']."_controller.php") ){
		require_once DIR_APP."/".$_GET['controller']."/".$_GET['controller']."_controller.php";
		$class = $_GET['controller']."_controller";
		$controller = new $class;
        $param = array('GET'=>$_GET,'POST'=>$_POST,'SESS'=>$_SESSION,'PARAM'=>array(),'HEAD'=>getallheaders());
		$controller->call($param);
		if (isset($param['SESS'])){
			$_SESSION = $param['SESS'];
		} else {
			unset($_SESS);
		}
	} else {
		trigger_error("BAD_REQUEST",E_USER_ERROR);
	}
?>
