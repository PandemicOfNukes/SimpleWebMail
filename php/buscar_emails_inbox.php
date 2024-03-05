<?php
session_start();
include 'config.php';

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
        die('Authentication failed');
    }

    $oDomain = $oApp->Domains->ItemByName($databaseConfig['dominio']);

    $emails = [];
    $response = [];

    $inbox = imap_open("{" . $imapServer . ":143/imap}", $username, $password);

    if ($inbox === false) {
        $error = imap_last_error();
        if (!empty($error)) {
            $response['error'] = 'Could not connect to IMAP server: ' . $error;
        }
    } else {
        $emailIds = imap_search($inbox, 'ALL');

        if (is_array($emailIds)) {
            foreach ($emailIds as $emailId) {
                $headerInfo = imap_headerinfo($inbox, $emailId);

                $attachments = [];
                $body = '';

                $structure = imap_fetchstructure($inbox, $emailId);

                if ($structure->type == 1) {
                    foreach ($structure->parts as $partNum => $part) {
                        if ($part->subtype == 'PLAIN') {
                            $body = imap_fetchbody($inbox, $emailId, $partNum + 1);
                        } elseif ($part->subtype == 'OCTETSTREAM' && $part->ifdparameters) {
                            $attachments[] = [
                                'filename' => $part->dparameters[0]->value,
                                'format' => $part->subtype,
                            ];
                        }
                    }
                }

                $emails[] = [
                    'id' => $emailId,
                    'subject' => $headerInfo->subject,
                    'fromaddress' => $headerInfo->fromaddress,
                    'sentDate' => date('d/m/Y', strtotime($headerInfo->date)),
                    'body' => $body,
                ];
            }
        }

        imap_close($inbox);
    }

    error_reporting($previousErrorReporting);

    if (!empty($response['error']) || !empty($emails)) {
        header('Content-Type: application/json');
        echo json_encode($response + ['emails' => $emails], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
} catch (com_exception $e) {
    error_reporting($previousErrorReporting);

    echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
}
?>
