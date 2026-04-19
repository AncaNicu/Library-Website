<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Category</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">
</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Insert Category</h2>
            <form id="insertCategoryForm">
                <input type="text" class="form-control" placeholder="Enter new category" name="new_category_name" required>
                <button type="button" class="admin_actions_btn" onclick="insertCategory()">Insert Category</button>
            </form>
        </div>
    </div>

    <script>
        // fct pt inserarea categoriei din formular
        function insertCategory() {
            // creeaza un ob care are ca date elementele luate din formularul pt inserare
            var formData = {
                new_category_name: document.getElementById('insertCategoryForm').elements['new_category_name'].value
            };

            // foloseste Fetch API pt a face o cerere HTTP POST 
            fetch('../category_api/api_insert_category.php', {
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
