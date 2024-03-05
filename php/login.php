<?php
session_start();
include 'config.php';

$email = $_POST["email"];
$password = $_POST["password"];

try {
    $oApp = new COM($databaseConfig['hhost']);

    $oApp->Authenticate($databaseConfig['hAdmin'], $databaseConfig['hpassword']);

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);
    $oAccount = $oDomain->Accounts->ItemByAddress($email);

    if ($oAccount && $oAccount->AdminLevel >= 1) {
		$user_id = $oAccount->ID;
		$_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        echo 'webadmin_dashboard.html';
    } else {
		$user_id = $oAccount->ID;
		$_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        echo 'inbox.html';
    }
} catch (com_exception $e) {
    echo 'Erro: ' . $e->getMessage().'<br>';
    echo 'Email Incorreto ou Password Incorreta';
}
?>
