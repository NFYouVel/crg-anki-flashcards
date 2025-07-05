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
    }

    #sidebar {
        background-color: #143d59;
        height: 100%;
        width: 15%;
        position: fixed;
        left: 0;
        top: 0;
    }

    /* #container, #sidebar {
        padding-top: 9vh;
    } */

    a {
        color: white;
        text-decoration: none;
    }

    li {
        list-style: none;
    }

    #home {
        font-size: 40px;
        margin: 25px 0;
    }

    #sidebar {
        display: flex;
        flex-direction: column;
    }

    #sidebar>* {
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

    ul {
        display: none;
    }

    li:hover>ul {
        display: block;
    }
</style>
<div id="sidebar">
    <a href="homepage.php" id='home'>Home</a>
    <nav>
        <li>
            <a id="user" href="">User</a>
            <ul>
                <li><a id="overview_user" href="overview_user.php">Overview</a></li>
                <li><a id="role" href="role.php">Role</a></li>
            </ul>
        </li>
        <li><a id="classroom" href="classroom.php">Classroom</a></li>
        <li><a id="dictionary" href="dictionary.php?limit=0">Dictionary</a></li>
        <li>
            <a id = "sentence" href="">Sentence</a>
            <ul>
                <li><a id="overview_sentence" href="overview_sentence.php">Overview</a></li>
                <li><a id="report" href="report.php">Report</a></li>
            </ul>
        </li>
        <li><a id="deck" href="deck.php">Deck</a></li>
    </nav>
</div>