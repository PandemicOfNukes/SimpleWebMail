<?php
session_start();
include 'config.php';

if (!isset($_GET['emailid'])) {
    echo json_encode(['error' => 'Email ID not provided']);
    exit;
}

$imapServer = $databaseConfig['imapServer'];
$username = $_SESSION['email'];
$password = $_SESSION['password'];

try {
    $inbox = imap_open("{" . $imapServer . ":143/imap}", $username, $password);

    if (!$inbox) {
        echo json_encode(['error' => 'Unable to connect to IMAP server']);
        exit;
    }

    $emailId = $_GET['emailid'];

    if (!is_numeric($emailId)) {
        echo json_encode(['error' => 'Invalid email ID format']);
        exit;
    }

    $success = imap_delete($inbox, $emailId);

    if ($success) {
        imap_expunge($inbox);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mensagem não encontrada ou não pode ser excluída.']);
    }

    imap_close($inbox);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao eliminar mensagens: ' . $e->getMessage()]);
}
?>
