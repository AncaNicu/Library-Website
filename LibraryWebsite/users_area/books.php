<?php
include('../includes/connect.php');
include('../admin_area/admin_functions.php');
session_start();

//se vor afisa 6 carti per pagina
$booksPerPage = 6;

//determina pagina crt.
//daca a fost selectata o pag, pag crt e acea pag, altfel, e pag 1
//pag selectata se ia din URL prin $_GET
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

//calculeza offset-ul pt interogarea SQL
$offset = ($current_page - 1) * $booksPerPage;

//aduce din bd si afiseaza cartile pt pag crt si pt categoria sau editura selectata
//categoria si editura sunt luate din URL prin $_GET
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : ''; //categoria selectata
$publisherFilter = isset($_GET['publisher']) ? $_GET['publisher'] : ''; //editura selectata
//verif daca a fost introdus ceva in search field
$searchQuery = isset($_GET['search_data']) ? $_GET['search_data'] : '';
$booksQuery = "SELECT * FROM book";//query pt a selecta toate coloanele din tabelul book

//daca avem ceva in campul de search
if (!empty($searchQuery)) {
    $booksQuery .= " WHERE book_title LIKE '%$searchQuery%'";
} elseif (!empty($categoryFilter)) {
    //daca a fost selectata o categorie
    $categoryId = getCategoryIdByName($categoryFilter, $conn);
    $booksQuery .= " WHERE category_id = '$categoryId'";
} elseif (!empty($publisherFilter)) {
    //daca a fost selectata o editura
    $publisherId = getPublisherIdByName($publisherFilter, $conn);
    $booksQuery .= " WHERE publisher_id = '$publisherId'";
}

//alipeste conditia la $booksQuery pt limitarea de la paginatie
$booksQuery .= " LIMIT $offset, $booksPerPage";

$booksResult = mysqli_query($conn, $booksQuery);

//pt a determina nr total de pagini
$totalBooksQuery = "SELECT COUNT(*) AS total FROM book";

//pt nr total de pag pt search input
if (!empty($searchQuery)) {
    $totalBooksQuery .= " WHERE book_title LIKE '%$searchQuery%'";
}
//pt nr total de pag pt o categorie
elseif (!empty($categoryFilter)) {
    $categoryId = getCategoryIdByName($categoryFilter, $conn);
    $totalBooksQuery .= " WHERE category_id = '$categoryId'";
}
//pt nr total de carti pt o editura 
elseif (!empty($publisherFilter)) {
    $publisherId = getPublisherIdByName($publisherFilter, $conn);
    $totalBooksQuery .= " WHERE publisher_id = '$publisherId'";
}

$totalBooksResult = mysqli_query($conn, $totalBooksQuery);
$totalBooks = mysqli_fetch_assoc($totalBooksResult)['total'];

//calculeaza nr total de carti
$totalPages = ceil($totalBooks / $booksPerPage);

