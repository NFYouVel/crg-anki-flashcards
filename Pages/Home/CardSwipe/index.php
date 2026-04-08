<script>
    const params = new URLSearchParams(window.location.search);
    const deckId = params.get("deckId");
    const skipTutorial = localStorage.getItem("skipTutorial") === "true";

    if (skipTutorial) {
        window.location.href = "cardSwipe.php?deckId=" + deckId;
    } else {
        window.location.href = "cardSwipeTutorial.php?deckId=" + deckId;
    }
</script>