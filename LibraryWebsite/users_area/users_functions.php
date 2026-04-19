<?php
//fct pt a obtine numele unei categorii avand id-ul ei
function getCategoryNameById($categoryId, $conn) {

    $categoryQuery = "SELECT category_name FROM category WHERE category_id = '$categoryId'";
    $categoryName = mysqli_query($conn, $categoryQuery);

    if ($row = mysqli_fetch_assoc($categoryName)) {
        return $row['category_name'];
    } else {
        return null;
    }
}

//fct pt a obtine numele unei edituri avand id-ul ei
function getPublisherNameById($publisherId, $conn) {

    $publisherQuery = "SELECT publisher_name FROM publisher WHERE publisher_id = '$publisherId'";
    $publisherName = mysqli_query($conn, $publisherQuery);

    if ($row = mysqli_fetch_assoc($publisherName)) {
        return $row['publisher_name'];
    } else {
        return null;
    }
}
?>