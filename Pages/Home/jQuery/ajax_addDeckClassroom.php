<?php
    include "../../../SQL_Queries/connection.php";
    $classroomID = $_GET["classroomID"];
    $teacherID = $_GET["teacherID"];
    function addParentClassroom($deckID) {
        global $con, $classroomID, $teacherID;
        $parentID = mysqli_query($con, "SELECT parent_deck_id FROM decks WHERE deck_id = '$deckID'");
        $parentID = mysqli_fetch_assoc($parentID);
        $parentID = $parentID ? $parentID["parent_deck_id"] : null;

        //ngitung parent deck ini punya berapa anak
        $countChild = mysqli_query($con, "SELECT COUNT(*) AS total FROM decks WHERE parent_deck_id = '$parentID'");
        $countChild = mysqli_fetch_assoc($countChild);
        $countChild = $countChild["total"];

        $checkChildren = mysqli_query($con, "SELECT COUNT(*) AS total
                                            FROM junction_deck_classroom AS deck_classroom
                                            JOIN decks AS deck ON deck_classroom.deck_id = deck.deck_id
                                            WHERE deck_classroom.classroom_id = '$classroomID'
                                            AND deck.parent_deck_id = '$parentID'");
        $checkChildren = mysqli_fetch_assoc($checkChildren);
        $checkChildren = $checkChildren["total"];

        if($parentID && $countChild == $checkChildren) {
            // Only insert if not already present
            $exists = mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$parentID' AND classroom_id = '$classroomID'");
            if (mysqli_num_rows($exists) == 0) {
                mysqli_query($con, "INSERT INTO junction_deck_classroom (deck_id, classroom_id) VALUES ('$parentID', '$classroomID')");
            }
            addParentClassroom($parentID);
        }
    }
    function addParentUser($deckID, $studentID) {
        global $con, $teacherID;
        $parentID = mysqli_query($con, "SELECT parent_deck_id FROM decks WHERE deck_id = '$deckID'");
        $parentID = mysqli_fetch_assoc($parentID);
        $parentID = $parentID ? $parentID["parent_deck_id"] : null;

        $countChild = mysqli_query($con, "SELECT COUNT(*) AS total FROM decks WHERE parent_deck_id = '$parentID'");
        $countChild = mysqli_fetch_assoc($countChild);
        $countChild = $countChild["total"];
        $checkChildren = mysqli_query($con, "SELECT COUNT(*) AS total
                                            FROM junction_deck_user AS deck_user
                                            JOIN decks AS deck ON deck_user.deck_id = deck.deck_id
                                            WHERE deck_user.user_id = '$studentID'
                                            AND deck.parent_deck_id = '$parentID'");
        $checkChildren = mysqli_fetch_assoc($checkChildren);
        $checkChildren = $checkChildren["total"];

        if($parentID && $countChild == $checkChildren) {
            // Only insert if not already present
            $exists = mysqli_query($con, "SELECT 1 FROM junction_deck_user WHERE deck_id = '$parentID' AND user_id = '$studentID'");
            if (mysqli_num_rows($exists) == 0) {
                mysqli_query($con, "INSERT INTO junction_deck_user (deck_id, user_id) VALUES ('$parentID', '$studentID')");
            }
            addParentUser($parentID, $studentID);
        }
    }

    function addDeck($parentID) {
        global $con, $classroomID, $teacherID;
        $getDecks = mysqli_query($con, "SELECT deck_id, parent_deck_id, is_leaf FROM decks WHERE deck_id = '$parentID'");
        if (mysqli_num_rows($getDecks) > 0) {
            if($deck = mysqli_fetch_assoc($getDecks)) {
                $deckID = $deck["deck_id"];
                $isLeaf = $deck["is_leaf"];

                // Only insert if not already present

                $owned = mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM deck_pool WHERE deck_id = '$deckID' AND user_id = '$teacherID'")) > 0;
                $exists = mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroomID'");
                if (mysqli_num_rows($exists) == 0 && $owned) {
                    mysqli_query($con, "INSERT INTO junction_deck_classroom (deck_id, classroom_id) VALUES ('$deckID', '$classroomID')");
                }

                //new feature (check if all child decks of the parent deck has been added)
                addParentClassroom($deckID);

                if($owned) {
                    $getStudents = mysqli_query($con, "SELECT user_id, classroom_role_id FROM junction_classroom_user WHERE classroom_id = '$classroomID'");
                    while($students = mysqli_fetch_assoc($getStudents)) {
                        $studentID = $students["user_id"];
                        $role = $students["classroom_role_id"];
                        $check = mysqli_query($con, "SELECT 1 FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$studentID'");
                        if (mysqli_num_rows($check) == 0 && $role == 3) {
                            mysqli_query($con, "INSERT INTO junction_deck_user (deck_id, user_id) VALUES ('$deckID', '$studentID')");
    
                            addParentUser($deckID, $studentID);
    
                            $getCards = mysqli_query($con, "SELECT card_id FROM junction_deck_card WHERE deck_id = '$deckID'");
                            $query = "INSERT INTO card_progress (user_id, card_id) VALUES ";
                            $count = 1;
                            while($card = mysqli_fetch_assoc($getCards)) {
                                $cardID = $card["card_id"];
                                if($count == 35) {
                                    $query = substr($query, 0, -2);
                                    mysqli_query($con, $query);
                                    $query = "INSERT INTO card_progress (user_id, card_id) VALUES ";
                                    $count = 1;
                                }
                                $exists = mysqli_query($con, "SELECT 1 FROM card_progress WHERE user_id = '$studentID' AND card_id = '$cardID'");
                                if (mysqli_num_rows($exists) == 0) {
                                    $query .= "('$studentID', $cardID), ";
                                    $count++;
                                }
                                else {
                                    mysqli_query($con, "UPDATE card_progress SET is_assigned = 1 WHERE user_id = '$studentID' AND card_id = $cardID AND is_assigned = 0");
                                }
                            }
                            if ($count > 1) {
                                $query = substr($query, 0, -2);
                                mysqli_query($con, $query);
                            }
                        }
                    }
                }

                $getChildren = mysqli_query($con, "SELECT deck_id FROM decks WHERE parent_deck_id = '$deckID' ORDER BY name ASC");
                while($children = mysqli_fetch_assoc($getChildren)) {
                    addDeck($children["deck_id"]);
                }
            }
        }
    }
    $deckID = $_GET["deckID"];
    addDeck($deckID);

    $rootDecks = [];
    function getRoot($parentID = null) {
        global $con, $teacherID, $rootDecks;
        if ($parentID == null) {
            $getDecks = mysqli_query($con, "SELECT deck.deck_id, deck.parent_deck_id
                                            FROM deck_pool AS deck_user
                                            JOIN decks AS deck 
                                            ON deck_user.deck_id = deck.deck_id
                                            WHERE deck_user.user_id = '$teacherID'");
        } 
        else {
            $getDecks = mysqli_query($con, "SELECT deck_id, parent_deck_id FROM decks WHERE deck_id = '$parentID'");
        }
        while ($deck = mysqli_fetch_assoc($getDecks)) {
            if (!in_array($deck["deck_id"], $rootDecks)) {
                $rootDecks[] = $deck["deck_id"];
            }
            if ($deck["parent_deck_id"] !== null) {
                getRoot($deck["parent_deck_id"]);
            } 
        }
    }
    getRoot();
    
    function showDecks($parentID = null) {
        global $con, $teacherID, $rootDecks, $classroom_id;
        if($parentID == null) {
            $getDecks = mysqli_query($con, "SELECT name, deck_id, is_leaf FROM decks WHERE parent_deck_id IS NULL");
        }
        else {
            $getDecks = mysqli_query($con, "SELECT name, deck_id, is_leaf FROM decks WHERE parent_deck_id = '$parentID'");
        }
        while($deck = mysqli_fetch_assoc($getDecks)) {
            $deckID = $deck["deck_id"];
            if(in_array($deckID, $rootDecks)) {
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
                                if(mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroom_id'")) > 0) {
                                    echo "<span onclick=\"removeDeck('$deckID')\" class = 'addDeck added'>+</span>";
                                }
                                else {
                                    echo "<span onclick=\"addDeck('$deckID')\" class='addDeck'>+</span>";
                                }
                            echo "</div>";
                        echo "</div>";
                        showDecks($deckID);
                    echo "</div>";
                } else {
                    echo "<div class = 'deck'>";
                        echo "<div class = 'labelWrapper'>";
                            echo "<div class = 'label'>";
                                echo "<div class = 'deckTitle'>";
                                    echo "<span style = 'opacity: 0;' class = 'expand'>▶</span>";
                                    echo "<span class = 'deckName'>$name</span>";
                                echo "</div>";
                                if(mysqli_num_rows(mysqli_query($con, "SELECT classroom_id FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroom_id'")) > 0) {
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
    showDecks();
?>