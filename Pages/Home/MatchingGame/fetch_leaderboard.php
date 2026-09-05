<?php
session_start();
include "../../../SQL_Queries/connection.php";

// ===== AMBIL PARAMETER =====
$deckId = $_GET['deckId'] ?? null;
$classroomId = $_GET['classroomId'] ?? '0'; // '0' berarti global
$userId = $_COOKIE["user_id"] ?? $_SESSION['user_id'] ?? null;

if (!$deckId || !$userId) {
    echo "Invalid request.";
    exit;
}

// ===== BANGUN QUERY DENGAN FILTER =====
$isClassroom = ($classroomId !== '0');

// Subquery untuk mendapatkan daftar user_id yang terdaftar di classroom (jika filter)
$classroomUserSubquery = "";
if ($isClassroom) {
    $classroomUserSubquery = " AND u.user_id IN (SELECT user_id FROM junction_classroom_user WHERE classroom_id = ?) ";
}

// Query utama dengan RANK()
$sql = "
    SELECT 
        u.user_id,
        u.name AS user_name,
        ml.best_time,
        RANK() OVER (ORDER BY ml.best_time ASC) AS rank_position
    FROM matching_leaderboard ml
    JOIN users u ON ml.user_id = u.user_id
    WHERE ml.deck_id = ?
    $classroomUserSubquery
    ORDER BY ml.best_time ASC
";

$stmt = $con->prepare($sql);
if ($isClassroom) {
    $stmt->bind_param("ss", $deckId, $classroomId);
} else {
    $stmt->bind_param("s", $deckId);
}
$stmt->execute();
$result = $stmt->get_result();

// ===== KUMPULKAN DATA =====
$leaderboard = [];
$userRank = null;
$userBestTime = null;

while ($row = $result->fetch_assoc()) {
    $isYou = ($row['user_id'] === $userId);
    if ($isYou) {
        $userRank = (int) $row['rank_position'];
        $userBestTime = (float) $row['best_time'];
    }
    $leaderboard[] = [
        'rank' => (int) $row['rank_position'],
        'user_name' => $row['user_name'],
        'best_time' => (float) $row['best_time'],
        'is_you' => $isYou
    ];
}
$stmt->close();

// ===== JIKA USER TIDAK ADA DI LEADERBOARD (BELUM PUNYA SKOR) =====
// Maka kita tidak tampilkan baris "you", karena tidak ada skor.
// Tapi jika user punya skor tapi tidak masuk hasil karena filter classroom?
// Sebenarnya jika user terdaftar di classroom dan punya skor, akan muncul.
// Jika tidak, maka tidak ada baris "you".

// ===== TAMPILKAN HTML =====
?>
<?php if ($userRank !== null && $userBestTime !== null): ?>
    <table class="leaderboard-you">
        <tr class="you">
            <td class="rank" style="font-weight: bold"><?= $userRank; ?></td>
            <td style="font-weight: bold"><?= htmlspecialchars($leaderboard[array_search(true, array_column($leaderboard, 'is_you'))]['user_name'] ?? 'You'); ?></td>
            <td class="score" style="font-weight: bold"><?= number_format($userBestTime, 2); ?>s</td>
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