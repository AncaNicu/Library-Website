<?php
    include('../includes/connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../includes/navbar_stylesheet.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-pzN2EeQpHDtz5tAdgG6sm5lc5s5K5Ghl9q5MUegASnF1ARPlJLl3me5aZzPG3e3CCml49u1PP4F5ckAChS+g6Q==" crossorigin="anonymous" />

    <style>
        .cart_icon {
            color: white;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="navbar">
        <div class="dropdown">
            <div class="logo">
                <a href="home.php">
                    <img src="../images/logo5.jpg" alt="" class="logo">
                </a>
            </div>
        </div>

        <div class="dropdown">
            <a href="home.php">
                <button class="dropbtn">Home</button>
            </a>
        </div>


        <div class="dropdown">
            <a href="books.php">
                <button class="dropbtn">Books</button>
            </a>
        </div>

        <div class="dropdown">
            <a href="contact.php">
                <button class="dropbtn">Contact</button>
            </a>
        </div>

        <div class="dropdown">
            <button class="dropbtn">PDF Actions</button>
            <div class="dropdown-content">
                <a href="search_in_PDFs.php">Search in PDFs</a>
                <a href="downloadPDFs.php">Downlaod PDFs</a>
            </div>
        </div>

         <!-- partea de search -->
        <div class="search-container">
            <form action="books.php" method="GET" class="search_form">
                <input type="text" id="search_data" placeholder="Search..." autocomplete="off" name="search_data">
                <button type="submit" id="searchButton" name="search_data_product">Search</button>
            </form>
        </div>


    </div>

    <!-- navbar 2 -->
    <div class="navbar2">
        <!-- daca user-ul e logat, navbar 2 are Logout si My Profile -->
        <?php
            if(!isset($_SESSION['username']))
            {
                echo "
                <div class='dropdown'>
                    <a href='login.php'>
                        <button class='dropbtn2'>Login</button>
                    </a>
                </div>
        
                <div class='dropdown'>
                    <a href='register.php'>
                        <button class='dropbtn2'>Register</button>
                    </a>
                </div>";
            }
            else
            {
                echo "
                <div class='dropdown'>
                    <a href='my_profile.php'>
                        <button class='dropbtn2'>Edit Profile</button>
                    </a>
                </div>

                <div class='dropdown'>
                    <a href='my_orders.php'>
                        <button class='dropbtn2'>My orders</button>
                    </a>
                </div>
        
                <div class='dropdown'>
                    <a href='../account_area/logout.php'>
                        <button class='dropbtn2'>Logout</button>
                    </a>
                </div>
                
                <div class='dropdown'>
                    <a href='shopping_cart.php'><i class='fas fa-shopping-cart cart_icon'></i></a>
                </div>";
            }
        ?>        
        <div class="welcome-msg-container">
            <?php
                //daca userul e logat, se af "Welcome" + username
                //altfel, doar "Welcome, reader!"
                if (isset($_SESSION['user_id'])) {
                    echo "<p>Welcome, {$_SESSION['username']}!</p>";
                } else {
                    echo "<p>Welcome, reader!</p>";
                }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- script-ul pt autocomplete la search field -->
<script>
    $(document).ready(function(){
        $('#search_data').autocomplete({
            source: function(request, response)
            {
                $.ajax({
                    url:"fetch.php",
                    method:"POST",
                    data:{query:request.term},
                    dataType:"json",
                    success:function(data)
                    {
                        response(data);
                    }
                })
            },
            minLength: 1, //trebuie sa se introduca minim 1 caracter ca sa se porneasca autocomple-ul
            select: function(event, ui) {
                console.log(ui.item.value);
            }
        });
    });
</script>
</body>

</html>


