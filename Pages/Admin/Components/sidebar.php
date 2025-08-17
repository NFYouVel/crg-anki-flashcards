<link rel="icon" href="../../../Logo/circle.png">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap');

    * {
        font-family: 'Nunito', sans-serif;
    }
    html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        background-color: #262626;
    }

    #container {
        width: 85%;
        margin-left: 15%;
        padding: 24px 24px;
        box-sizing: border-box;
        /* border: 2px solid white; */
    }
    #sidebar {
        background-color: #143d59;
        height: 100%;
        width: 15%;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
    }

    a {
        color: white;
        text-decoration: none;
    }

    nav li {
        list-style: none;
    }

    #home {
        font-size: 40px;
        margin: 25px 0;
    }

    /* #sidebar > * {
        margin-left: 35px;
    } */
    #navWrapper {
        margin-left: 35px;
    }

    nav {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-top: 12px;
    }

    nav a {
        font-size: 20px;
        font-weight: bold;
    }

    nav li {
        margin: 12px 0;
    }

    nav ul {
        display: none;
    }

    nav li:hover>ul {
        display: block;
    }

    .logo {
        width: 80%;
        filter: drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white);
    }

    #logout {
        position: absolute; 
        bottom: 24px; 
        left: 35px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    #logout a {
        font-size: 20px;
        font-weight: bold;
        color: #ffa72a;
        transition: all 0.3s ease;
    }

    #logout a:hover {
        transform: translateY(-5px);
    }
</style>
<div id="sidebar">
    <div style = "width: 100%; height: 15%; display: flex; justify-content: center; align-items: center">
        <img class="logo" src="../../Logo/1080.png" alt="CRG Logo" style="cursor: pointer;" onclick="window.location.href = '../Home/home_page.php'">
    </div>
    <div id="navWrapper">

        <a href="homepage.php" id='home'>Home</a>
        <nav>
            <li>
                <a id="user" href="overview_user.php">User</a>
                <ul>
                    <li><a id="overview_user" href="overview_user.php">Overview</a></li>
                    <li><a id="role" href="role.php">Role</a></li>
                </ul>
            </li>
            <li><a id="classroom" href="classroom.php">Classroom</a></li>
            <li><a id="dictionary" href="dictionary.php?page=0">Dictionary</a></li>
            <li>
                <a id = "sentence" href="overview_sentence.php">Sentence</a>
                <ul>
                    <li><a id="overview_sentence" href="overview_sentence.php">Overview</a></li>
                    <li><a id="report" href="report.php">Report</a></li>
                </ul>
            </li>
            <li>
                <a id="deck" href="deck.php">Deck</a>
                <ul>
                    <li><a id="deckList" href="deck.php">Deck List</a></li>
                    <li><a id="deckPool" href="deckPool.php">Deck Pool</a></li>
                    <li><a id="deckAssigned" href="deckAssigned.php">Assigned Deck</a></li>
                </ul>
            </li>
        </nav>
    </div>

    <div id="logout">
        <a href="../Home/setting.php"><span>⚙️</span>Settings</a>
        <a href="../Home/exit.php"><span style = "transform: scaleX(-1); display: inline-block;">⍈</span> Logout</a>
    </div>
</div>