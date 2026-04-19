<?php
include('../includes/connect.php');
session_start();

if (isset($_SESSION['user_id'])) {
    //obtine id-ul userului logat
    $userId = $_SESSION['user_id'];

    //obtine acel user din baza de date
    $userDataQuery = "SELECT * FROM user WHERE user_id = $userId";
    $userDataResult = mysqli_query($conn, $userDataQuery);

    $userData = mysqli_fetch_assoc($userDataResult);

    //obtine nr de tel si adresa userului pt a le putea afisa in campurile pt editare
    $userAddress = $userData['user_address'];
    $userPhone = $userData['user_phone'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>

        .container {
            display: flex;
            font-size: 16px;
        }

        #leftDiv, #rightDiv {
            flex: 5;
            padding: 30px;
            margin: 20px;
            border-radius: 10px;
        }

        #leftDiv {
            background-color: #e8f4f8;
        }

        #rightDiv {
            background-color: #dae7f4;
        }

        /* campurile de completat */
        #address, #phone, #old_password,
        #new_password, #confirm_password {
            margin-bottom: 10px;
            padding: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        /* butoanele */
        #change_password_btn, #update_address_phone_btn {
            padding: 10px;
            background-color: #3039a1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
        }

        #update_address_phone_btn:hover, #change_password_btn:hover {
            background-color: #8cabff;
        }
    </style>
</head>
<body>
    <?php
        include("navbar.php");
    ?>

    <div class="container">
        <!-- divul pt editarea nt tel si adresei -->
        <div id="leftDiv">
            <h2>Edit Address and Phone</h2>
            <form action="" method="post">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userAddress); ?>" required>

                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>" required>

                <button type="button" onclick="updateProfile()" id="update_address_phone_btn">Update Address and Phone</button>

            </form>
        </div>

        <!-- divul pt schimbarea parolei -->
        <div id="rightDiv">
            <h2>Change Password</h2>
            <form action="" method="post">
                <label for="old_password">Old Password:</label>
                <input type="password" id="old_password" name="old_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="button" onclick="changePassword()" id="change_password_btn">Change Password</button>
                <!-- <button type="submit" name="change_password" id="change_password_btn">Change Password</button> -->
            </form>
        </div>
    </div>

    <?php
        include("../includes/footer.php");
    ?>
</body>

<script>
        // fct pt schimbarea adresei si a nr de tel
        function updateProfile() {
            // creeaza un ob care are ca date elementele luate din formularul pt schimbarea adresei si a nr de tel
            var updateData = {
                update_profile: true,
                user_phone: document.getElementById('phone').value,
                user_address: document.getElementById('address').value
            };

            // foloseste Fetch API pt a face o cerere HTTP POST 
            fetch('../authentication_api/api_edit_profile.php', {
                method: 'POST', //specifica metoda HTTP ca fiind POST
                headers: {
                    'Content-Type': 'application/json', //specifica tipul continutului ca JSON
                },
                body: JSON.stringify(updateData), //trimite formData ca JSON
            })
            //proceseaza raspunsul si afiseaza mesajul corespunzator primit de la server
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        // fct pt schimbarea parolei
        function changePassword() {
            var oldPassword = document.getElementById('old_password').value;
            var newPassword = document.getElementById('new_password').value;
            var confirm_password = document.getElementById('confirm_password').value;

            // creeaza un ob care are ca date elementele luate din formularul pt schimbarea parolei
            var changePasswordData = {
                change_password: true,
                old_password: oldPassword,
                new_password: newPassword,
                confirm_password: confirm_password,
            };

            // foloseste Fetch API pt a face o cerere HTTP POST 
            fetch('../authentication_api/api_edit_profile.php', {
                method: 'POST', //specifica metoda HTTP ca fiind POST
                headers: {
                    'Content-Type': 'application/json', //specifica tipul continutului ca JSON
                },
                body: JSON.stringify(changePasswordData), //trimite formData ca JSON
            })
            //proceseaza raspunsul si afiseaza mesajul corespunzator primit de la server
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</html>