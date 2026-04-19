<?php
include('../includes/connect.php');
include('admin_functions.php');

//daca butonul Insert Book a fost apasat
if (isset($_POST['update_book'])) {
    //datele cartii de actualizat
    $old_title = mysqli_real_escape_string($conn, $_POST['book_old_title']);
    $old_author = mysqli_real_escape_string($conn, $_POST['book_old_author']);
    $publisherOldName = mysqli_real_escape_string($conn, $_POST['selected_old_publisher']);
    $publisherOldId = getPublisherIdByName($publisherOldName, $conn);//obtine ID-ul editurii

    //noile date ale cartii
    $title = mysqli_real_escape_string($conn, $_POST['book_title']);
    $author = mysqli_real_escape_string($conn, $_POST['book_author']);
    $description = mysqli_real_escape_string($conn, $_POST['book_description']);
    $price = floatval($_POST['book_price']); //pretul e float
    $quantity = intval($_POST['book_quantity']); //cantitatea e intreg
    $categoryName = mysqli_real_escape_string($conn, $_POST['selected_category']);
    $publisherName = mysqli_real_escape_string($conn, $_POST['selected_publisher']);

    //obtine ID-ul pt categorie si pt editura
    $categoryId = getCategoryIdByName($categoryName, $conn);
    $publisherId = getPublisherIdByName($publisherName, $conn);

    //verif daca a fost aleasa o imagine pt coperta
    if (!empty($_FILES['book_cover']['name'])) {
        //obtine numele fisierului si locatia temporara
        $bookCoverName = $_FILES['book_cover']['name'];
        $tempBookCover = $_FILES['book_cover']['tmp_name'];

        //muta fisierul incarcat in folderul pt coperti
        move_uploaded_file($tempBookCover, "./book_covers/$bookCoverName");

        //actualizeaza $cover cu numele fisierului
        $cover = $bookCoverName;
    } else {
        //daca niciun fisier nu a fost incarcat, se va lua o imagine default
        $cover = './images/no_image_available.jpg';
    }

    //verif daca a fost aleasa o imagine pt autor
    if (!empty($_FILES['author_image']['name'])) {
        //obtine numele fisierului si locatia temporara
        $authorImageName = $_FILES['author_image']['name'];
        $tempAuthorImage = $_FILES['author_image']['tmp_name'];

        //muta fisierul incarcat in folderul pt autori
        move_uploaded_file($tempAuthorImage, "./authors/$authorImageName");

        //actualizeaza $authorImage cu numele fisierului
        $authorImage = $authorImageName;
    } else {
        //daca niciun fisier nu a fost incarcat, se va lua o imagine default
        $authorImage = './images/no_image_available.jpg';
    }

    //verif daca a fost ales un pdf
    if (!empty($_FILES['book_pdf']['name'])) {
        //obtine numele fisierului si locatia temporara
        $pdfFileName = $_FILES['book_pdf']['name'];
        $tempPdf = $_FILES['book_pdf']['tmp_name'];
    
        //muta fisierul incarcat in folderul pt pdf-uri
        move_uploaded_file($tempPdf, "./book_pdfs/$pdfFileName");
    
        //actualizeaza $pdf cu numele fisierului
        $pdf = $pdfFileName;
    } else {
        //daca niciun fisier nu a fost incarcat, se va lua un pdf default
        $pdf = './book_pdfs/MilkAndHoney.pdf';
    }     

    //cartea de editat nu exista
    if(!bookExists($old_title, $old_author, $publisherOldName, $conn)) {
        echo "<script>alert('Book does not exist');</script>";
    }
    else {
        //daca deja exista o carte care are ac titlu, ac autor si ac editura
        if(bookExists($title, $author, $publisherName, $conn) && ($title != $old_title || $author != $old_author || $publisherName != $publisherOldName)) { 
            echo "<script>alert('Book already exists');</script>";
        }
        else {
            if (updateBook($old_title, $old_author, $publisherOldId, $title, $author, $description, $cover, $authorImage, $pdf, $price, $quantity, $categoryId, $publisherId, $conn)) {
                echo "<script>alert('Book updated successfully');</script>";
            } else {
                echo "<script>alert('Error updating book');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../includes/admin_stylesheet.css">

</head>
<body>
    <div class="row">
        <div class="col left">
            <img src="../images/logo5.jpg" alt="Left Image">
        </div>
        <div class="col right">
            <h2>Edit Book</h2>
            <form method="post" action="" enctype="multipart/form-data">

                <input type="text" class="form-control" placeholder="Enter book title" name="book_old_title" required>
                <input type="text" class="form-control" placeholder="Enter book author" name="book_old_author" required>

                <select class="form-control" name="selected_old_publisher" required>
                    <option value="" disabled selected>Select old publisher</option>
                    <?php
                    //foloseste editurile obtinute cu endpount-ul api
                    if (isset($publishers) && is_array($publishers)) {
                        foreach ($publishers as $publisher) {
                            echo "<option value='{$publisher['publisher_name']}'>{$publisher['publisher_name']}</option>";
                        }
                    } 
                    ?>
                </select>

                <input type="text" class="form-control" placeholder="Enter book new title" name="book_title" required>
                <input type="text" class="form-control" placeholder="Enter book new author" name="book_author" required>
                <input type="text" class="form-control" placeholder="Enter book new description" name="book_description" required>

                <div class="file-input-group">
                    <input type="file" accept="image/*" name="book_cover" id="book_cover" style="display: none;" onchange="displayBookCoverFileName(this)">
                    <label for="book_cover" class="file-input-label">Browse Book Cover</label>
                    <div class="form-text" id="book_cover_file-name-placeholder">Choose a new image for the book cover</div>
                </div>

                <div class="file-input-group">
                    <input type="file" accept="image/*" name="author_image" id="author_image" style="display: none;" onchange="displayAuthorImageFileName(this)">
                    <label for="author_image" class="file-input-label">Browse Author Image</label>
                    <div class="form-text" id="author-image-file-name-placeholder">Choose a new image for the author</div>
                </div>

                <div class="file-input-group">
                    <input type="file" accept="application/pdf" name="book_pdf" id="book_pdf" style="display: none;" onchange="displayPdfFileName(this)">
                    <label for="book_pdf" class="file-input-label">Browse Book PDF</label>
                    <div class="form-text" id="pdf-file-name-placeholder">Choose a new PDF for the book</div>
                </div>

                <script>
                function displayAuthorImageFileName(input) {
                    var fileName = input.files[0].name;
                    document.getElementById('author-image-file-name-placeholder').innerText = fileName;
                }
                </script>

                <script>
                function displayBookCoverFileName(input) {
                    var fileName = input.files[0].name;
                    document.getElementById('book_cover_file-name-placeholder').innerText = fileName;
                }
                </script>

                <script>
                function displayPdfFileName(input) {
                    var fileName = input.files[0].name;
                    document.getElementById('pdf-file-name-placeholder').innerText = fileName;
                }
                </script>

                <input type="number" step="0.01" class="form-control" placeholder="Enter book new price" name="book_price" required>
                <input type="number" class="form-control" placeholder="Enter new quantity available" name="book_quantity" required>
                
                <!-- meniuri pt categorii si edituri -->
                <select class="form-control" name="selected_category" required>
                    <option value="" disabled selected>Select a category</option>
                    <?php
                    //foloseste categoriile obtinute cu endpount-ul api
                    if (isset($categories) && is_array($categories)) {
                        foreach ($categories as $category) {
                            echo "<option value='{$category['category_name']}'>{$category['category_name']}</option>";
                        }
                    }
                    ?>
                </select>


                <select class="form-control" name="selected_publisher" required>
                    <option value="" disabled selected>Select a publisher</option>
                    <?php
                    //foloseste editurile obtinute cu endpount-ul api
                    if (isset($publishers) && is_array($publishers)) {
                        foreach ($publishers as $publisher) {
                            echo "<option value='{$publisher['publisher_name']}'>{$publisher['publisher_name']}</option>";
                        }
                    } 
                    ?>
                </select>

                
                <button type="submit" name="update_book" class="admin_actions_btn">Update Book</button>
            </form>
        </div>
    </div>
</body>

<script>
    //se executa numai dupa ce documentul HTML a fost complet incarcat
    document.addEventListener('DOMContentLoaded', function () {
        //foloseste Fetch API pt a face o cerere HTTP GET
        fetch('../category_api/api_get_all_categories.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    //populeaza meniul pt selectarea categoriei
                    const categoryDropdown = document.querySelector('[name="selected_category"]');
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.category_name;
                        option.textContent = category.category_name;
                        categoryDropdown.appendChild(option);
                    });
                } else {
                    console.error('Error fetching categories:', data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    });


    //se executa numai dupa ce documentul HTML a fost complet incarcat
    document.addEventListener('DOMContentLoaded', function () {
        //foloseste Fetch API pt a face o cerere HTTP GET
        fetch('../publisher_api/api_get_all_publishers.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    //populeaza meniul pt selectarea editurii noi
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

    //se executa numai dupa ce documentul HTML a fost complet incarcat
    document.addEventListener('DOMContentLoaded', function () {
        //foloseste Fetch API pt a face o cerere HTTP GET
        fetch('../publisher_api/api_get_all_publishers.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    ////populeaza meniul pt selectarea editurii vechi
                    const publisherDropdown = document.querySelector('[name="selected_old_publisher"]');
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

</html>


