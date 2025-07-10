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
        #tree {
            display: inline-block;
            min-width: max-content;
        }
        #tree input {
            font-size: 16px;
        }
        #tree, #tree ul {
            list-style: none;
            padding-left: 20px;
            position: relative;
        }

        #tree > * {
            overflow: visible;
        }

        #tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 9px;
            border-left: 1px dotted white;
            height: 100%;
        }

        #tree li {
            position: relative;
            padding-left: 24px;
            margin: 12px 0;
        }

        #tree li li::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 0;
            width: 12px;
            border-top: 1px dotted white;
        }

        #tree li:last-child::before {
            background: #1e1e1e;
        }

        .toggle {
            display: inline-block;
            vertical-align: middle;
        }
        #tree span {
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
            padding: 4px 8px;
        }

        #tree li > ul {
            height: 0;
            overflow: hidden;
        }

        .maximized {
            height: fit-content;
        }
        #deckMenu {
            position: absolute;
            width: 200px;
            background-color: #404040;
            color: white;
            font-size: 18px;
            display: none;
            z-index: 100;
        }
        .menu {
            padding: 8px;
            cursor: pointer;
        }
        .label:hover, .menu:hover {
            background-color: #595959;
        }
        #confirmation {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #143d59;
            width: 50%;
            height: 50%;
            border-radius: 25px;
            z-index: 200;
            padding: 12px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: center;
            text-align: center;
        }
        #confirmation h1 {
            margin: 0;
        }
        #confirmation div {
            display: flex;
            justify-content: space-evenly;
            width: 100%;
        }
        button {
            font-family: 'Arial', sans-serif;
            width: 200px;
            height: 75px;
            background-color: #ffa72a;
            border-radius: 25px;
            font-size: 32px;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
    </style>
<script>
    loadDOM();

    function getExpandedIDs() {
        let expanded = [];
        $("#tree .maximized").each(function () {
            let label = $(this).siblings(".label");
            if (label.length > 0) {
                expanded.push(label.attr("id"));
            }
        });
        return expanded;
    }

    function restoreExpandedIDs(expanded) {
        expanded.forEach(function (id) {
            const toggle = $("#" + id).siblings(".toggle");
            if (toggle.length > 0) {
                toggle.click();
            }
        });
    }

    function addFolder(name, parent) {
        const expandedBefore = getExpandedIDs();

        var xmlhttp;
        if (window.XMLHttpRequest != null) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("tree").innerHTML = xmlhttp.responseText;
                loadDOM();
                setTimeout(() => {
                    restoreExpandedIDs(expandedBefore);
                    $("#masterDeck").siblings("ul").css("height", "fit-content").addClass("maximized");
                    $("#masterDeck").siblings(".toggle").find("img").attr("src", "../../Assets/Icons/minimizeDeck.png");
                }, 10);

            }
        }
        xmlhttp.open("GET", "AJAX/addFolder.php?name=" + name + "&parent=" + parent, true);
        xmlhttp.send();
    }

    function deleteFolder(deckID) {
        const expandedBefore = getExpandedIDs();

        var xmlhttp;
        if (window.XMLHttpRequest != null) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("tree").innerHTML = xmlhttp.responseText;
                loadDOM();
                setTimeout(() => {
                    restoreExpandedIDs(expandedBefore);
                    $("#masterDeck").siblings("ul").css("height", "fit-content").addClass("maximized");
                    $("#masterDeck").siblings(".toggle").find("img").attr("src", "../../Assets/Icons/minimizeDeck.png");
                }, 10);
            }
        }
        xmlhttp.open("GET", "AJAX/deleteDeck.php?deckID=" + deckID, true);
        xmlhttp.send();
    }

    function loadDOM() {
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

        $(document).ready(function () {
            $("body").click(function () {
                $("#deckMenu").css({
                    display: "none"
                });
            })

            $(".label").on("contextmenu", function (e) {
                e.preventDefault();
                let label = $(this);
                $("#deckMenu").css({
                    top: e.pageY + "px",
                    left: e.pageX + "px",
                    display: "block"
                });

                $("#addFolder").off("click").on("click", function () {
                    let parentID = label.attr("id");
                    const parent = label.closest("li");
                    const newFolder = `
                    <ul class = "maximized" style = "height: fit-content;">
                        <li>
                            <span class="toggle"><img src="../../Assets//Icons/maximizeDeck.png" class="min"></span> 
                            <span class="label"><img src="../../Assets//Icons/folder.png" class="icon"> <input id = "input_newFolder" autofocus type = "text" placeholder = "New Folder"></span>
                        </li>
                    </ul>
                    `;
                    parent.append(newFolder);

                    $("#input_newFolder").on('keydown', function(e) {
                        if (e.key === 'Enter' || e.which === 13) {
                            var name = $(this).val();
                            var parent = $(this).closest("li").parent().siblings(".label");
                            var parentID = parent.attr("id");
                            addFolder(name, parentID);
                        }
                    });
                })

                $("#delete").off("click").on("click", function () {
                    let deckID = label.attr("id");

                    deleteFolder(deckID);
                });
            });
        });
    };
</script>

</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
        function getDecks($parentID) {
            global $con;
            if($parentID == "root") {
                $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
            }
            else {
                $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
            }
            if(mysqli_num_rows($getDecks) > 0) {
                if($parentID == "root") {
                    echo "<ul class = 'maximized' style = 'height: fit-content;'>";
                }
                else {
                    echo "<ul>";
                }
                    while($deck = mysqli_fetch_assoc($getDecks)) {
                        $deckID = $deck["deck_id"];
                        $name = $deck["name"];

                        echo "
                            <li>
                                <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon'> $name</span>
                            ";
                            getDecks($deckID);
                        echo"</li>
                        ";  
                    }
                echo "</ul>";
            }
        }
    ?>
    <div id="container">
        <div id="deckMenu">
            <div id = "addFolder" class="menu">Add Folder Inside</div>
            <div id = "addDeck" class="menu">Add Deck Inside</div>
            <div id = "rename" class="menu">Rename</div>
            <div id = "delete" class="menu">Delete</div>
        </div>
        <div id="confirmation">
            <h1>Are you sure you want to delete this deck and it's child decks?</h1>
            <h1 id="deletedDeck">HSK</h1>
            <div>
                <button>Cancel</button>
                <button>Confirm</button>
            </div>
        </div>
        <div id="header">
            <h1 style = "margin-top: 0;">Deck Overview</h1>
        </div>
        <div id="main">
            <div id="list">
                <h2>Deck List</h2>
                <div class="content">
                    <div id="wrapper">
                        <ul id="tree">
                            <li>
                                <span class="toggle"><img src="../../Assets//Icons/minimizeDeck.png" class = "min"></span> 
                                <span class="label" id = "masterDeck"><img src="../../Assets//Icons/folder.png" class = "icon"> Master Deck Folder</span>
                                <?php
                                    getDecks("root");
                                ?>
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