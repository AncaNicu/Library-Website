<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
    //lista de PDF uri selectate
    $selectedFiles = isset($_POST['theme-files']) ? $_POST['theme-files'] : array();

    //creeaza o arhiva zip
    $zip = new ZipArchive();
    $zipFileName = 'compressed_files.zip';

    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
        foreach ($selectedFiles as $selectedFile) {
            //valideaza si adauga fiecare fisier PDF la arhiva zip
            $filePath = "../admin_area/book_pdfs/" . basename($selectedFile);

            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($selectedFile));
            }
        }

        //inchide arhiva zip
        $zip->close();

        //seteaza antetele potrivite pt descarcarea arhivei
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipFileName);
        header('Content-Length: ' . filesize($zipFileName));

        readfile($zipFileName);

        unlink($zipFileName);
    } else {
        echo 'Failed to create ZIP archive';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download PDFs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .content {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form_style {
            text-align: center;
        }

        .form-field {
            margin-bottom: 15px;
        }

        label {
            display: inline-block;
            margin-left: 5px;
        }

        .btn {
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

    <?php
        include('navbar.php');
    ?>

    <div class="content py-4">

        <div class="form_style w-50 m-auto py-5">
            <h3 class="text-center">Download selected PDF preview of our books</h3>
            <form action="" method="post" enctype="multipart/form-data" class="m-auto w-50">

                <?php
                $files = glob("../admin_area/book_pdfs/*.pdf");
                foreach ($files as $theme_fles) {
                    ?>
                    <div class="form-field">
                        <input type="checkbox" name="theme-files[]" value="<?php echo $theme_fles ?>">
                        <label><?php echo basename($theme_fles); ?></label>
                    </div>
                    <?php
                }
                ?>

                <button type="submit" name="download" class="btn" onclick="showAlertAfterDownload()">Download</button>
                <p class="error"><?php echo @$error; ?></p>
            </form>
        </div>

    </div>
    <?php
        include('../includes/users_footer.php');
    ?>

    <script>
        function showAlertAfterDownload() {
            alert('Download completed!');
            setTimeout(function () {
                window.location.reload();
            }, 100);
        }
    </script>

</body>
</html>

