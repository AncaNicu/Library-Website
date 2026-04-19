<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/navbar_stylesheet.css">
    <title>Home</title>
    <style>
        /* pt. caruselul de imagini */
        #slider{
        overflow: hidden;
        }

        #slider figure{
        position: relative;
        width:500%;
        margin: 0;
        left: 0;
        animation: 20s slider infinite;
        }

        #slider figure img{
        width: 20%;
        height: 300px;
        object-fit: contain;
        float: left;
        }

        @keyframes slider{
        0%{
            left: 0;
        }
        20%{
            left: 0;
        }
        25%{
            left: -100%;
        }
        45%{
            left: -100%;
        }
        50%{
            left: -200%;
        }
        70%{
            left: -200%;
        }
        75%{
            left: -300%;
        }
        95%{
            left: -300%;
        }
        100%{
            left: -400%;
        }
        }

        .slider-container {
            margin: 10px 10px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #3039a1;
            color: white;
        }

        .left {
            width: 60%;
            padding-right: 20px; /* Space between the text and the left side of the left div */
            box-sizing: border-box; /* Include padding in the width */
        }

        .right {
            width: 40%;
            display: flex;
            justify-content: space-between;
        }

        .right img {
            width: 45%; /* Two images with 2% space between them */
            object-fit: cover;
        }

    </style>
</head>
<body>
    <!-- navbar-ul -->
    <?php
        include("navbar.php");
    ?>

    <!-- caruselul de imagini -->
    <div class="slider-container">
          <div id="slider">
            <figure>
              <img src="../images/img2.jpg" alt="">
              <img src="../images/img3.jpg" alt="">
              <img src="../images/img4.jpg" alt="">
              <img src="../images/img5.jpg" alt="">
              <img src="../images/img2.jpg" alt="">
            </figure>
          </div>
    </div>

    <!-- partea de About Us -->
    <div class="container">
        <div class="left">
            <h3>Welcome to Royal Violet Books!</h3>
            <p>At Royal Violet Books, we believe in the transformative power of literature. Our bookshop is more than just a place to buy books; it's a haven for avid readers, a sanctuary for book lovers, and a community of literary enthusiasts.</p>
            <p>Our shelves are curated with care, offering a diverse collection that spans genres and cultures. Whether you're a seasoned bibliophile or a casual reader, our selection is thoughtfully chosen to ignite your imagination and kindle the joy of reading.</p>
            <p>Discover the joy of reading at Royal Violet Books, where stories come to life, and readers become friends.</p>
        </div>
        <div class="right">
            <img src="../images/logo8.jpg" alt="Image 1">
            <img src="../images/logo6.jpg" alt="Image 2">
        </div>
    </div>

    <!-- footer-ul -->
    <?php
        include("../includes/users_footer.php");
    ?>

</body>

</html>