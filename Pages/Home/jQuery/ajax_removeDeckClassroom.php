<?php
    include "../../../SQL_Queries/connection.php";
    $classroomID = $_GET["classroomID"];
    function deleteParentsClassroom($deckID) {
        global $classroomID, $con;
        $parentID = mysqli_query($con, "SELECT parent_deck_id FROM decks WHERE deck_id = '$deckID'");
        $parentID = mysqli_fetch_assoc($parentID);
        $parentID = $parentID["parent_deck_id"];

        mysqli_query($con, "DELETE FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroomID'");

        if($parentID !== null) {
            deleteParentsClassroom($parentID);
        }
    }
    function deleteParentsUser($deckID, $userID) {
        global $con;
        $parentID = mysqli_query($con, "SELECT parent_deck_id FROM decks WHERE deck_id = '$deckID'");
        $parentID = mysqli_fetch_assoc($parentID);
        $parentID = $parentID["parent_deck_id"];

        mysqli_query($con, "DELETE FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$userID'");

        if($parentID !== null) {
            deleteParentsUser($parentID, $userID);
        }
    }
    function deleteDeck($parentID) {
        global $con;
        global $classroomID;

        $getDecks = mysqli_query($con, "SELECT deck_id, parent_deck_id, is_leaf FROM decks WHERE deck_id = '$parentID'");
        if (mysqli_num_rows($getDecks) > 0) {
            if ($deck = mysqli_fetch_assoc($getDecks)) {
                $deckID = $deck["deck_id"];
                $isLeaf = $deck["is_leaf"];

                // Delete from junction_deck_classroom if exists
                $exists = mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroomID'");
                if (mysqli_num_rows($exists) > 0) {
                    deleteParentsClassroom($deckID);
                    // mysqli_query($con, "DELETE FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroomID'");

                    $getStudents = mysqli_query($con, "SELECT user_id, classroom_role_id FROM junction_classroom_user WHERE classroom_id = '$classroomID'");
                    while ($students = mysqli_fetch_assoc($getStudents)) {
                        $studentID = $students["user_id"];
                        $role = $students["classroom_role_id"];

                        $check = mysqli_query($con, "SELECT 1 FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$studentID'");
                        if (mysqli_num_rows($check) > 0 && $role == 3) {
                            // Delete from junction_deck_user
                            deleteParentsUser($deckID, $studentID);
                            // mysqli_query($con, "DELETE FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$studentID'");

                            mysqli_query($con, "
                                UPDATE card_progress cp
                                JOIN junction_deck_card jdc ON cp.card_id = jdc.card_id
                                SET cp.is_assigned = 0
                                WHERE jdc.deck_id = '$deckID' AND cp.user_id = '$studentID'
                            ");
                        }
                    }
                }

                // Recursively delete children
                $getChildren = mysqli_query($con, "SELECT deck_id FROM decks WHERE parent_deck_id = '$deckID' ORDER BY name ASC");
                while ($children = mysqli_fetch_assoc($getChildren)) {
                    deleteDeck($children["deck_id"]);
                }
            }
        }
    }

    $deckID = $_GET["deckID"];
    deleteDeck($deckID);
    function getDecks($parentID) {
        global $classroomID;
        global $con;
        if ($parentID == "root") {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
        } else {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE deck_id = '$parentID'");
        }
        if (mysqli_num_rows($getDecks) > 0) {
            while ($deck = mysqli_fetch_assoc($getDecks)) {
                $deckID = $deck["deck_id"];
                $name = $deck["name"];
                $isLeaf = $deck["is_leaf"];

                if ($isLeaf == 0) {
                    echo "<div class = 'deck'>";
                        echo "<div class = 'labelWrapper'>";
                            echo "<div class = 'label'>";
                                echo "<div class = 'deckTitle'>";
                                    echo "<span class = 'expand'>▶</span>";
                                    echo "<span class = 'deckName'>$name</span>";
                                echo "</div>";
                                if(mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroomID'")) > 0) {
                                    echo "<span onclick=\"removeDeck('$deckID')\" class = 'addDeck added'>+</span>";
                                }
                                else {
                                    echo "<span onclick=\"addDeck('$deckID')\" class='addDeck'>+</span>";
                                }
                            echo "</div>";
                        echo "</div>";
                        $getChildren = mysqli_query($con, "SELECT deck_id FROM decks WHERE parent_deck_id = '$deckID' ORDER BY name ASC");
                        while($children = mysqli_fetch_assoc($getChildren)) {
                            getDecks($children["deck_id"]);
                        }
                    echo "</div>";
                } else {
                    echo "<div class = 'deck'>";
                        echo "<div class = 'labelWrapper'>";
                            echo "<div class = 'label'>";
                                echo "<div class = 'deckTitle'>";
                                    echo "<span style = 'opacity: 0;' class = 'expand'>▶</span>";
                                    echo "<span class = 'deckName'>$name</span>";
                                echo "</div>";
                                if(mysqli_num_rows(mysqli_query($con, "SELECT classroom_id FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroomID'")) > 0) {
                                    echo "<span onclick=\"removeDeck('$deckID')\" class = 'addDeck added'>+</span>";
                                }
                                else {
                                    echo "<span onclick=\"addDeck('$deckID')\" class='addDeck'>+</span>";
                                }
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }
            }
        }
    }
    getDecks("root");
?>
