<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'check_events':
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
                    break;

                case 'edit_event':
                    $year = $_POST['year'];
                    $month = $_POST['month'];
                    $day = $_POST['day'];
                    $title = $_POST['title'];
                    $description = $_POST['description'];
					$eventId = $_POST['eventId'];

                    $stmt = $pdo->prepare("UPDATE agenda_events SET event_title = :title, event_description = :description, event_date = :eventDate WHERE user_id = :user_id AND event_id = :eventId LIMIT 1");
					$eventDate = "$year-$month-$day";
					$stmt->bindParam(':title', $title);
					$stmt->bindParam(':description', $description);
					$stmt->bindParam(':eventDate', $eventDate);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':eventId', $eventId);
					$stmt->execute();

                    echo json_encode(['success' => true]);
                    break;
					
				case 'add_event':
					$year = $_POST['year'];
					$month = $_POST['month'];
					$day = $_POST['day'];
					$title = $_POST['title'];
					$description = $_POST['description'];

					$stmt = $pdo->prepare("INSERT INTO agenda_events (user_id, event_date, event_title, event_description) VALUES (:user_id, :event_date, :title, :description)");
					$eventDate = "$year-$month-$day";
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':event_date', $eventDate);
					$stmt->bindParam(':title', $title);
					$stmt->bindParam(':description', $description);
					$stmt->execute();

					echo json_encode(['success' => true]);
					break;

                case 'delete_event':
					$event_id = $_POST['event_id'];

					$stmt = $pdo->prepare("DELETE FROM agenda_events WHERE user_id = :user_id AND event_id = :event_id");
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':event_id', $event_id);
					$stmt->execute();

					echo json_encode(['success' => true]);
					break;

				default:
					echo json_encode(['error' => 'Invalid action']);
					break;
            }
        }
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>
