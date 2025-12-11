<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "praktikaO") or die("Ошибка подключения");
function find($login, $password) {
  global $db;
  $result = mysqli_query($db, "SELECT * FROM user WHERE username = '$login' AND password = MD5('$password')");
  return mysqli_fetch_assoc($result);
}
?>