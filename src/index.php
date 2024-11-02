<?php
session_start();

if (isset($_SESSION['isloggedin'])) {
    if ($_SESSION['isloggedin'] === '') 
        header('Location: ../public/warning.html');
    else 
        header('Location: ../public/view/home.php');
}else
    header('Location: ../public/warning.html');