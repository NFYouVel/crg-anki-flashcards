<?php
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);
$role_id = $line['role'];
if ($role_id != 2) {
    header("Location: ../Login");
}

if ($line['role'] == 0) {
    $role = "Admin";
} else if ($line['role'] == 1) {
    $role = "Teacher";
} else {
    $role = "Student";
}

// User ID Student
$user_id_student = $_SESSION['user_id_student'];
$query_user_id_student = mysqli_query($con, "SELECT * FROM users WHERE user_id = '$user_id_student'");
$line_student = mysqli_fetch_assoc($query_user_id_student);
$student_name = $line_student['name'];

// GET Deck ID
$deck_id = $_GET['deck_id'];
$query_deck_id_student = mysqli_query($con, "SELECT * FROM decks WHERE deck_id = '$deck_id'");
if (mysqli_num_rows($query_deck_id_student) === 0) {
    $name = "Main Deck";
} else {
    $line_deck = mysqli_fetch_assoc($query_deck_id_student);
    $name = $line_deck['name'];
}

// GET RGB
include_once "repetition_flashcard_student.php";
include "../../SQL_Queries/connection.php";
$counts = mysqli_fetch_assoc($query_flashcard_rbg_count);
$blue = $counts['blue'];
$green = $counts['green'];
$red = $counts['red'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../Home//CSS/deck_progress.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
</head>

<body>

    <!-- Header -->
    <?php include "Component/header_login.php" ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <span class="as" style="cursor: pointer;" onclick="Mode()"><?php echo $role ?> Mode</span>
        </div>
        <script>
            function Mode() {
                window.location.href = "home_page_teacher_student.php";
            }
        </script>

        <div class="navbar">
            <span class="icon">&#9776;</span>
        </div>
    </div>
    </div>
    </div>
    <?php include "Component/account_logout.php"; ?>

    <!-- Colored Title -->
    <div class="title-user">
        <!-- Deck Title -->
        <div class="title-sp">
            <span>Student Progress</span>
            <span><?php echo $student_name ?></span>
        </div>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="click-reset" id="click">Reset</span>
            <span class="click-delete" id="click">Delete</span>
        </div>
    </div>
    <div class="title-to-review">
        <!-- Deck Title -->
        <span class="title-name"><?php echo $name; ?></span>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="green"><?php echo $green; ?></span>
            <span class="red"><?php echo $red; ?></span>
            <span class="blue"><?php echo $blue; ?></span>
        </div>
    </div>
    <div class="wrapper-delete">
        <div class="delete">
            <div class="title-delete"><span>Delete Deck</span></div>
            <div class="explanation">
                <span>Delete Deck</span>
                <span class="delete-deck">"<?php echo $name; ?>"</span>
                <span class="delete-deck">From Student</span>
                <span class="delete-deck-to-user">"<?php echo $student_name; ?>"?</span>
                <span class="br">This action cannot be undone.</span>
            </div>
            <div class="button">
                <button class="button-cancel">Cancel</button>
                <button class="button-delete">Delete</button>
            </div>
        </div>
    </div>
    <div class="wrapper-reset">
        <div class="reset">
            <div class="title-reset"><span>Reset Deck</span></div>
            <div class="explanation">
                <span>Reset Deck</span>
                <span class="delete-deck">"<?php echo $name; ?>"</span>
                <span class="delete-deck">From Student</span>
                <span class="delete-deck-to-user">"<?php echo $student_name; ?>"?</span>
                <span class="br">This action cannot be undone.</span>
            </div>
            <div class="button">
                <button class="button-cancel">Cancel</button>
                <button class="button-delete" id="button-reset">Reset</button>
            </div>
        </div>
    </div>

    <div class="wrapper-loading">
        <div id="loading">
            <div class="spinner"></div>
        </div>
    </div>
    <div class="wrapper-deck-progress">
        <div class="container-deck-progress">
            <div class="status-group">
                <label class="status" style="color: green;">
                    <input type="checkbox" name="status[]" value="new">
                    <span class="dot"></span>
                    New Cards
                </label>

                <label class="status" style="color: red;">
                    <input type="checkbox" name="status[]" value="weak">
                    <span class="dot"></span>
                    Weak Cards
                </label>

                <label class="status" style="color: gray;">
                    <input type="checkbox" name="status[]" value="unreviewed">
                    <span class="dot"></span>
                    Unreviewed
                </label>
            </div>

            <table>
                <tr class="title">
                    <th></th>
                    <th class="title-words">Card</th>
                    <th>Pinyin</th>
                    <th>Meaning</th>
                </tr>
                <tbody id="flashcard-data"></tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination" id="pagination-controls" style="margin-top: 20px; text-align: center;"></div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        const deck_id = "<?php echo $deck_id; ?>";
        const student_id = "<?php echo $user_id_student; ?>";

        $("#button-reset").click(function(){
            $.ajax({
                url: "../Home/jQuery/ajax_resetDeck.php",
                method: "GET",
                data: {
                    deck_id: deck_id,
                    user_id_student: student_id
                }
            })
        });

        function loadFlashcards(page = 1) {
            $('#loading').show();

            const selectedStatuses = $("input[name='status[]']:checked").map(function() {
                return this.value;
            }).get();

            $.ajax({
                url: "../Home/jQuery/ajax_page_deck_progress.php",
                method: "GET",
                data: {
                    page: page,
                    deck_id: deck_id,
                    user_id_student: student_id,
                    status: selectedStatuses
                },
                beforeSend: function() {
                    $('.wrapper-loading').fadeIn(100);
                },

                // Hide loading setelah sukses
                success: function(data) {
                    const result = JSON.parse(data);
                    $('#flashcard-data').html(result.rows);
                    $('#pagination-controls').html(result.pagination);

                    $('html, body').animate({
                        scrollTop: 0
                    }, 300);

                    $('.wrapper-loading').fadeOut(100); // hide loading
                },

                // Biar tetep disembunyiin kalau error
                error: function() {
                    $('.wrapper-loading').fadeOut(100);
                    alert("Something went wrong!");
                },
                complete: function() {
                    $('#loading').hide(); // Sembunyikan loading setelah selesai
                }
            });
        }

        // Initial load
        loadFlashcards();

        // Handle pagination click
        $(document).on('click', '.pagination-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadFlashcards(page);
        });

        $("input[name='status[]']").on("change", function() {
            loadFlashcards(); // reload dengan status terpilih
        });
    });
</script>

</html>