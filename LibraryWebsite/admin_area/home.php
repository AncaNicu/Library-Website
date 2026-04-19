<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/navbar_stylesheet.css">
</head>
<body>
    <!-- navbarul -->
    <div class="navbar">
        <div class="dropdown">
            <div class="logo">
                <a href="home.php">
                    <img src="../images/logo5.jpg" alt="" class="logo">
                </a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Insert Options</button>
            <div class="dropdown-content">
                <a href="home.php?insert_option=insert_book">Insert Book</a>
                <a href="home.php?insert_option=insert_category">Insert Category</a>
                <a href="home.php?insert_option=insert_publisher">Insert Publisher</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Update Options</button>
            <div class="dropdown-content">
                <a href="home.php?update_option=update_book">Update Book</a>
                <a href="home.php?update_option=update_category">Update Category</a>
                <a href="home.php?update_option=update_publisher">Update Publisher</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Delete Options</button>
            <div class="dropdown-content">
                <a href="home.php?delete_option=delete_book">Delete Book</a>
                <a href="home.php?delete_option=delete_category">Delete Category</a>
                <a href="home.php?delete_option=delete_publisher">Delete Publisher</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="../account_area/logout.php">
                <button class="dropbtn">Logout</button>
            </a>
        </div>
    </div>

    <div class="container my-4">
        <?php

            //in functie de butonul apasat, se va include o anumita pagina
            $chosedOption = false;

            if (isset($_GET['insert_option'])) {
                $insertOption = $_GET['insert_option'];
                switch ($insertOption) {
                    case 'insert_book':
                        include('insert_book.php');
                        break;
                    case 'insert_category':
                        include('insert_category.php');
                        break;
                    case 'insert_publisher':
                        include('insert_publisher.php');
                        break;
                }
                $chosedOption = true;
            }

            if (isset($_GET['update_option'])) {
                $updateOption = $_GET['update_option'];
                switch ($updateOption) {
                    case 'update_book':
                        include('update_book.php');
                        break;
                    case 'update_category':
                        include('update_category.php');
                        break;
                    case 'update_publisher':
                        include('update_publisher.php');
                        break;
                }
                $chosedOption = true;
            }

            if (isset($_GET['delete_option'])) {
                $deleteOption = $_GET['delete_option'];
                switch ($deleteOption) {
                    case 'delete_book':
                        include('delete_book.php');
                        break;
                    case 'delete_category':
                        include('delete_category.php');
                        break;
                    case 'delete_publisher':
                        include('delete_publisher.php');
                        break;
                }
                $chosedOption = true;
            }

            if ($chosedOption == false) {
                include('home_content.php');
            }
        ?>
    </div>

    <?php
        include("../includes/footer.php");
    ?>

</body>

</html>