//pt add to cart
//daca a fost apasat add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    //daca user-ul e logat
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        //verif daca userul are deja un cos activ
        $activeCartQuery = "SELECT * FROM cart WHERE user_id = $userId AND order_status = 'in process'";
        $activeCartResult = mysqli_query($conn, $activeCartQuery);

        if (mysqli_num_rows($activeCartResult) == 0) {
            //nu avem cos activ => se creeaza unul
            $createCartQuery = "INSERT INTO cart (user_id, order_status) VALUES ($userId, 'in process')";
            mysqli_query($conn, $createCartQuery);
        }

        //obtine cart_id (pt cosul existent sau creat anterior)
        $activeCartResult = mysqli_query($conn, $activeCartQuery);
        $cartId = mysqli_fetch_assoc($activeCartResult)['cart_id'];

        //obtine book_id pt cartea pe care am apasat
        $bookId = $_POST['book_id'];

        //verif daca cartea deja exista in cos
        $checkBookQuery = "SELECT * FROM cart_item WHERE cart_id = $cartId AND book_id = $bookId";
        $checkBookResult = mysqli_query($conn, $checkBookQuery);

        //daca cartea nu e in cos, o adauga
        if (mysqli_num_rows($checkBookResult) == 0) {
            $addToCartQuery = "INSERT INTO cart_item (cart_id, book_id, item_quantity) VALUES ($cartId, $bookId, 1)";
            mysqli_query($conn, $addToCartQuery);

            echo "<script>
                alert('Book successfuly added to the cart!');
                window.location.href = 'shopping_cart.php';
            </script>";
        } else {
            echo "<script>
                alert('This book is already in the cart!');
                window.location.href = 'books.php';
            </script>";
        }
    } else {
        //daca nu e logat => redirectioneaza carte login
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Page</title>
    <style>
        /* container e cel ce contine cele 2 divuri */
        .container {
            display: flex;
            height: 100vh; /* dimensiunea maxima */
        }

        .left-div {
            width: 30%;
            padding: 20px;
            background-color: #f2f2f2;
            overflow-y: auto;
            box-sizing: border-box;
            max-height: 100vh;
        }

        /* divul din dreapta */
        .right-div {
            width: 70%;
            padding: 20px;
            text-align: center;
            overflow-y: auto; /* daca continutul divului depaseste inaltimea max => apare bara de scroll */
        }

        .right-div h1 {
            margin-bottom: 20px;
            
        }

        .no_books_msg {
            font-size: 25px;
        }

        /* divul care contine cartile */
        .books-container {
            display: flex;
            flex-wrap: wrap;
            text-align: center;
        }

        /* card-ul pt fiecare carte */
        .book {
            width: calc(33.33% - 20px); /* 3 carti pe rand cu spatiu de 20px intre ele */
            margin-bottom: 20px;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ddd;
            margin-right: 20px;
            border-radius: 10px;
        }

        .book img {
            width: 80%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book h3 {
            margin-bottom: 5px;
        }

        .book p {
            margin: 5px 0;
        }

        .book_card_btn {
            padding: 10px;
            background-color: #3039a1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
        }

        .book_card_btn:hover {
            background-color: #8cabff;
        }

        /* paginile */
        .pagination a{
            margin-right: 15px;
            text-decoration: none;
        }

        .pagination {
            text-align: center;
        }

        /* meniul din stanga */
        .left_menu {
            text-align: center;
            background-color: #333;
            color: white;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 5px;
            margin-top: 0px;
        }

        .left_menu a {
            text-decoration: none;
            color: white;
        }

        .menu {
            list-style: none;
            padding: 0;
        }

        .menu li a {
            border-radius: 5px;
            display: block;
            padding: 5px;
            text-decoration: none;
            color: #333;
            font-size: 17px;
        }

        .menu li {
            border-radius: 5px;
            overflow: hidden; 
            background-color: #f2f2f2;
            margin-bottom: 2px;
        }

        .left_menu h2 {
            margin-top: 0px; 
            margin-bottom: 2px; 
        }

        .left_menu_categories,
        .left_menu_publishers {
            list-style: none;
            padding: 0;
            margin: 0; 
        }

        .left_menu_categories li,
        .left_menu_publishers li {
            border-radius: 5px;
            overflow: hidden;
            background-color: #f2f2f2;
            margin-bottom: 3px; 
        }

        .menu a:hover,
        .left_menu_categories li a:hover,
        .left_menu_publishers li a:hover {
            background-color: #ddd;
        }

        .menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 17px;
        }

        .menu a:hover {
            background-color: #ddd;
        }

        #btn_container {
            display: flex;
        }

        .add_to_cart_btn:disabled {
            background-color: #ccc; 
            cursor: not-allowed; /* acest buton nu se mai poate apasa */
            opacity: 1; /*seteaza opacitatea la 1 pt ca textul sa fie complet vizibil */
        }

        .add_to_cart_btn:hover:disabled {
            background-color: #ccc; 
        }

    </style>
</head>

