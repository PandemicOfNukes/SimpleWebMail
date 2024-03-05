<?php
session_start();
include 'config.php';

try {
    $oApp = new COM($databaseConfig['hhost']);

    $authResult = $oApp->Authenticate($_SESSION['email'], $_SESSION['password']);

    if (!$authResult) {
        die('Authentication failed');
    }

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);

    $oAccounts = $oDomain->Accounts;

    $numAccounts = $oAccounts->Count;

    $accountDetails = [];

    for ($i = 0; $i < $numAccounts; $i++) {
        $oAccount = $oAccounts->Item($i);

        $address = $oAccount->Address;
        $adminLevel = $oAccount->AdminLevel;
        $password = $oAccount->Password;

        $accountDetails[] = [
            'address' => $address,
            'adminLevel' => $adminLevel,
            'password' => $password,
        ];
    }

    header('Content-Type: application/json');
    echo json_encode(['accounts' => $accountDetails]);

} catch (com_exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
