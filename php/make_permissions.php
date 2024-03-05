<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['newPermission'])) {
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

        $oAccount->AdminLevel = $_POST['newPermission'];
        $oAccount->Save();

        echo json_encode(['success' => true, 'message' => 'Permission changed successfully']);

    } catch (com_exception $e) {
        error_log('Error in make_permissions.php: ' . $e->getMessage(), 0);

        echo json_encode(['success' => false, 'message' => 'An error occurred. Please check the logs for more information.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
