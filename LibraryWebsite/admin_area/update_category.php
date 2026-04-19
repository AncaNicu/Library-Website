<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">
</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Edit Category</h2>
            <form id="editCategoryForm">
                <input type="text" class="form-control" placeholder="Enter current category" name="category_name" required>
                <input type="text" class="form-control" placeholder="Enter new category name" name="new_category_name" required>
                <button type="button" class="admin_actions_btn" onclick="editCategory()">Edit Category</button>
            </form>
        </div>
    </div>

    <script>
        // fct pt editarea categoriei din formular
        function editCategory() {
            // creeaza un ob care are ca date elementele luate din formularul pt editare
            var formData = {
                category_name: document.getElementById('editCategoryForm').elements['category_name'].value,
                new_category_name: document.getElementById('editCategoryForm').elements['new_category_name'].value
            };

            // foloseste Fetch API pt a face o cerere HTTP PUT 
            fetch('../category_api/api_update_category.php', {
                method: 'PUT', //specifica metoda HTTP ca fiind PUT
                headers: {
                    'Content-Type': 'application/json',//specifica tipul continutului ca JSON
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