<body>
    <?php
        include('navbar.php');
    ?>
    <div class="container">
        <!-- div-ul din stanga ocupa 30% din spatiu si are lista cu cat., edituri si all books -->
        <div class="left-div">
            <div class="left_menu">
                <h2 class="left_menu">Categories</h2>
                <ul class='menu left_menu_categories'>
                </ul>
            </div>

            <div class="left_menu">
                <h2 class="left_menu">Publishers</h2>
                <ul class='menu left_menu_publishers'>
                </ul>
            </div>

            <!-- daca a fost selectat butonul All Books, se adauga ? in URL -->
            <h2 class="left_menu"><a href='?'>All Books</a></h2>
        </div>

        <!-- div-ul din dreapta ocupa 70% din spatiu si contine cartile -->
        <div class="right-div">
            <h1>Books</h1>
            <?php
            //daca exista carti de afisat (in cat selectata sau editura selectata sau in bd)
            //=> se afiseaza
            if (mysqli_num_rows($booksResult) > 0) {
                echo "<div class='books-container'>";
                while ($book = mysqli_fetch_assoc($booksResult)) {
                    //fiecare carte e intr-un div separat
                    echo "<div class='book'>";
                        echo "<img src='../admin_area/book_covers/{$book['book_cover']}' alt='{$book['book_title']}'>";
                        echo "<h3>{$book['book_title']}</h3>";
                        echo "<p>Author: {$book['book_author']}</p>";
                        echo "<p>Price: $" . number_format($book['book_price'], 2) . "</p>";
                        echo "<div id='btn_container'>";
                            echo "<form method='post'>";
                                echo "<input type='hidden' name='book_id' value='{$book['book_id']}'>";

                                echo "<button type='submit' name='add_to_cart' class='book_card_btn add_to_cart_btn' " . ($book['quantity_available'] == 0 ? 'disabled' : '') . ">
                                        Add to Cart
                                    </button>";
                                
                            echo "</form>";
                            echo "<button class='book_card_btn' onclick='viewDetails({$book['book_id']})'>View Details</button>";
                        echo "</div>";
                    echo "</div>";
                }
                echo "</div>";

                //paginile
                if ($totalPages > 1) {
                    echo "<div class='pagination'>";
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $filterParam = !empty($categoryFilter) ? "&category=$categoryFilter" : (!empty($publisherFilter) ? "&publisher=$publisherFilter" : "");
                        $searchParam = !empty($searchQuery) ? "&search_data=$searchQuery" : "";
                        //se alipeste searchParam la URL
                        echo "<a href='?page=$i$filterParam$searchParam'>$i</a>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p class='no_books_msg'>No books found</p>";
            }
            ?>
        </div>
    </div>

    <?php
    include('../includes/users_footer.php');
    //inchide conexiunea la bd
    mysqli_close($conn);
    ?>

    <!-- Add this script to fetch categories asynchronously -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fetch categories from the API
        fetch('../category_api/api_get_all_categories.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update the menu with fetched categories
                    const categoryMenu = document.querySelector('.left_menu_categories');
                    data.categories.forEach(category => {
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.href = `?category=${category.category_name}`;
                        a.textContent = category.category_name;
                        li.appendChild(a);
                        categoryMenu.appendChild(li);
                    });
                } else {
                    console.error('Error fetching categories:', data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    });
</script>
<!-- Add this script to fetch publishers asynchronously -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fetch publishers from the API
        fetch('../publisher_api/api_get_all_publishers.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update the menu with fetched publishers
                    const publisherMenu = document.querySelector('.left_menu_publishers');
                    data.publishers.forEach(publisher => {
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.href = `?publisher=${publisher.publisher_name}`;
                        a.textContent = publisher.publisher_name;
                        li.appendChild(a);
                        publisherMenu.appendChild(li);
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

<!-- fct care trimite la book_details.php -->
<script>
    function viewDetails(bookId) {
        //redirectioneaza catre book_details.php avand ca parametru in URL id-ul cartii pe care am apasat
        window.location.href = `book_details.php?book_id=${bookId}`;
    }
</script>
</body>
</html>