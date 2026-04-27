<?php
// api/logout.php
setcookie("user_session", "", time() - 3600, "/");
header("Location: /login");
exit();
?>