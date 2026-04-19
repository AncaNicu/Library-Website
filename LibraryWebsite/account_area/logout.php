<?php
    session_start();
    session_unset();//elibereaza toate variabilele sesiunii
    session_destroy();//distruge complet sesiunea 
    echo "<script>window.open('../users_area/home.php', '_self')</script>";
?>