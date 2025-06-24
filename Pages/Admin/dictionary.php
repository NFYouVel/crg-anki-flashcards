<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary</title>
    <style>
        h1 {
            color: white;
            font-size: 50px;
            margin: 16px 0;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        input {
            height: 50px;
            width: 275px;
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
            width: 200px;
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
        <div id="header">
            <h1>Dictionary (Card) Overview</h1>
            <input type="text" placeholder = "&#128269;Search" onkeyup = "searchUser(this.value)">
            <form action="addCards.php" method="POST" enctype="multipart/form-data">
                <input id = "file" name = "excel_file" style = "display: none" class = "button" type="file" onchange="this.form.submit()">
                <label class = "button" for="file">Import</label>
            </form>
        </div>
    </div>
</body>
<style>
    #dictionary {
        color: #ffa72a;
    }
</style>
</html>