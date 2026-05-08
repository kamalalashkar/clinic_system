<?php


function checkSession() {
   
    if (!isset($_SESSION['user'])) {
        header("Location: login.php"); 
        exit();
    }
}

function checkRole($role) {
   
    if ($_SESSION['user']['role'] !== $role) {
     
        header("Location: unauthorized.php");
        exit();
    }
}


function checkPatient() {
    checkRole('patient');
}

function checkDoctor() {
    checkRole('doctor');
}

function checkAdmin() {
    checkRole('admin');
}


function checkLoggedIn() {
    if (!isset($_SESSION['user'])) {
        return false; 
    }
    return true; 
}
?>
