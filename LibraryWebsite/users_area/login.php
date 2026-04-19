<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../includes/navbar_stylesheet.css">
    <link rel="stylesheet" href="../includes/account_stylesheet.css">
</head>
<body>

    <?php
        include("navbar.php");
    ?>
    <!-- formularul de Login -->
    <form id="loginForm" class="login_form">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required><br>

        <label for="user_password">Password:</label>
        <input type="password" id="user_password" name="user_password" class="form-control" placeholder="Enter password" required><br>

        <button type="button" class="account_btn" onclick="loginUser()">Login</button>
        <p>Don't have an account? Go to <a href="register.php">Register Page</a></p>
    </form>
    <?php
        include("../includes/users_footer.php");
    ?>

    <script>
        //fct pt logarea unui utilizator
        function loginUser() {
            // creeaza un ob care are ca date elementele luate din formularul pt logare
            var formData = {
                username: document.getElementById('username').value,
                user_password: document.getElementById('user_password').value
            };

            // foloseste Fetch API pt a face o cerere HTTP POST
            fetch('../authentication_api/api_login.php', {
                method: 'POST', //specifica metoda HTTP ca fiind POST
                headers: {
                    'Content-Type': 'application/json', //specifica tipul continutului ca JSON
                },
                body: JSON.stringify(formData),
            })
            //proceseaza raspunsul si afiseaza mesajul corespunzator primit de la server
            .then(response => response.json())
            .then(data => {
                alert(data.message);

                if (data.user_id) {
                    //redirectioneaza user ul la pag primita ca raspuns de la endpoint
                    window.location.href = data.redirect_url;
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
