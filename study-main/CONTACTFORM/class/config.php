<?php
session_start();

define("DSN", "mysql:host=localhost; dbname=qanda; charset=utf8mb4");
define("DB_USER", "root");
define("DB_PASS", "root");
// define("SITE_URL", "http://". $_SERVER["HTTP_HOST"]);

// require_once("Utils.php");
// require_once("Token.php");
// require_once("db.php");aaa
// require_once("contact.php");

// 自動でクラスを読み取る
spl_autoload_register(function($class){
  $prefix = "contactsApp\\";//バックスラッシュ自身を表現するには『\\』とする

  if(strpos($class, $prefix) === 0){//$prefixが先頭にあるかどうか判別する
    // $fileName = sprintf("%s.php", substr($class, 11)); //11番目以降の部分文字列を渡す(contactApp→10文字)
    $fileName = sprintf("%s.php", substr($class, strlen($prefix))); //11番目以降の部分文字列を渡す(contactApp→10文字)

    if(file_exists($fileName)){
      require_once($fileName);
    }else{
      echo "ファイルが見つかりません".$fileName;
      exit;
    }
  }
});