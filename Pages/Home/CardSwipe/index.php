<script>
    const params = new URLSearchParams(window.location.search);
    const deckId = params.get("deckId");

    window.location.href = "cardSwipeTutorial.php?deckId=" + deckId;
</script>