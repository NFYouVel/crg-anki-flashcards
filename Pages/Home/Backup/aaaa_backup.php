<li class="class-title">
                    <!-- Colored Title -->
                    <div class="title-to-review">
                        <!-- Deck Title -->
                        <span class="title">
                        Student List (7)
                        </span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <span class="click">Add Deck to Classroom</span>
                        </div>
                    </div>
                </li>

                                        // function showDecks($parentID = null) {
                        //     global $con, $user_id;
                        //     $getDeckIDs = mysqli_query($con, "SELECT deck_id FROM junction_deck_user WHERE user_id = '$user_id'");
                        //     $ownedDecks = [];
                        //     while ($row = mysqli_fetch_assoc($getDeckIDs)) {
                        //         $ownedDecks[] = $row['deck_id'];
                        //     }           

                        //     if ($parentID === null) {
                        //         $getDecks = mysqli_query($con, "SELECT * FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
                        //     } else {
                        //         $getDecks = mysqli_query($con, "SELECT * FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
                        //     }


                        //     while ($deck = mysqli_fetch_assoc($getDecks)) {

                        //         // echo "Deck saat ini: " . $deck['deck_id'] . "<br>";
                        //         if (in_array($deck['deck_id'], $ownedDecks)) {
                        //             $temp_deck_id = $deck['deck_id'];
                        //             echo "<!-- Debug: nemu deck: " . $deck['deck_id'] . " -->";
                        //             echo "<li class='contain'>";
                        //             echo "<div class='title-to-review-second'>";
                        //             echo "<span class='title-second' onclick='goToFlashcard(this)' data-id='$temp_deck_id'>" . htmlspecialchars($deck['name']) . "</span>";
                        //             echo "<div class='to-review'>
                        //                     <span class='green'>169</span>
                        //                     <span class='red'>28</span>
                        //                     <span class='blue'>1638</span>
                        //                   </div>";
                        //             echo "</div>";
                        //             echo "<div class='line'></div>";

                        //             // Recursive call buat subdeck (kalau is_leaf = 0)
                        //             if ($deck['is_leaf'] == 0) {
                        //                 echo "<ul>"; // start subdeck
                        //                 showDecks($deck['deck_id']);
                        //                 echo "</ul>"; // end subdeck
                        //             }

                        //             echo "</li>";

                        //         }

                        //     }
                        // }
