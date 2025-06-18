<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu</title>
    <style>
        h1, h3 {
            color: white;
            margin: 0;
        }
        #menu {
            padding: 72px 72px;
        }
        h3 {
            font-weight: 200;
            margin-bottom: 12px;
        }
        #actions {
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            height: 150px;
            /* border: 2px solid white; */
        }
        #actions a {
            color: #5793c9;
            font-weight: bolder;
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>

    <div id="menu">
        <h1>Hello, Herodian!</h1>
        <h3>What would you like to do?</h3>

        <div id="actions">
            <a href="">Manage User</a>
            <a href="">Manage Classroom</a>
            <a href="">Manage Dictionary (Card)</a>
            <a href="">Manage Example Sentence</a>
            <a href="">Manage Deck</a>
        </div>
    </div>
</body>
</html>