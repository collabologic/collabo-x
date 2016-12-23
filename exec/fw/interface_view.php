<?php
    require_once "constant.php";
    // -------------------------------------------------------------------
    // ビューのインターフェイス
    // -------------------------------------------------------------------
    interface interface_view {
        public function display(&$params);
    }
?>