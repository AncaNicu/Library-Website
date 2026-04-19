<?php
include('../includes/connect.php');
include('users_functions.php');
session_start();

//obtine id-ul cartii din URL
$bookId = isset($_GET['book_id']) ? $_GET['book_id'] : '';

$booksQuery = "SELECT * FROM book WHERE book_id = '$bookId'";

$booksResult = mysqli_query($conn, $booksQuery);

if (mysqli_num_rows($booksResult) > 0) {
    $bookDetails = mysqli_fetch_assoc($booksResult);
}

//verifica daca butonul add to cart a fost apasat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    //daca este un user logat
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        //verifica daca userul are un cos activ
        $activeCartQuery = "SELECT * FROM cart WHERE user_id = $userId AND order_status = 'in process'";
        $activeCartResult = mysqli_query($conn, $activeCartQuery);

        if (mysqli_num_rows($activeCartResult) == 0) {
            //nu are cos activ => creeaza unul
            $createCartQuery = "INSERT INTO cart (user_id, order_status) VALUES ($userId, 'in process')";
            mysqli_query($conn, $createCartQuery);
        }

        //obtine cart_id pt cosul anterior reat sau deja existent
        $activeCartResult = mysqli_query($conn, $activeCartQuery);
        $cartId = mysqli_fetch_assoc($activeCartResult)['cart_id'];

        //verif daca cartea exista deja in cos
        $checkBookQuery = "SELECT * FROM cart_item WHERE cart_id = $cartId AND book_id = $bookId";
        $checkBookResult = mysqli_query($conn, $checkBookQuery);

        //nu exista => o adauga
        if (mysqli_num_rows($checkBookResult) == 0) {
            $addToCartQuery = "INSERT INTO cart_item (cart_id, book_id, item_quantity) VALUES ($cartId, $bookId, 1)";
            mysqli_query($conn, $addToCartQuery);
            $_SESSION['last_added_book'] = $bookId;

            //seteaza un mesaj de succes pt JavaScript
            $_SESSION['add_to_cart_message'] = 'success';
        } else {
            //seteaza un mesaj ca acea carte e deja in cos pt JavaScript
            $_SESSION['add_to_cart_message'] = 'already_in_cart';
        }
    } else {
        //daca un user nelogat incearca sa adauge o carte in cos, se redirectioneaza la login.php
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
    <title>Book Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .container {
            display: flex;
            padding: 50px;
        }

        .left-div, .middle-div, .right-div {
            padding: 10px;
            box-sizing: border-box;
            margin-right: 10px;
        }

        .left-div {
            width: 25%;
        }

        .middle-div {
            width: 55%;
            padding: 0px 30px;
            border-radius: 10px;
            background-color: #e8f4f8;
        }

        .right-div {
            width: 20%;
            border-radius: 10px;
            background-color: #dae7f4;
            height: 30%;
        }

        .book-cover {
            width: 80%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .author-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .author-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 10px;
        }

        #add_to_cart_btn, 
        .flip_through_btn {
            padding: 10px;
            background-color: #3039a1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
        }

        #add_to_cart_btn:hover,
        .flip_through_btn:hover {
            background-color: #8cabff;
        }

        #available {
            color: green;
        }

        #unavailable {
            color: red;
        }

        #add_to_cart_btn:disabled {
            background-color: #ccc;
            cursor: not-allowed; 
            opacity: 1; 
        }

        #add_to_cart_btn:hover:disabled {
            background-color: #ccc; 
        }

        #youtube-videos {
            overflow-y: auto;
            max-height: 130px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
        }

        #youtube-videos a {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #youtube-videos img {
            width: 120px;
            height: auto;
            border-radius: 5px;
        }

        #youtube-videos p {
            margin-top: 5px;
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

    </style>
