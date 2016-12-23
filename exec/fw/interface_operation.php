<?php
    require_once "constant.php";
    // -------------------------------------------------------------------
    // オペレーションのインターフェイス
    // -------------------------------------------------------------------
    interface interface_operation {
        public function exec(&$params);
    }
?>