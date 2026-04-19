<?php
include('../includes/connect.php');
//fct pt a sterge o carte
function deleteBook($title, $author, $publisherId, $conn) {
    //scapa de orice caractere speciale din variabila $title
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);

    $deleteBookQuery = "DELETE FROM book WHERE book_title = '$title' AND book_author = '$author' AND publisher_id = $publisherId";
    $result = mysqli_query($conn, $deleteBookQuery);

    return $result;
}

//fct pt a obtine toate editurile din baza de date
function getPublishers($conn) {
    $publishers = array();
    $getPublishersQuery = "SELECT * FROM publisher";
    $result = mysqli_query($conn, $getPublishersQuery);

    while ($row = mysqli_fetch_assoc($result)) {
        $publishers[] = $row;
    }

    return $publishers;
}

//fct pt a vedea daca o carte exista in baza de date
function bookExists($title, $author, $publisherName, $conn) {
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);

    //pt a obtine id-ul editurii avand numele ei
    $checkPublisherQuery = "SELECT * FROM publisher WHERE publisher_name = '$publisherName'";
    $resultPublisher = mysqli_query($conn, $checkPublisherQuery);
    $publisherData = mysqli_fetch_assoc($resultPublisher);
    $publisherId = $publisherData['publisher_id'];

    $checkBookQuery = "SELECT * FROM book WHERE book_title = '$title' AND book_author = '$author' AND publisher_id = $publisherId";
    $resultBook = mysqli_query($conn, $checkBookQuery);

    return mysqli_num_rows($resultBook) > 0;
}

//fct pt a vedea ce id are o editura cu un nume dat
function getPublisherIdByName($publisherName, $conn) {
    $categoryName = mysqli_real_escape_string($conn, $publisherName);

    $getPublisherIdQuery = "SELECT publisher_id FROM publisher WHERE publisher_name = '$publisherName'";
    $result = mysqli_query($conn, $getPublisherIdQuery);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['publisher_id'];
    } else {
        return null;
    }
}

// fct pt a sterge categoria si toate cartile ascociate ei
function deleteCategory($categoryId, $conn) {
    // sterge cartile asociate categoriei
    $deleteBooksQuery = "DELETE FROM book WHERE category_id = $categoryId";
    $resultBooks = mysqli_query($conn, $deleteBooksQuery);

    // sterge categoria
    $deleteCategoryQuery = "DELETE FROM category WHERE category_id = $categoryId";
    $resultCategory = mysqli_query($conn, $deleteCategoryQuery);

    return $resultBooks && $resultCategory;
}

// fct pt a sterge editura si toate cartile ascociate ei
function deletePublisher($publisherId, $conn) {
    // sterge cartile asociate editurii
    $deleteBooksQuery = "DELETE FROM book WHERE publisher_id = $publisherId";
    $resultBooks = mysqli_query($conn, $deleteBooksQuery);

    // sterge editura
    $deletePublisherQuery = "DELETE FROM publisher WHERE publisher_id = $publisherId";
    $resultPublisher = mysqli_query($conn, $deletePublisherQuery);

    return $resultBooks && $resultPublisher;
}

//fct pt a obtine toate categoriile din baza de date
function getCategories($conn) {
    $categories = array();
    $getCategoriesQuery = "SELECT * FROM category";
    $result = mysqli_query($conn, $getCategoriesQuery);

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    return $categories;
}

//fct pt a insera o noua carte in baza de date
function insertBook($title, $author, $description, $cover, $authorImage, $pdf, $price, $quantity, $categoryId, $publisherId, $conn) {
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $description = mysqli_real_escape_string($conn, $description);
    $cover = mysqli_real_escape_string($conn, $cover);
    $authorImage = mysqli_real_escape_string($conn, $authorImage);
    $pdf = mysqli_real_escape_string($conn, $pdf);

    $insertBookQuery = "INSERT INTO book (book_title, book_author, book_description, book_cover, author_image, book_pdf, book_price, quantity_available, category_id, publisher_id)
                        VALUES ('$title', '$author', '$description', '$cover', '$authorImage', '$pdf', $price, $quantity, $categoryId, $publisherId)";
    
    $result = mysqli_query($conn, $insertBookQuery);

    return $result;
}

//fct pt a obtine id-ul unei categorii cu nume dat
function getCategoryIdByName($categoryName, $conn) {
    $categoryName = mysqli_real_escape_string($conn, $categoryName);

    $getCategoryIdQuery = "SELECT category_id FROM category WHERE category_name = '$categoryName'";
    $result = mysqli_query($conn, $getCategoryIdQuery);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['category_id'];
    } else {
        return null;
    }
}
// fct pt a insera o noua categorie
function insertCategory($categoryName, $conn) {
    $categoryName = mysqli_real_escape_string($conn, $categoryName);

    $insertCategoryQuery = "INSERT INTO category (category_name) VALUES ('$categoryName')";
    $result = mysqli_query($conn, $insertCategoryQuery);

    return $result;
}

// fct pt a insera o noua editura
function insertPublisher($publisherName, $conn) {
    $publisherName = mysqli_real_escape_string($conn, $publisherName);

    $insertPublisherQuery = "INSERT INTO publisher (publisher_name) VALUES ('$publisherName')";
    $result = mysqli_query($conn, $insertPublisherQuery);

    return $result;
}

// fct pt a actualiza o carte
function updateBook($old_title, $old_author, $old_publisher_id, $title, $author, $description, $cover, $authorImage, $pdf, $price, $quantity, $categoryId, $publisherId, $conn) {
    $old_title = mysqli_real_escape_string($conn, $old_title);
    $old_author = mysqli_real_escape_string($conn, $old_author);
    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $description = mysqli_real_escape_string($conn, $description);
    $cover = mysqli_real_escape_string($conn, $cover);
    $authorImage = mysqli_real_escape_string($conn, $authorImage);
    $pdf = mysqli_real_escape_string($conn, $pdf);

    $updateBookQuery = "UPDATE book SET 
                        book_title = '$title',
                        book_author = '$author',
                        book_description = '$description',
                        book_cover = '$cover',
                        author_image = '$authorImage',
                        book_pdf = '$pdf',
                        book_price = $price,
                        quantity_available = $quantity,
                        category_id = $categoryId,
                        publisher_id = $publisherId
                        WHERE book_title = '$old_title' AND book_author = '$old_author' AND publisher_id = $old_publisher_id";
    
    $result = mysqli_query($conn, $updateBookQuery);

    return $result;
}

// fct pt a actualiza numele unei categorii
function updateCategory($categoryId, $newCategoryName, $conn) {
    $newCategoryName = mysqli_real_escape_string($conn, $newCategoryName);

    $updateCategoryQuery = "UPDATE category SET category_name = '$newCategoryName' WHERE category_id = $categoryId";
    $result = mysqli_query($conn, $updateCategoryQuery);

    return $result;
}
// fct pt a actualiza numele unei edituri
function updatePublisher($publisherId, $newPublisherName, $conn) {
    $newPublisherName = mysqli_real_escape_string($conn, $newPublisherName);

    $updatePublisherQuery = "UPDATE publisher SET publisher_name = '$newPublisherName' WHERE publisher_id = $publisherId";
    $result = mysqli_query($conn, $updatePublisherQuery);

    return $result;
}

?>