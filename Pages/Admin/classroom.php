<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom</title>
    <style>
        h1 {
            color: white;
            font-size: 50px;
        }
        #heading {
            height: fit-content;
            width: 90%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #action {
            display: flex;
            align-items: center;
            gap: 24px;
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
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="container">
        <div id="heading">
            <h1>Classroom</h1>
            <div id="action">
                <input type="text" placeholder = "&#128269;Search" onkeyup = "">
                <a class = 'button' href = 'addClassroom.php'><span>Add</span></a>
            </div>
        </div>
    </div>
</body>
<style>
    #classroom {
        color: #ffa72a;
    }
</style>
</html>