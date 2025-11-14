<?php
require _THEMES_."/"._THEME_NAME_."/routes.php";
Cavesman\Router::run(function(){
    Cavesman\Smarty::getInstance()->display("base.tpl");
});
