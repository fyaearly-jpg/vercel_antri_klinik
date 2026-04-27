<?php
// api/session_config.php
// Session handler menggunakan TiDB database (stabil di Vercel serverless)

$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = 'wG4jTPVkrLpurY1.root';
$pass = 'lb5JGw7uy0uZJ9yf';
$db   = 'klinik_db';

$db_session = mysqli_init();
mysqli_ssl_set($db_session, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($db_session, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

// Buat tabel sessions jika belum ada
mysqli_query($db_session, "CREATE TABLE IF NOT EXISTS `sessions` (
    `session_id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `data` TEXT NOT NULL,
    `last_activity` INT NOT NULL
)");

class DbSessionHandler implements SessionHandlerInterface {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function open($path, $name): bool { return true; }
    public function close(): bool { return true; }

    public function read($id): string {
        $id = mysqli_real_escape_string($this->db, $id);
        $r  = mysqli_query($this->db, "SELECT data FROM sessions WHERE session_id='$id' AND last_activity > " . (time() - 3600));
        if ($r && mysqli_num_rows($r) > 0) {
            return mysqli_fetch_assoc($r)['data'];
        }
        return '';
    }

    public function write($id, $data): bool {
        $id   = mysqli_real_escape_string($this->db, $id);
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
        return mysqli_affected_rows($this->db);
    }
}

$handler = new DbSessionHandler($db_session);
session_set_save_handler($handler, true);

// PENGATURAN PENTING UNTUK VERCEL (HTTPS)
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);   // WAJIB AKTIF karena Vercel pakai HTTPS
ini_set('session.cookie_path', '/');   // Pastikan cookie terbaca di semua folder
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 3600);

session_start();