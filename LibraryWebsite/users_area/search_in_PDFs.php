<?php
include('../includes/connect.php');
session_start();

//fct pt a realiza cautarea cu Solr
function solrSearch($query, $order, $page) {
    //construieste URL-ul pt cautarea cu Solr
    $solrUrl = "http://localhost:8983/solr/library_core/select?q=description:(" . urlencode($query) . ")^2.0&defType=dismax&qf=description&sort=score%20" . $order;

    //calculeaza pagina de start
    $start = ($page - 1) * 3;
    $solrUrl .= "&wt=json&rows=3&start=" . $start;

    //realizeaza cautarea cu Solr si decodifica raspunsul JSON
    $contents = file_get_contents($solrUrl);
    $result = json_decode($contents, true);

    return $result;
}

//fct pt a afisa rezultatul cautarii
function displaySearchResults($result) {
    include('../includes/connect.php');
    //afiseaza rezultatul (cartile sau msq)
    if (isset($result['response']['docs']) && !empty($result['response']['docs'])) {
        echo "<div class='books-container'>";

        //pt fiecare carte din rezultat
        foreach ($result['response']['docs'] as $doc) {
            $book_title = isset($doc['title']) ? $doc['title'][0] : '';
            $book_id = isset($doc['id']) ? $doc['id'][0] : '';

            $booksQuery = "SELECT * FROM book WHERE book_id = '$book_id'";
            $booksResult = mysqli_query($conn, $booksQuery);

            //verif daca query-ul a avut succes si obtine datele cartii
            if ($booksResult && $book = mysqli_fetch_assoc($booksResult)) {
                echo "<div class='book'>";
                echo "<img src='../admin_area/book_covers/{$book['book_cover']}' alt='{$book['book_title']}'>";
                echo "<h3>{$book['book_title']}</h3>";
                echo "<p>Author: {$book['book_author']}</p>";
                echo "<p>Price: $" . number_format($book['book_price'], 2) . "</p>";
                echo "<div id='btn_container'>";
                    echo "<form method='post'>";
                        echo "<input type='hidden' name='book_id' value='{$book['book_id']}'>";

                        echo "<button type='button' name='add_to_cart' class='book_card_btn add_to_cart_btn' " . ($book['quantity_available'] == 0 ? 'disabled' : '') . "
                            onclick='addToCart({$book['book_id']})'>
                            Add to Cart
                        </button>";

                        
                    echo "</form>";
                    echo "<button class='book_card_btn' onclick='viewDetails({$book['book_id']})'>View Details</button>";
                echo "</div>";
            }
            echo "</div>";
        }
        echo "</div>";

        //calculeaza nr total de pagini in fct de nr de carti pe pagina
        $booksPerPage = 3;
        $totalPages = ceil($result['response']['numFound'] / $booksPerPage);

        //paginatia
        echo '<div class="pagination" style="display: flex; flex-wrap: wrap;">';
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a href="?page=' . $i . '&query=' . urlencode($_GET['query']) . '&sort_order=desc">' . $i . '</a>';
        }
        echo '</div>';
    } else {
        echo "<p class='text-center' style='font-size: 25px; color: #850909;'>No results found!</p>";
    }
}

//daca cererea crt e de tip GET si daca a fost introdus ceva in campul de cautare
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
    //seteaza query-ul, ordinea rezultatelor (cresc sau descresc) si pagina
    $query = $_GET['query'];
    $order = isset($_GET['ascending']) ? 'asc' : 'desc';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    //executa cautarea prin apelarea lui solrSearch()
    $solrResult = solrSearch($query, $order, $page);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search in PDFs</title>
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
        .pagination a {
            margin-right: 15px;
            text-decoration: none;
        }

        .pagination {
            text-align: center;
        }

        #btn_container {
            display: flex;
        }

        .add_to_cart_btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 1;
        }

        .add_to_cart_btn:hover:disabled {
            background-color: #ccc;
        }

        .solr_search_input {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            width: 100%;
            font-size: 15px;
        }

        .solr_search_btn {
            padding: 10px;
            background-color: #3039a1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            margin: 10px auto;
        }

        .solr_search_btn:hover {
            background-color: #8cabff;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <!-- div-ul din stanga ocupa 30% din spatiu si are lista cu cat., edituri si all books -->
        <div class="left-div">
            <form action="" method="get" class="d-flex" style="margin: 15px">
                <input type="text" size="30" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Search Keywords" class="solr_search_input" required>
                <input type="submit" value="Search" class="solr_search_btn" name="search">
                <p>Insert the keywords with a space between them. They will be searched in the books PDFs.</p>
                <p>The results will be displayed in a descending order.</p>
            </form>
        </div>

        <!-- div-ul din dreapta ocupa 70% din spatiu si contine cartile -->
        <div class="right-div">
            <h1>Search results</h1>
            <?php
            if (isset($solrResult)) {
                displaySearchResults($solrResult);
            }
            ?>
        </div>
    </div>

    <?php include('../includes/users_footer.php'); ?>
</body>
</html>

<script>
    //fct care trimite la book_details.php
    function viewDetails(bookId) {
        // redirectioneaza catre book_details.php avand ca parametru in URL id-ul cartii pe care am apasat
        window.location.href = `book_details.php?book_id=${bookId}`;
    }

    //fct pt adaugarea in cos
    function addToCart(bookId) {
        <?php
            // Check if the user is logged in and generate the JavaScript code
            //verif daca userul e logatsi genereaza codul JavaScript
            if (isset($_SESSION['user_id'])) {
        ?>
                var xhr = new XMLHttpRequest();
                var formData = new FormData();

                formData.append('book_id', bookId);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            //parcurge raspunsul JSON
                            var response = JSON.parse(xhr.responseText);

                            //afiseaza mesajul alert
                            alert(response.message);

                            //daca s-a adaugat cartea cu succes, se redirectioneaza catre shopping_cart.php
                            if (response.success) {
                                window.location.href = 'shopping_cart.php';
                            }
                        } else {
                            //altfel, se reincarca pagina crt
                            window.location.reload();
                        }
                    }
                };

                xhr.open('POST', 'addToCart.php', true);
                xhr.send(formData);
        <?php
            } else {
                //redirectioneaza userul nelogat catre login
        ?>
                window.location.href = 'login.php';
        <?php
            }
        ?>
    }
</script>
