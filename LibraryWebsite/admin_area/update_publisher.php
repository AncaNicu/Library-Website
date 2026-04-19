<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Publisher</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">
</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Edit Publisher</h2>
            <form id="editPublisherForm">
                <input type="text" class="form-control" placeholder="Enter current publisher" name="publisher_name" required>
                <input type="text" class="form-control" placeholder="Enter new publisher name" name="new_publisher_name" required>
                <button type="button" class="admin_actions_btn" onclick="editPublisher()">Edit Publisher</button>
            </form>
        </div>
    </div>

    <script>
        // fct pt editarea editurii din formular
        function editPublisher() {
            // creeaza un ob care are ca date elementele luate din formularul pt editare
            var formData = {
                publisher_name: document.getElementById('editPublisherForm').elements['publisher_name'].value,
                new_publisher_name: document.getElementById('editPublisherForm').elements['new_publisher_name'].value
            };

            // foloseste Fetch API pt a face o cerere HTTP PUT
            fetch('../publisher_api/api_update_publisher.php', {
                method: 'PUT', //specifica metoda HTTP ca fiind PUT
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
