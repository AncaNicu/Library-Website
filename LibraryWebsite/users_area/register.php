<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="../includes/navbar_stylesheet.css">
    <link rel="stylesheet" href="../includes/account_stylesheet.css">
</head>
<body>

    <?php
        include("navbar.php");
    ?>
    <!-- formularul de register -->
    <form id="registrationForm" class="login_form">
        <h2>User Registration</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required><br>

        <label for="user_email">Email:</label>
        <input type="email" id="user_email" name="user_email" class="form-control" placeholder="Enter email" required><br>

        <label for="user_password">Password:</label>
        <input type="password" id="user_password" name="user_password" class="form-control" placeholder="Enter password" required><br>

        <label for="user_phone">Phone:</label>
        <input type="tel" id="user_phone" name="user_phone" class="form-control" placeholder="Enter phone number" required><br>

        <label for="user_address">Address:</label>
        <input type="text" id="user_address" name="user_address" class="form-control" placeholder="Enter address" required><br>

        <button type="button" class="account_btn" onclick="registerUser()">Register</button>
        <p>Already have an account? Go to <a href="login.php">Login Page</a></p>
    </form>

    <?php
        include("../includes/users_footer.php");
    ?>
<script>

    //fct pt crearea unui nou cont de user
    function registerUser() {
        // creeaza un ob care are ca date elementele luate din formularul pt crearea unui cont de user
        var formData = {
            username: document.getElementById('username').value,
            user_email: document.getElementById('user_email').value,
            user_password: document.getElementById('user_password').value,
            user_phone: document.getElementById('user_phone').value,
            user_address: document.getElementById('user_address').value
        };

        // foloseste Fetch API pt a face o cerere HTTP POST
        fetch('../authentication_api/api_registration.php', {
            method: 'POST', //specifica metoda HTTP ca fiind POST
            headers: {
                'Content-Type': 'application/json', //specifica tipul continutului ca JSON
            },
            body: JSON.stringify(formData), //trimite formData ca JSON
        })
        //proceseaza raspunsul si afiseaza mesajul corespunzator primit de la server
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            
            //daca userul a creat contul => redirectioneaza catre logon.php
            if (data.message === "User registered successfully") {
                window.location.href = 'login.php';
            } else {
                //altfel => incarca din nou register.php
                location.reload();
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
</script>


</body>
</html>
