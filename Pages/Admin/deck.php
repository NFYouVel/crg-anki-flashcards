<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck</title>
    <link rel="icon" href="../../Logo/circle.png">
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
            width: 30px;
            height: 30px;
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
        .selectedDeck {
            background-color: #595959;
        }
        #confirmation, #assignDeckMenu {
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
            display: none;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: center;
            text-align: center;
        }
        #confirmation h1, #assignDeckMenu h1 {
            margin: 0;
        }
        #confirmation div, #assignDeckMenu div {
            display: flex;
            justify-content: space-evenly;
            width: 100%;
        }
        .button {
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
        table {
            width: 100%;
            font-size: 20px;
            border-collapse: collapse;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 5px 10px;
        }
        tr {
            transition: box-shadow 0.5s ease;
        }
        td {
            padding: 5px;
            word-break: break-word;
            white-space: normal;
        }
        th {
            position: sticky;
            z-index: 200;
            top: 0;
        }
        .short {
            word-break: normal;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }
        #deckTable {
            padding: 0;
            overflow: auto;
        }
        ::-webkit-scrollbar-track {
            background: #404040;
        }
        #updateDeck {
            display: none;
        }
    </style>
<script>
    loadDOM();
    //keep expanded decks
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

    function getDeckDetails(deckID) {
        var xmlhttp;
        if (window.XMLHttpRequest != null) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("deckTable").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "AJAX/getDeckDetails.php?deckID=" + deckID, true);
        xmlhttp.send();
    }

    function restoreExpandedIDs(expanded) {
        expanded.forEach(function (id) {
            const toggle = $("#" + id).siblings(".toggle");
            if (toggle.length > 0) {
                toggle.click();
            }
        });
    }

    //ajax add folder
    function addFolder(name, parent, type) {
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
        xmlhttp.open("GET", "AJAX/addFolder.php?name=" + name + "&parent=" + parent + "&type=" + type, true);
        xmlhttp.send();
    }

    //ajax delete folder
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

    //refresh dom
    function loadDOM() {
        deckID = "masterDeck";
        $(document).ready(function () {
            //expand and minimize decks
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
            //remove right click menu
            $("body").click(function () {
                // $(".menu").css({
                //     "pointer-events": "auto",
                //     cursor: "pointer",
                //     opacity: 1
                // });

                $("#deckMenu").css({
                    display: "none"
                });
            })

            //right click menu
            $(".label").on("contextmenu", function (e) {
                $(".menu").css({
                    "pointer-events": "auto",
                    cursor: "pointer",
                    opacity: 1
                });
                e.preventDefault();
                let label = $(this);
                let type = $(this).children("img").attr("id");
                if($(this).attr("id") == "masterDeck") {
                    $("#updateDeck").css({
                        "display": "none"
                    })
                    $("#rename").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                    $("#delete").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                    $("#addDeck").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                }
                else if (type == "deck") {
                    $("#updateDeck").css({
                        "display": "flex"
                    })
                    $("#addFolder").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                    $("#addDeck").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                }
                else if (type == "folder_folder") {
                    $("#updateDeck").css({
                        "display": "none"
                    })
                    $("#addDeck").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                    $("#addFolder").css({
                        "pointer-events": "auto",
                        cursor: "pointer",
                        opacity: 1
                    });
                }
                else if (type == "folder_deck") {
                    $("#updateDeck").css({
                        "display": "none"
                    })
                    $("#addFolder").css({
                        "pointer-events": "auto",
                        cursor: "pointer",
                        opacity: 1
                    });
                    $("#addFolder").css({
                        "pointer-events": "none",
                        opacity: 0.3
                    });
                }
                $("#deckMenu").css({
                    top: e.pageY + "px",
                    left: e.pageX + "px",
                    display: "block"
                });

                //click add folder
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
                            addFolder(name, parentID, 0);
                        }
                    });
                })

                //click add deck
                $("#addDeck").off("click").on("click", function () {
                    let parentID = label.attr("id");
                    const parent = label.closest("li");
                    const newFolder = `
                    <ul class = "maximized" style = "height: fit-content;">
                        <li>
                            <span class="label"><img src="../../Assets//Icons/deck.png" class="icon"> <input id = "input_newFolder" autofocus type = "text" placeholder = "New Folder"></span>
                        </li>
                    </ul>
                    `;
                    parent.append(newFolder);

                    $("#input_newFolder").on('keydown', function(e) {
                        if (e.key === 'Enter' || e.which === 13) {
                            var name = $(this).val();
                            var parent = $(this).closest("li").parent().siblings(".label");
                            var parentID = parent.attr("id");
                            addFolder(name, parentID, 1);
                        }
                    });
                })

                //click delete
                $("#delete").off("click").on("click", function () {
                    deckID = label.attr("id");
                    $.ajax({
                        url: "AJAX/getDeckName.php",
                        method: "POST",
                        data: { deck_id: deckID },
                        success: function (response) {
                            $("#deletedDeck").text(response);
                            $("#confirmation").css({ display: "flex" });
                        }
                    });
                    $("#confirmDelete").off("click").on("click", function () {
                        deleteFolder(deckID);
                        $("#confirmation").css({display: "none"});
                        alert("Deck succesfully deleted!");
                    });
                });

                $("#cancelDelete").off("click").on("click", function () {
                    $("#confirmation").css({display: "none"});
                });

                //click rename
                $("#rename").off("click").on("click", function () {
                    deckID = label.attr("id");
                    $.ajax({
                        url: 'AJAX/getDeckName.php',
                        type: 'POST',
                        data: {
                            deck_id: deckID
                        },
                        success: function(response) {
                            var deckName = response.trim();
                            $(label).html(`
                                <img src='../../Assets/Icons/folder.png' class='icon' id='folder' style='vertical-align: middle;'>
                                <form method='post' style='display: inline;'>
                                    <input type='hidden' name='deckID' value='${deckID}'>
                                    <input id='renameDeck' name='renameDeck' type='text' value='${deckName}' style='font-size: 16px; vertical-align: middle; width: auto;' autofocus>
                                </form>
                            `);
                        },
                    });
                });


                $("#renameDeck").on('keydown', function(e) {
                    if (e.key === 'Enter' || e.which === 13) {
                        $(this).closest("form").submit();
                    }
                });
            });

            $(".label").off("click").on("click", function () {
                let type = $(this).children("img").attr("id");
                if($(this).attr("id") == "masterDeck") {
                    $("#updateDeck").css({
                        "display": "none"
                    })
                }
                else if (type == "deck") {
                    $("#updateDeck").css({
                        "display": "flex"
                    })
                }
                else {
                    $("#updateDeck").css({
                        "display": "none"
                    })
                }
                $(".selectedDeck").removeClass("selectedDeck");
                $(this).addClass("selectedDeck");
                deckID = $(this).attr("id");
                $("#updateDeck").attr("href", "updateDeck.php?deckID=" + deckID);
                getDeckDetails(deckID);
            });

            $("#assignDeck").off("click").on("click", function () {
                $("#assignDeckMenu").css({
                    display: "flex"
                });
                $("#cancelAssign").off("click").on("click", function () {
                    $("#assignDeckMenu").css({
                        display: "none"
                    });
                });

                $("#assignUser").attr("href", "assignDeckUser.php?deckID=" + deckID);
                $("#assignClassroom").attr("href", "assignDeckClassroom.php?deckID=" + deckID);
            });
        });
    };
