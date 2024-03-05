<?php
$databaseConfig = array(
    'host' => 'localhost',
    'dbname' => 'dreamscorporation',
    'username' => 'root',
    'password' => '',
	'hhost' => 'hMailServer.Application',
	'SMTP' => 'hMailServer.Message',
	'hAdmin' => 'Administrator',
	'hpassword' => '12345',
	'dominio' => 'dreamscorporation.com',
	'max' => 100,
	'imapServer' => 'localhost',
	'uploadDirectory' => '../anexos/'
);
try {
    $pdo = new PDO("mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']}", $databaseConfig['username'], $databaseConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database failhou: " . $e->getMessage());
}
?>
