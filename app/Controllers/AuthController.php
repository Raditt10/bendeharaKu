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

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['nis'] = $user['nis'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['login_success'] = true;
            $_SESSION['success_msg'] = "Anda berhasil login!";
            header('Location: ./');
            exit;
        }

        $this->showLogin(['error_msg' => 'NIS atau password salah!']);
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        header('Location: ./');
        exit;
    }
}
