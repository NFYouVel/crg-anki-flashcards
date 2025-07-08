<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck</title>
    <style>
        h2, h1 {
            color: white;
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
        #container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        #main {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 16px;
            flex: 1;
        }
        #list {
            flex: 1.2;
        }
        #details {
            flex: 2;
        }
        .content {
            width: 100%;
            border: 1px solid white;
            height: 80%;
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
        </div>
        <div id="main">
            <div id="list">
                <h2>Deck List</h2>
                <div class="content"></div>
            </div>
            <div id="details">
                <h2>Deck Details</h2>
                <div class="content"></div>
            </div>
        </div>
    </div>
</body>
<style>
    #deck {
        color: #ffa72a;
    }
</style>
</html>