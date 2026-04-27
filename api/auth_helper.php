<?php
// api/auth_helper.php
function get_user_session() {
    if (isset($_COOKIE['user_session'])) {
        return json_decode(base64_decode($_COOKIE['user_session']), true);
    }
    return null;
}

function cek_akses($role_required) {
    $user = get_user_session();
    if (!$user || $user['role'] !== $role_required) {
        header("Location: /login");
        exit();
    }
    return $user;
}