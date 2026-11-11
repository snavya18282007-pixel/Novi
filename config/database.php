<?php
/**
 * NOVI - Personal Study Companion
 * Database Configuration & Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'novi');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Friendly error page if DB fails to connect
                http_response_code(500);
                echo '<!DOCTYPE html><html><head><title>NOVI - Database Connection Error</title>';
                echo '<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f8fafc;color:#1e293b;padding:3rem 1rem;display:flex;justify-content:center;}';
                echo '.card{background:#fff;padding:2rem;border-radius:12px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);max-width:550px;width:100%;border-top:4px solid #ef4444;}';
                echo 'h2{color:#ef4444;margin-top:0;}code{background:#f1f5f9;padding:0.2rem 0.4rem;border-radius:4px;font-size:0.9em;}</style></head><body>';
                echo '<div class="card"><h2>Database Connection Required</h2>';
                echo '<p>NOVI could not connect to the MySQL database <code>' . DB_NAME . '</code>.</p>';
                echo '<p><strong>Quick Fix Instructions:</strong></p><ol>';
                echo '<li>Ensure XAMPP MySQL is running.</li>';
                echo '<li>Import <code>database/novi.sql</code> in phpMyAdmin or MySQL terminal.</li>';
                echo '<li>Refresh this page.</li></ol>';
                echo '<p style="color:#64748b;font-size:0.85rem;">Error detail: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div></body></html>';
                exit;
            }
        }
        return self::$instance;
    }
}

/**
 * Escapes HTML string safely
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirects to a relative route
 */
function redirect(string $path): void {
    header("Location: " . $path);
    exit;
}

/**
 * Sets a flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type, // success, error, info
        'message' => $message
    ];
}

/**
 * Gets and clears flash message
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Returns or generates a CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies the CSRF token from POST request
 */
function verify_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            set_flash('error', 'Invalid security token. Please try again.');
            return false;
        }
    }
    return true;
}
