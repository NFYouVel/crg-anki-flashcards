<?php
// ============================================================
// 1. ONE query — fetch every deck this user has access to
// ============================================================
$stmtDecks = $con->prepare("
    SELECT d.deck_id, d.name, d.parent_deck_id, d.is_leaf
    FROM junction_deck_user AS jdu
    JOIN decks AS d ON jdu.deck_id = d.deck_id
    WHERE jdu.user_id = ?
    ORDER BY d.name
");
$stmtDecks->bind_param("s", $user_id);
$stmtDecks->execute();
$deckRows = $stmtDecks->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtDecks->close();

// Build two lookup maps from the single result
$deckMap      = [];   // deck_id  => deck row
$childrenMap  = [];   // parent_deck_id => [deck_id, ...]
$userDeckIDs  = [];   // flat list of deck_ids the user owns

foreach ($deckRows as $row) {
    $userDeckIDs[]              = $row['deck_id'];
    $deckMap[$row['deck_id']]   = $row;
    $childrenMap[$row['parent_deck_id'] ?? 'root'][] = $row['deck_id'];
}

// ============================================================
// 2. ONE query — RGB counts for ALL decks at once
// ============================================================
$stmtRGB = $con->prepare("
    WITH RECURSIVE deck_tree AS (
        SELECT deck_id AS root_deck_id, deck_id, parent_deck_id, is_leaf
        FROM decks
        WHERE deck_id IN (
            SELECT deck_id FROM junction_deck_user WHERE user_id = ?
        )

        UNION ALL

        SELECT dt.root_deck_id, d.deck_id, d.parent_deck_id, d.is_leaf
        FROM decks AS d
        JOIN deck_tree AS dt ON d.parent_deck_id = dt.deck_id
    )
    SELECT
        dt.root_deck_id AS deck_id,
        SUM(CASE WHEN cp.current_stage = 0                          THEN 1 ELSE 0 END) AS blue,
        SUM(CASE WHEN cp.current_stage != 0 AND cp.review_due <= NOW() THEN 1 ELSE 0 END) AS green,
        SUM(CASE WHEN cp.review_due > NOW()                         THEN 1 ELSE 0 END) AS red
    FROM deck_tree AS dt
    JOIN junction_deck_card AS jdc ON jdc.deck_id = dt.deck_id AND dt.is_leaf = 1
    JOIN card_progress      AS cp  ON cp.card_id  = jdc.card_id AND cp.user_id = ?
    GROUP BY dt.root_deck_id
");
$stmtRGB->bind_param("ss", $user_id, $user_id);
$stmtRGB->execute();
$rgbRows = $stmtRGB->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtRGB->close();

// Build RGB lookup: deck_id => ['red'=>N, 'green'=>N, 'blue'=>N]
$rgbMap = [];
foreach ($rgbRows as $row) {
    $rgbMap[$row['deck_id']] = $row;
}

// Helper: get counts for any deck_id (zero-safe)
function getRGB(string $deckID, array $rgbMap): array {
    return $rgbMap[$deckID] ?? ['red' => 0, 'green' => 0, 'blue' => 0];
}

// ============================================================
// 3. Main Deck RGB — still just a PHP map lookup now
// ============================================================
$stmtMain = $con->prepare("
    SELECT
        SUM(CASE WHEN cp.current_stage = 0                             THEN 1 ELSE 0 END) AS blue,
        SUM(CASE WHEN cp.current_stage != 0 AND cp.review_due <= NOW() THEN 1 ELSE 0 END) AS green,
        SUM(CASE WHEN cp.review_due > NOW()                            THEN 1 ELSE 0 END) AS red
    FROM card_progress AS cp
    WHERE cp.user_id = ?
");
$stmtMain->bind_param("s", $user_id);
$stmtMain->execute();
$countMain = $stmtMain->get_result()->fetch_assoc();
$stmtMain->close();

// ============================================================
// 4. Pure-PHP recursive renderer — ZERO extra DB queries
// ============================================================
function showDecks(?string $parentID, array $childrenMap, array $deckMap, array $userDeckIDs, array $rgbMap): void
{
    $key      = $parentID ?? 'root';
    $children = $childrenMap[$key] ?? [];

    foreach ($children as $deckID) {
        // Only show decks this user is enrolled in
        if (!in_array($deckID, $userDeckIDs, true)) continue;

        $deck  = $deckMap[$deckID];
        $rgb   = getRGB($deckID, $rgbMap);
        $name  = htmlspecialchars($deck['name']);
        $hasChildren = !empty($childrenMap[$deckID]);

        echo "<li class='contain' data-id='$deckID'>";
        echo   "<div class='container-deck'>";

        if ($hasChildren) {
            echo "<div class='plus'><i class='bx bxs-caret-down bx-flip-horizontal' style='color:#8e8e8e;font-size:24px'></i></div>";
        } else {
            echo "<div class='md5qdw8dq' style='width:30px;display:flex;align-items:center;'></div>";
        }

        echo   "<div class='title-to-review-second' onclick=\"window.location.href='flashcard.php?deck_id=$deckID'\">";
        echo     "<span class='title-second'>$name</span>";
        echo     "<div class='to-review'>
                    <span class='red'>{$rgb['red']}</span>
                    <span class='green'>{$rgb['green']}</span>
                    <span class='blue' style='color:#8497B0;'>/{$rgb['blue']}</span>
                  </div>";
        echo   "</div>";
        echo   "</div>";
        echo   "<div class='line'></div>";

        if ($hasChildren) {
            echo "<ul>";
            showDecks($deckID, $childrenMap, $deckMap, $userDeckIDs, $rgbMap);
            echo "</ul>";
        }

        echo "</li>";
    }
}
?>

<!-- Main Deck -->
<div class="wrapper-main">
    <div class="deck-layout">
        <ul>
            <li class="class-title">
                <div class="title-to-review" onclick="window.location.href='flashcard.php?deck_id=main'">
                    <span class="title">Main Deck</span>
                    <div class="to-review">
                        <span class="red"><?= $countMain['red'] ?></span>
                        <span class="green"><?= $countMain['green'] ?></span>
                        <span class="blue">/<?= $countMain['blue'] ?></span>
                    </div>
                </div>

                <div class="subdeck">
                    <ul>
                        <?php showDecks(null, $childrenMap, $deckMap, $userDeckIDs, $rgbMap); ?>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>