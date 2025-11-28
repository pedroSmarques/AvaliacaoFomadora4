<?php
session_start();

// destrói completamente
session_unset();
session_destroy();

// limpa cookies PHPSESSID se existirem
setcookie(session_name(), '', time() - 3600, '/');

// redireciona para login
header("Location: ../login.php");
exit;
?>
