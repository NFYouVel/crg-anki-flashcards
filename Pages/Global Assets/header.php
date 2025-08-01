<style>
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
        z-index: 100;
        position: relative;
    }

    .wrapper-header .header {
        display: flex;
        background-color: rgb(255, 165, 5);
        width: 100%;
        height: 9vh;
        align-items: center;
    }

    .logo {
        display: flex;
        height: 90%;
        align-items: center;
        margin: 8px;
    }

    .wrapper-header .header .logo img {
        object-fit: cover;
        height: 87%;

        filter:
            drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white);
    }

    @media screen and (max-width: 768px) {
        .logo {
            padding: 3px 0;
        }
    }
</style>
<div class="wrapper-header">
    <!-- Untuk Logo di atas (header) -->
    <div class="header">
        <div class="logo">
            <img src="../../Logo/1080.png" alt="CRG Logo" onclick="BackHome()" style="cursor:pointer;">
        </div>
    </div>
</div>

<script>
    function BackHome() {
        window.location.href = "index.php"
    }

</script>