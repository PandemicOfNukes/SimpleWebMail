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

$previousErrorReporting = error_reporting();

function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    if ($errno === E_NOTICE) {
        $GLOBALS['customNotice'] = $errstr;
        return true;
    }
    return false;
}

set_error_handler('customErrorHandler');

try {
    $oApp = new COM($databaseConfig['hhost']);

    $authResult = $oApp->Authenticate($username, $password);

    if (!$authResult) {
        echo json_encode(['error' => 'Authentication failed']);
        exit;
    }

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);

    $emailId = $_GET['emailid'];

    $inbox = imap_open("{" . $imapServer . ":143/imap}", $username, $password) or die('Could not connect to IMAP server');

    $headerInfo = imap_headerinfo($inbox, $emailId);

    function getPart($part, $emailId, $partNum) {
        global $inbox;

        $data = imap_fetchbody($inbox, $emailId, $partNum);
        $data = urldecode($data);

        return $data;
    }

    function processPart($part, $emailId, $partNum) {
        global $attachments, $body;

        if (isset($part->parts) && is_array($part->parts)) {
            foreach ($part->parts as $subPartNum => $subPart) {
                processPart($subPart, $emailId, $partNum . '.' . ($subPartNum + 1));
            }
        } else {
            $body .= getPart($part, $emailId, $partNum + 1);
        }
    }

    processPart($headerInfo->parts, $emailId, 0);

    $emailDetails = [
        'id' => $emailId,
        'subject' => $headerInfo->subject,
        'fromaddress' => $headerInfo->fromaddress,
        'sentDate' => date('d/m/Y', strtotime($headerInfo->date)),
        'body' => $body,
    ];

    header('Content-Type: application/json');
    echo json_encode($emailDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    imap_close($inbox);
} catch (com_exception $e) {
    echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
}
?>
