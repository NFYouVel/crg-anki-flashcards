<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck</title>
    <style>
        h1 {
            color: white;
            font-size: 50px;
        }
        input {
            height: 40px;
            width: 250px;
            border-radius: 12px;
            border: 2px solid #e9a345;
            font-size: 20px;
        }
        input::placeholder {
            color: #e9a345;
            font-size: 20px;
        }
        .button {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            width: 150px;
            height: 50px;
            background-color: #ffa72a;
            border-radius: 25px;
            font-size: 24px;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>

    <div id="container">
        <div id="header">
            <h1>Deck Overview</h1>
            <input type="text" placeholder = "&#128269;Search" onkeyup = "searchUser(this.value)">
            <a href="" class="button"><span>Add Deck</span></a>
        </div>
    </div>
</body>
<style>
    #deck {
        color: #ffa72a;
    }
</style>
</html>