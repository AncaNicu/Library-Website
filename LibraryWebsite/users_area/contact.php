<?php
include('../includes/connect.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>

        #contact_form_div {
            font-size: 16px;
            width: 50%;
            margin: 30px auto;
            padding: 20px;
            background-color: #dae7f4;
            border-radius: 10px;
        }

        /* campurile de completat */
        #subject, #message {
            margin-bottom: 10px;
            padding: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        /* butonul */
        #contact_us_btn {
            padding: 10px;
            background-color: #3039a1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 30%;
            box-sizing: border-box;
            font-size: 16px;
        }

        #contact_us_btn:hover {
            background-color: #8cabff;
        }
    </style>
</head>
<body>
    <?php
        include("navbar.php");
    ?>

    <div id="contact_form_div">
        <h2>Contact Us</h2>

        <form action="https://formsubmit.co/ancanicu2001@gmail.com" method="post" enctype="multipart/form-data">

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea name="message" cols="30" rows="5" id="message" name="message" required></textarea>

            <button type="submit" name="contact_us_btn" id="contact_us_btn">Send</button>
        </form>
    </div>

    <?php
        include("../includes/footer.php");
    ?>
</body>
</html>
