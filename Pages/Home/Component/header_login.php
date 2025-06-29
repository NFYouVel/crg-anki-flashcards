<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        height: 100%;
    }

    .wrapper-header {
        display: flex;

    }

    .wrapper-header .header {
        display: flex;
        background-color: rgb(216, 149, 33);
        width: 100%;
        height: 9.65vh;
        align-items: center;
        justify-content: space-between;
    }

    .logo {
        display: flex;
        height: 90%;
        align-items: center;
        margin: 8px 0 8px 0.6%;
    }

    .wrapper-header .header .logo img {
        object-fit: cover;
        height: 87%;

        filter:
            drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white);
    }

    .right-bar {
        display: flex;
        height: 90%;
        width: auto;
    }

    .right-bar span {
        color: blue;
        font-size: 2.7vh;
        text-align: right;
        font-family: 'Nunito', sans-serif;
        font-weight: bold;
    }

    .right-bar .account-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .navbar {
        display: flex;
        width: 13vh;
        align-items: center;
        justify-content: center;
    }

    .navbar .icon {
        font-size: 7.65vh;
        cursor: pointer;
    }

    @media screen and (max-width: 768px) {
        .navbar .icon {
            font-size: 5vh;
        }

        .right-bar span {
            font-size: 1.7vh;
        }

        .logo {
            height: 62%;
            margin-left: 10px;
        }

        .navbar {
            display: flex;
            width: 8vh;
            align-items: center;
            justify-content: center;
        }


    }
</style>
<div class="wrapper-header">
    <!-- Untuk Logo di atas (header) -->
    <div class="header">
        <div class="logo">
            <img src="../../Logo/1080.png" alt="CRG Logo">
        </div>