<?php
// api/session_config.php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = 'wG4jTPVkrLpurY1.root';
$pass = 'lb5JGw7uy0uZJ9yf';
$db   = 'klinik_db';

if (!isset($koneksi)) {
    $koneksi = mysqli_init();
    mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);
    mysqli_real_connect($koneksi, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);
}

// Gunakan koneksi yang sudah ada untuk session
$db_session = $koneksi;

class DbSessionHandler implements SessionHandlerInterface {
    private $db;
    public function __construct($db) { $this->db = $db; }
    public function open($path, $name): bool { return true; }
    public function close(): bool { return true; }
    public function read($id): string {
        $id = mysqli_real_escape_string($this->db, $id);
        $r = mysqli_query($this->db, "SELECT data FROM sessions WHERE session_id='$id' AND last_activity > " . (time() - 3600));
        return ($r && $row = mysqli_fetch_assoc($r)) ? $row['data'] : '';
    }
    public function write($id, $data): bool {
        $id = mysqli_real_escape_string($this->db, $id);
        $data = mysqli_real_escape_string($this->db, $data);
        $time = time();
        mysqli_query($this->db, "REPLACE INTO sessions (session_id, data, last_activity) VALUES ('$id', '$data', $time)");
        return true;
    }
    public function destroy($id): bool {
        $id = mysqli_real_escape_string($this->db, $id);
        mysqli_query($this->db, "DELETE FROM sessions WHERE session_id='$id'");
        return true;
    }
    public function gc($max_lifetime): int|false {
        $old = time() - $max_lifetime;
        mysqli_query($this->db, "DELETE FROM sessions WHERE last_activity < $old");
        return true;
    }
}

if (!session_id()) {
    $handler = new DbSessionHandler($db_session);
    session_set_save_handler($handler, true);
    ini_set('session.cookie_secure', 1); // Wajib untuk HTTPS Vercel
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}