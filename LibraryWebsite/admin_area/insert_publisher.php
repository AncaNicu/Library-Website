<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Publisher</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">
</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Insert Publisher</h2>
            <form id="insertPublisherForm">
                <input type="text" class="form-control" placeholder="Enter new publisher" name="new_publisher_name" required>
                <button type="button" class="admin_actions_btn" onclick="insertPublisher()">Insert Publisher</button>
            </form>
        </div>
    </div>

    <script>
        // fct pt inserarea editurii din formular
        function insertPublisher() {
            // creeaza un ob care are ca date elementele luate din formularul pt inserare
            var formData = {
                new_publisher_name: document.getElementById('insertPublisherForm').elements['new_publisher_name'].value
            };

            // foloseste Fetch API pt a face o cerere HTTP POST
            fetch('../publisher_api/api_insert_publisher.php', {
                method: 'POST', //specifica metoda HTTP ca fiind POST
                headers: {
                    'Content-Type': 'application/json', //specifica tipul continutului ca JSON
                },
                body: JSON.stringify(formData), //trimite formData ca JSON
            })
            //proceseaza raspunsul si afiseaza mesajul corespunzator primit de la server
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
