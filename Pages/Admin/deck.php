<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck</title>
    <script src="../../library/jquery.js"></script>
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
            width: 35%;
        }
        #details {
            width: 65%;
        }
        .content {
            width: 100%;
            border: 1px solid white;
            height: 450px;
            color: white;
            box-sizing: border-box;
        }
        #wrapper {
            overflow-x: auto;
            overflow-y: auto;
            white-space: nowrap;
            width: 100%;
            height: 100%;
        }
        .tree {
            display: inline-block;
            min-width: max-content;
        }
        .tree, .tree ul {
            list-style: none;
            padding-left: 20px;
            position: relative;
        }

        .tree > * {
            overflow: visible;
        }

        .tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 9px;
            border-left: 1px dotted white;
            height: 100%;
        }

        .tree li {
            position: relative;
            padding-left: 24px;
            margin: 12px 0;
        }

        .tree li li::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 0;
            width: 12px;
            border-top: 1px dotted white;
        }

        .tree li:last-child::before {
            background: #1e1e1e;
        }

       .toggle {
            display: inline-block;
            vertical-align: middle;
            cursor: pointer;
        }

        .min {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

        .icon {
            width: 24px;
            height: 24px;
            vertical-align: middle;
        }

        .label {
            display: inline-block;
            vertical-align: middle;
        }

        .tree li > ul {
            height: 0;
            overflow: hidden;
        }

        .maximized {
            height: fit-content;
        }
    </style>
<script>
    $(document).ready(function () {
        $(".toggle").click(function () {
            let $ul = $(this).closest("li").children("ul");
            let $img = $(this).find("img");
            if ($ul.hasClass("maximized")) {
                $ul.css("height", 0).removeClass("maximized");
                $img.attr("src", "../../Assets/Icons/maximizeDeck.png");
            } else {
                $ul.css("height", "fit-content").addClass("maximized");
                $img.attr("src", "../../Assets/Icons/minimizeDeck.png");
            }
        });
    })
</script>

</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>

    <div id="container">
        <div id="header">
            <h1 style = "margin-top: 0;">Deck Overview</h1>
        </div>
        <div id="main">
            <div id="list">
                <h2>Deck List</h2>
                <div class="content">
                    <div id="wrapper">
                        <ul class="tree">
                            <li>
                                <span class="toggle"><img src="../../Assets//Icons/minimizeDeck.png" class = "min"></span> 
                                <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> Master Deck Folder</span>
                                <ul class = "maximized" style = "height: fit-content;">
                                    <li>
                                        <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                        <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> Active Chinese</span>
                                    </li>

                                    <li>
                                        <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                        <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> HSK</span>
                                    </li>
                                    <li>
                                        <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                        <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> TOCFL</span>
                                        <ul>
                                            <li>
                                                <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                                <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> TOCFL A1</span>
                                            </li>
                                            <li>
                                                <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                                <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> TOCFL A2</span>
                                            </li>
                                            <li>
                                                <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                                <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> TOCFL B1</span>
                                                <ul>
                                                    <li>
                                                        <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class = "min"></span> 
                                                        <span class="label"><img src="../../Assets//Icons/folder.png" class = "icon"> Deck 01</span>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
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