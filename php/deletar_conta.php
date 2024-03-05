<?php
session_start();
include 'config.php';

try {
    $oApp = new COM($databaseConfig['hhost']);

    $authResult = $oApp->Authenticate($_SESSION['email'], $_SESSION['password']);

    if (!$authResult) {
        die('Authentication failed');
    }

    $accountAddress = isset($_POST['email']) ? $_POST['email'] : null;

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);
    $oAccounts = $oDomain->Accounts;
    $oAccount = $oAccounts->ItemByAddress($accountAddress);

    if ($oAccount) {
        try {
            $oAccount->Delete();
            echo json_encode(['success' => true]);
        } catch (com_exception $deleteError) {
            echo json_encode(['success' => false, 'error' => 'Error during account deletion.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Account not found.']);
    }

} catch (com_exception $e) {
    error_log('Erro: ' . $e->getMessage(), 3, 'C:\xampp\php\logs\php_error_log.txt');
    echo json_encode(['success' => false, 'error' => 'Erro: ' . $e->getMessage()]);
}
?>
