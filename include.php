<?php
include_once __DIR__."/github/DB/DB.php";

include_once __DIR__."/github/MysqlCompact/MysqlCompact.php";

include_once __DIR__."/github/MysqlCompact/CRUD.php";

include_once __DIR__."/FansList.php";
include_once __DIR__."/simple_html_dom.php";

$DBConfig=json_decode(file_get_contents(__DIR__."/config/DB.json"),1);

DB::$config=$DBConfig;
DB::query("SET NAMES utf8");
