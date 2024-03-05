<?php
include 'config.php';

$password = isset($_POST["password"]) ? $_POST["password"] : null;
$email = isset($_POST["email"]) ? $_POST["email"] : null;

try {
    if (!$password || !$email) {
        error_log("Erro: Dados de formulário incompletos.");
        echo "Erro: Dados de formulário incompletos.";
        exit;
    }

    error_log("Recebido email: $email, senha: $password");

    $oApp = new COM($databaseConfig['hhost']);

    $oApp->Authenticate($databaseConfig['hAdmin'], $databaseConfig['hpassword']);

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);

    $oAccount = $oDomain->Accounts->Add();
    $oAccount->Address = $email;
    $oAccount->Password = $password;
    $oAccount->Active = True;
    $oAccount->MaxSize = $databaseConfig['max'];
    $oAccount->Save();

    echo "Conta de email criada com sucesso.";

    $oApp = null;
} catch (com_exception $e) {
    error_log("Erro ao criar conta de email: " . $e->getMessage());
    echo "Erro ao criar conta de email: " . $e->getMessage();
}
?>
