<?php
try {
  $db = new PDO('mysql:dbname=mini_bbs;host=localhost:8889;
  charset=utf8', 'root', 'root');
} catch(PDOExeption $e) {
  print('DB接続エラー：' . $e->getMessage());
}
?>