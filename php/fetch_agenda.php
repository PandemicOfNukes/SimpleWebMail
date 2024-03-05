<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM agenda_events WHERE user_id = :user_id AND YEAR(event_date) = :year AND MONTH(event_date) = :month AND DAY(event_date) = :day");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':year', $_POST['year']);
    $stmt->bindParam(':month', $_POST['month']);
    $stmt->bindParam(':day', $_POST['day']);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['events' => $events]);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
