<?php
session_start();

if (isset($_SESSION['isloggedin'])) {
    if ($_SESSION['isloggedin'] === '') 
        header('Location: warning.html');
    else 
        header('Location: ./view/home.php');
}else
    header('Location: warning.html');