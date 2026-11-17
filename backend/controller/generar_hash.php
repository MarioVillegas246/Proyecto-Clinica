<?php
// Esto genera un hash seguro compatible con password_verify()
$password_segura = password_hash("Mario20406", PASSWORD_DEFAULT);
echo $password_segura;
?>
