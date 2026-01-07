<?php
session_start();
if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 0;
}
$_SESSION['count']++;

echo "<h1>Debug Sessão</h1>";
echo "<p>Contador: <strong>" . $_SESSION['count'] . "</strong></p>";
echo "<p>ID da Sessão: " . session_id() . "</p>";
echo "<p>Save Path: " . session_save_path() . "</p>";
echo "<p>Cookie Params: <pre>" . print_r(session_get_cookie_params(), true) . "</pre></p>";
echo "<p><a href='debug_session.php'>Recarregar</a></p>";
