<?php
    include "../../../SQL_Queries/connection.php";
    
    
    function getDecks($parentID) {
        $classroom_id = $_GET["classroomID"];
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
                                if(mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroom_id'")) > 0) {
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
    $search = $_GET["deckName"];
    if($search == "") {
        getDecks("root");
    }
    else {
        $getDecks = mysqli_query($con, "SELECT deck_id FROM decks WHERE name LIKE '%$search%'");
        while ($row = mysqli_fetch_assoc($getDecks)) {
            getDecks($row["deck_id"]);
        }
    }
?>