</head>
<body>
    <?php
        include('navbar.php');
    ?>

    <div class="container">
        <!-- divul din stanga = coperta + flip through -->
        <div class="left-div">
            <img src="../admin_area/book_covers/<?php echo $bookDetails['book_cover']; ?>" alt="Book Cover" class="book-cover">
            <button class="flip_through_btn" onclick="openPDF('<?php echo $bookDetails['book_pdf']; ?>')">Flip Through</button>
        </div>

        <!-- divul din mijloc = detalii carte -->
        <div class="middle-div">
            <h2 class="book-title"><?php echo $bookDetails['book_title']; ?></h2>
            <div class="author-info">
                <img src="../admin_area/authors/<?php echo $bookDetails['author_image']; ?>" alt="Author Image" class="author-image">
                <p>By <?php echo $bookDetails['book_author']; ?></p>
            </div>
            <p><?php echo $bookDetails['book_description']; ?></p>

            <div id="book-rating">
            </div>

            <h3>Related YouTube Videos:</h3>
            <div id="youtube-videos">
                
            </div>
        </div>

        <!-- divul din dreapta = detalii carte + Add to Cart -->
        <div class="right-div">
            <p><strong>Price:</strong> $<?php echo $bookDetails['book_price']; ?></p>
            <p><strong>Category:</strong> <?php echo getCategoryNameById($bookDetails['category_id'], $conn); ?></p>
            <p><strong>Publisher:</strong> <?php echo getPublisherNameById($bookDetails['publisher_id'], $conn); ?></p>
            <?php
                //daca nr exemplare = 0 => unavailable
                if($bookDetails['quantity_available'] == 0) {
                    echo "<p id='unavailable'>Unavailable</p>";
                }
                else {
                    echo "<p id='available'>Avilable</p>";
                }
                echo "<form method='post'>";
                    echo "<input type='hidden' name='book_id' value='{$bookId}'>";
                    echo "<button type='submit' name='add_to_cart' id='add_to_cart_btn'>Add to Cart</button>";
                echo "</form>";
            ?>
            
        </div>
    </div>
    <?php
        include('../includes/users_footer.php');
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // fct pt a obtine primele 5 videouri youtube pe baza titlului si autorului cartii crt
            function fetchYouTubeVideos(bookTitle, bookAuthor) {
                const apiKey = 'xxx';
                const query = `${bookTitle} ${bookAuthor}`;
                const apiUrl = `https://www.googleapis.com/youtube/v3/search?key=${apiKey}&q=${query}&part=snippet,id&order=relevance&maxResults=5`;

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.items && data.items.length > 0) {
                            // afiseaza videoclipurile youtube
                            const videosContainer = document.getElementById('youtube-videos');

                            data.items.forEach(video => {
                                const videoUrl = `https://www.youtube.com/watch?v=${video.id.videoId}`;
                                const videoTitle = video.snippet.title;
                                const videoThumbnail = video.snippet.thumbnails.medium.url;

                                const videoElement = document.createElement('div');
                                videoElement.innerHTML = `
                                    <a href="${videoUrl}" target="_blank">
                                        <img src="${videoThumbnail}" alt="${videoTitle}">
                                        <p>${videoTitle}</p>
                                    </a>`;
                                videosContainer.appendChild(videoElement);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching YouTube videos:', error);
                    });
            }

            //apeleaza fct pe baza titlului si a autorului
            const bookTitle = "<?php echo $bookDetails['book_title']; ?>";
            const bookAuthor = "<?php echo $bookDetails['book_author']; ?>";
            fetchYouTubeVideos(bookTitle, bookAuthor);
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            //fct pt a obtine detaliile cartii folosing Books API (inclusiv rating-ul)
            function fetchBookDetailsFromAPI(title, author, apiKey) {
                const query = `${title} ${author}`;
                const apiUrl = `https://www.googleapis.com/books/v1/volumes?q=${query}&key=${apiKey}`;

                return fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.items && data.items.length > 0) {
                            return data.items[0].volumeInfo;
                        } else {
                            return null;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching book information:', error);
                        return null;
                    });
            }

            //fct pt a afisa rating-ul
            function displayBookDetailsWithRating() {
                //titlul, autorul si cheia API-ului
                const bookTitle = "<?php echo $bookDetails['book_title']; ?>";
                const bookAuthor = "<?php echo $bookDetails['book_author']; ?>";
                const googleBooksApiKey = 'xxx';

                //apeleaza fct pt a afisa detaliile cartii
                fetchBookDetailsFromAPI(bookTitle, bookAuthor, googleBooksApiKey)
                    .then(bookInfo => {
                        if (bookInfo) {
                            const rating = bookInfo.averageRating || 'Not rated';
                            const ratingContainer = document.getElementById('book-rating');
                            ratingContainer.innerHTML = `<strong>Rating:</strong> ${rating} &#9733;`;
                        } else {
                            console.log('Book not found.');
                        }
                    });
            }

            displayBookDetailsWithRating();
        });


    </script> 


    <script>

        //fct pt a deschide PDF-ul cartii crt
        function openPDF(pdfPath) {
            //locatia pdf-ului
            var pdfUrl = '../admin_area/book_pdfs/' + pdfPath;

            //deschide acel pdf intr-o noua fereasctra
            window.open(pdfUrl, '_blank');
        }

        //butonul add to cart
        var addToCartBtn = document.getElementById("add_to_cart_btn");
        //cantitatea = 0 => Add to Cart e inactiv
        if (<?php echo $bookDetails['quantity_available']; ?> === 0) {
            addToCartBtn.disabled = true;
        } else {
            addToCartBtn.disabled = false;
        }

        //verifica mesajul pt JavaScript pt a vedea ce fel de mesaj sa afiseze
        var addToCartMessage = "<?php echo isset($_SESSION['add_to_cart_message']) ? $_SESSION['add_to_cart_message'] : ''; ?>";

        if (addToCartMessage === "success") {
            alert('Book successfully added to the cart!');
            window.location.href = 'shopping_cart.php';
        } else if (addToCartMessage === "already_in_cart") {
            alert('Book already in the cart!');
            window.location.href = `book_details.php?book_id=<?php echo $bookId; ?>`;
        }

        //face clear la variabila pt mesajul add to cart 
        <?php unset($_SESSION['add_to_cart_message']); ?>
    </script>
</body>
</html>


