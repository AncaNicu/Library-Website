<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Book</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">
</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Delete Book</h2>
            <form id="deleteBookForm">
                <input type="text" class="form-control" placeholder="Enter book title" name="book_title" required>
                <input type="text" class="form-control" placeholder="Enter book author" name="book_author" required>
                
                <select class="form-control" name="selected_publisher" required>
                    <option value="" disabled selected>Select a publisher</option>
                    <?php
                    //se iau editurile din baza de date folosind end-point-ul pt obtinerea tuturor editurilor
                    if (isset($publishers) && is_array($publishers)) {
                        foreach ($publishers as $publisher) {
                            echo "<option value='{$publisher['publisher_name']}'>{$publisher['publisher_name']}</option>";
                        }
                    } 
                    ?>
                </select>

                <button type="button" class="admin_actions_btn" onclick="deleteBook()">Delete Book</button>
            </form>
        </div>
    </div>

    <script>
        // fct pt stergerea cartii din formular
        function deleteBook() {
            // creeaza un ob care are ca date elementele luate din formularul pt stergere
            var formData = {
                book_title: document.getElementById('deleteBookForm').elements['book_title'].value,
                book_author: document.getElementById('deleteBookForm').elements['book_author'].value,
                selected_publisher: document.getElementById('deleteBookForm').elements['selected_publisher'].value
            };

            // foloseste Fetch API pt a face o cerere HTTP DELETE 
            fetch('../book_api/api_delete_book.php', {
                method: 'DELETE', //specifica metoda HTTP ca fiind DELETE
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
        
        //se executa numai dupa ce documentul HTML a fost complet incarcat
        document.addEventListener('DOMContentLoaded', function () {
            //foloseste Fetch API pt a face o cerere HTTP GET
            fetch('../publisher_api/api_get_all_publishers.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        //populeaza meniul pt selectarea editurii
                        const publisherDropdown = document.querySelector('[name="selected_publisher"]');
                        data.publishers.forEach(publisher => {
                            const option = document.createElement('option');
                            option.value = publisher.publisher_name;
                            option.textContent = publisher.publisher_name;
                            publisherDropdown.appendChild(option);
                        });
                    } else {
                        console.error('Error fetching publishers:', data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>
</html>
