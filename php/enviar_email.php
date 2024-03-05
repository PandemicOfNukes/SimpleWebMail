<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $mail = $_SESSION['email'];

        function extractTextUntilAt($inputString) {
            $atIndex = strpos($inputString, '@');
            return $atIndex !== false ? substr($inputString, 0, $atIndex) : $inputString;
        }

        $username = extractTextUntilAt($mail) . "<" . $mail . ">";

        $to = $_POST["to"] ? $_POST["to"] : "";
        $recipients = explode(',', $to);
        $subject = $_POST["subject"];
        $message = isset($_POST["message"]) ? $_POST["message"] : "";
        $attachments = $_FILES["attachment"];
        
        foreach ($recipients as $recipient) {
            $recipient = trim($recipient);
            if (!empty($recipient)) {
                $oMail = new COM($databaseConfig['SMTP']);
                $oMail->From = $username;
                $oMail->FromAddress = $mail;
                $oMail->AddRecipient($recipient, $recipient);
                $oMail->Subject = $subject;

                $oMail->Body = $message;

                if (!empty($attachments["name"][0])) {
                    $uploadDirectory = $databaseConfig['uploadDirectory'];

                    foreach ($attachments["tmp_name"] as $index => $attachmentPath) {
                        $attachmentName = $attachments["name"][$index];
                        $uploadedFilePath = $uploadDirectory . $attachmentName;

                        move_uploaded_file($attachmentPath, $uploadedFilePath);

                        $downloadLink = "<a href=anexos/" . $attachmentName . " download>Download</a>";
                        $oMail->Body .= "<br>Anexo: " . $attachmentName . "\n\n: " . urlencode($downloadLink);
                    }
                    $oMail->Save();
                } else {
                    $oMail->Save();
                }
            }
        }
        header("Refresh: 5; URL=../inbox.html");
        echo "Email Enviado! Voltando para a sua Inbox em 5 segundos...";
        echo '<a href="../inbox.html">Clique aqui para ir para a sua Inbox imediatamente</a>';
        exit;
    } catch (com_exception $e) {
        $errorResponse = ['error' => 'Erro: ' . $e->getMessage()];
    }
}
?>
