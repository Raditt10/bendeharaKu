<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Database.php';

class AuthController extends BaseController
{
    public function showLogin(array $data = [])
    {
        $this->render('login', $data);
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $db = Database::getInstance()->getConnection();

        $nis = trim($_POST['nis'] ?? '');
        $password = $_POST['password'] ?? '';

        // Use prepared statement to avoid SQL injection
        $user = null;
        if ($stmt = $db->prepare("SELECT nis, nama, password, role FROM users WHERE nis = ? LIMIT 1")) {
            $stmt->bind_param('s', $nis);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                $user = $res->fetch_assoc();
            }
            $stmt->close();
        }

        if ($user) {
            $passwordOk = false;

            // Normal case: hashed password stored in DB
            if (password_verify($password, $user['password'])) {
                $passwordOk = true;
            } else {
                // Fallback for legacy accounts that still have plaintext passwords
                if (hash_equals($user['password'], $password)) {
                    $passwordOk = true;
                    // Re-hash the plaintext password and update the DB to improve security
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    if ($updateStmt = $db->prepare("UPDATE users SET password = ? WHERE nis = ?")) {
                        $updateStmt->bind_param('ss', $newHash, $nis);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                }
            }

            if ($passwordOk) {
                $_SESSION['nis'] = $user['nis'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['login_success'] = true;
                
                // Redirect ke route animasi sukses
                $script = $_SERVER['PHP_SELF'] ?? './';
                header('Location: ' . $script . '?page=login_success');
                exit;
            }
        }

        // On failure, redirect back with an error message (use query param as fallback)
        $script = $_SERVER['PHP_SELF'] ?? './';
        $msg = urlencode('NIS atau password salah!');
        header('Location: ' . $script . '?page=login&err=' . $msg);
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        $script = $_SERVER['PHP_SELF'] ?? './';
        header('Location: ' . $script . '?page=home');
        exit;
    }
}