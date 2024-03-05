<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];

        $stmt = $pdo->prepare("SELECT event_title, event_description FROM agenda_events WHERE user_id = :user_id AND YEAR(event_date) = :year AND MONTH(event_date) = :month AND DAY(event_date) = :day");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':day', $day);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');

        echo json_encode(['hasEvents' => count($events) > 0]);
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
