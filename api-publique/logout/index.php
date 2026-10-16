<?php
session_start();
session_destroy();
header("Location: /api-publique");
exit();
?>