</script>

</head>
<body>
    <?php
        include "convertPinyin.php";
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
        if(isset($_POST["renameDeck"])) {
            $name = $_POST["renameDeck"];
            $deckID = $_POST["deckID"];
            mysqli_query($con, "UPDATE decks SET name = '$name' WHERE deck_id = '$deckID'");
        }
        function getDecks($parentID) {
            global $con;
            if($parentID == "root") {
                $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
            }
            else {
                $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
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

                        if($deck["is_leaf"] == 0) {
                            if(mysqli_num_rows(mysqli_query($con, "SELECT is_leaf FROM decks WHERE parent_deck_id = '$deckID' AND is_leaf = 1")) > 0) {
                                echo "
                                <li>
                                    <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                    <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_deck'> $name</span>
                                ";
                            }
                            else {
                                echo "
                                <li>
                                    <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                    <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_folder'> $name</span>
                                ";
                            }
                        }
                        else {
                            echo "
                            <li>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/deck.png' class = 'icon' id = 'deck'> $name</span>
                            ";
                        }
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
            <h1 id="deletedDeck"></h1>
            <div>
                <button class = "button" id = "cancelDelete">Cancel</button>
                <button class = "button" id = "confirmDelete">Confirm</button>
            </div>
        </div>
        <div id="assignDeckMenu">
            <h1>Which of these would you want to assign the deck to?</h1>
            <div>
                <button class = "button" id = "cancelAssign">Cancel</button>
                <a href="" id = "assignClassroom" class="button">Classroom</a>
                <a href="" id = "assignUser" class="button">Users</a>
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
                                <span class="label selectedDeck" id = "masterDeck"><img src="../../Assets//Icons/folder.png" class = "icon"> Master Deck Folder</span>
                                <?php
                                    getDecks("root");
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="details">
                <div id="header">
                    <h2>Deck Details</h2>
                    
                    <div style = 'display: flex; gap: 16px; height: 50%;'>
                        <a id = "assignDeck" class="button" style = "padding: 12px; width: auto; font-size: 24px; height: auto;">Assign Deck</a>
                        <a href="" id = "updateDeck" class="button" style = "padding: 12px; width: auto; font-size: 24px; height: auto;">Update Deck</a>
                    </div>
                </div>
                <div id = "deckTable" class="content">
                    <table>
                        <tr>
                            <th>No.</th>
                            <th>ID</th>
                            <th>Trad</th>
                            <th>Simp</th>
                            <th>Prio</th>
                            <th>Pinyin</th>
                            <th>Word Class</th>
                            <th>English</th>
                            <th>Indo</th>
                        </tr>
                        <?php
                            $count = 1;
                            $getCards = mysqli_query($con, "SELECT DISTINCT c.* FROM junction_deck_card dc JOIN cards c ON dc.card_id = c.card_id");
                            while($card = mysqli_fetch_array($getCards)) {
                                $cardID = $card["card_id"];
                                $traditional = $card["chinese_tc"];
                                $simplified = $card["chinese_sc"];
                                $prio = $card["priority"];
                                $pinyin = $card["pinyin"];
                                $class = $card["word_class"];
                                $eng = $card["meaning_eng"];
                                $indo = $card["meaning_ina"];
                                echo "
                                <tr>
                                    <td>$count</td>
                                    <td class = 'short'>$cardID</td>
                                    <td>$traditional</td>
                                    <td>$simplified</td>
                                    <td>$prio</td>
                                    <td>" . convert($pinyin) . "</td>
                                    <td class = 'short'>$class</td>
                                    <td>$eng</td>
                                    <td>$indo</td>
                                </tr>";
                                $count++;
                            }
                        ?>
                    </table>
                </div>
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