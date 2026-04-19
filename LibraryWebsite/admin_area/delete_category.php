<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">

</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Delete Category</h2>
            <form id="deleteCategoryForm">
                <input type="text" class="form-control" placeholder="Insert category" name="category_name" required>
                <button type="button" class="admin_actions_btn" onclick="deleteCategory()">Delete Category</button>
            </form>
        </div>
    </div>

    <script>
        // fct pt stergerea categoriei din formular
        function deleteCategory() {
            // creeaza un ob care are ca date elementele luate din formularul pt stergere
            var formData = {
                category_name: document.getElementById('deleteCategoryForm').elements['category_name'].value
            };

            // foloseste Fetch API pt a face o cerere HTTP DELETE 
            fetch('../category_api/api_delete_category.php', {
                method: 'DELETE', //specifica metoda HTTP ca fiind DELETE
                headers: {
                    'Content-Type': 'application/json', //specifica tipul continutului ca JSON
                },
                body: JSON.stringify(formData),//trimite formData ca JSON
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
