document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.criteria').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const status = this.dataset.status;
            const cardId = document.getElementById('flashcard-form').dataset.cardId;
            const stages = this.dataset.cs;
            document.getElementById('loading-overlay').style.display = 'flex';

            // Tampilkan pesan berdasarkan status
            // const messageBox = document.getElementById('flashcard-message');
            // if (status === 'forgot') {
            //     messageBox.innerText = 'It’s okay! Let’s try again!';
            // } else if (status === 'hard') {
            //     messageBox.innerText = 'Nice! Keep pushing!';
            // } else if (status === 'remember') {
            //     messageBox.innerText = 'Great job! You nailed it!';
            // }
            // messageBox.style.display = 'flex';


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


$(document).on("click", "#button-report", function (e) {
    e.preventDefault();

    if ($('input[name="reason[]"]:checked').length === 0) {
        e.preventDefault(); // stop form submit
        alert('Please select at least one reason!');
    } else {
        let formData = $("#report-sentence").serialize();

        $.ajax({
            url: "jQuery/ajax_sendReport.php",
            type: "GET",
            data: formData,
            success: function (response) {
                alert(response);
                $(".wrapper-report").hide();
                $("#report-sentence")[0].reset();
                sentenceCode = $('button-report').data("sentence-id")
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert("Failed to send report!");
            }
        });
    }

});


