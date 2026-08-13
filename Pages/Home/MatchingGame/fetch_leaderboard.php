<?php
session_start();
include "../../../SQL_Queries/connection.php";

$deckId = $_GET['deckId'] ?? null;
$classroomId = $_GET['classroomId'];
$userId = $_COOKIE["user_id"] ?? $_SESSION['user_id'] ?? null;

if (!$deckId || !$userId) exit;

if ($classroomId > 0) {
    // Hanya user di classroom yang sudah punya waktu
    $sql = "SELECT u.user_id, u.name AS user_name, ml.best_time
            FROM junction_classroom_user jcu
            JOIN users u ON jcu.user_id = u.user_id
            INNER JOIN matching_leaderboard ml ON ml.user_id = u.user_id AND ml.deck_id = ?
            WHERE jcu.classroom_id = ?
            ORDER BY ml.best_time ASC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $deckId, $classroomId);
} else {
    // Global: semua yang punya skor
    $sql = "SELECT u.user_id, u.name AS user_name, ml.best_time
            FROM matching_leaderboard ml
            JOIN users u ON ml.user_id = u.user_id
            WHERE ml.deck_id = ?
            ORDER BY ml.best_time ASC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $deckId);
}

$stmt->execute();
$result = $stmt->get_result();

$leaderboard = [];
$rank = 0;
while ($row = $result->fetch_assoc()) {
    $rank++;
    $isYou = ($row['user_id'] === $userId);

    $leaderboard[] = [
        'rank' => $rank,
        'user_name' => $row['user_name'],
        'best_time' => (float)$row['best_time'],
        'is_you' => $isYou
    ];
}
$stmt->close();

// Pisahkan user yang login
$you = null;
foreach ($leaderboard as $entry) {
    if ($entry['is_you']) {
        $you = $entry;
        break;
    }
}
?>
<?php if ($you): ?>
<table class="leaderboard-you">
    <tr class="you">
        <td class="rank" style="font-weight: bold"><?= $you['rank']; ?></td>
        <td style="font-weight: bold"><?= htmlspecialchars($you['user_name']); ?></td>
        <td class="score" style="font-weight: bold"><?= number_format($you['best_time'], 2); ?>s</td>
    </tr>
</table>
<?php endif; ?>

<div class="leaderboard-wrapper">
    <table class="leaderboard-table">
        <?php foreach ($leaderboard as $entry): ?>
            <?php if (!$entry['is_you']): ?>
                <tr>
                    <td class="rank"><?= $entry['rank']; ?></td>
                    <td><?= htmlspecialchars($entry['user_name']); ?></td>
                    <td class="score"><?= number_format($entry['best_time'], 2); ?>s</td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</div>