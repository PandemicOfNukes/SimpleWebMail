<?php
session_start();
include 'config.php';

$password = isset($_POST["password"]) ? $_POST["password"] : null;
$email = isset($_POST["email"]) ? $_POST["email"] : null;
$permissionLevel = isset($_POST["permissionLevel"]) ? $_POST["permissionLevel"] : 0;

try {
    if (!$password || !$email) {
        error_log("Error: Incomplete form data.");
        echo "Error: Incomplete form data.";
        exit;
    }

    $oApp = new COM($databaseConfig['hhost']);

    $authResult = $oApp->Authenticate($_SESSION['email'], $_SESSION['password']);

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);

    $oAccount = $oDomain->Accounts->Add();
    $oAccount->Address = $email;
    $oAccount->Password = $password;
    $oAccount->Active = true;
    $oAccount->MaxSize = $databaseConfig['max'];
    $oAccount->AdminLevel = $permissionLevel;
    $oAccount->Save();

    $oApp = null;
} catch (com_exception $e) {
    error_log("Erro: " . $e->getMessage());
    echo "Erro: " . $e->getMessage();
}
?>
