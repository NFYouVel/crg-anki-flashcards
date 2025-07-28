document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.criteria').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const status = this.dataset.status;
            const cardId = document.getElementById('flashcard-form').dataset.cardId;
            const stages = this.dataset.cs;
            document.getElementById('loading-overlay').style.display = 'flex';
            // Tampilkan pesan berdasarkan status
            const messageBox = document.getElementById('flashcard-message');
            if (status === 'forgot') {
                messageBox.innerText = 'It’s okay! Let’s try again!';
            } else if (status === 'hard') {
                messageBox.innerText = 'Nice! Keep pushing!';
            } else if (status === 'remember') {
                messageBox.innerText = 'Great job! You nailed it!';
            }
            messageBox.style.display = 'flex';


            const xhr = new XMLHttpRequest();
            xhr.open('GET', `flashcard_algorithm.php?card_id=${cardId}&status=${status}&stage=${stages}`, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href = 'flashcard.php';
                }
            };
            xhr.send();
        });
    });
});
