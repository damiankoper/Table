<?php 
spl_autoload_register(function($classname){
    $dir = __DIR__;
        require_once __DIR__."\\".$classname.".php";
});
$options = array(
    "_type"=>"mysql",
    "_options"=>array(
        "_host"=>"localhost",
        "_username"=>"",
        "_password"=>"",
        "_schema"=>"",
        "_port"=>"3306"
    )
);