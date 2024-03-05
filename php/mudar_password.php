<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['newPassword'])) {
    try {
        $oApp = new COM($databaseConfig['hhost']);

        $authResult = $oApp->Authenticate($_SESSION['email'], $_SESSION['password']);

        if (!$authResult) {
            echo json_encode(['success' => false, 'message' => 'Authentication failed']);
            exit();
        }

        $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);

        $oAccount = $oDomain->Accounts->ItemByAddress($_POST['email']);

        if (!$oAccount) {
            echo json_encode(['success' => false, 'message' => 'Account not found']);
            exit();
        }

        $oAccount->Password = isset($_POST['newPassword']) ? $_POST['newPassword'] : null;

        $oAccount->Save();

        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);

    } catch (com_exception $e) {
        error_log('Error in mudar_password.php: ' . $e->getMessage(), 0);

        echo json_encode(['success' => false, 'message' => 'Erro.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
