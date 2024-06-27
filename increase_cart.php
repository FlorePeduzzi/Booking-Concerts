<?php
include "header.php";

// recuperer la clé du panier (id event)
$idevent=$_GET['id'];
// Recuperer le panier
$cart=$_SESSION['cart'];
// ajouter un a la valeur de la clé du panier

$cart[$idevent]=$cart[$idevent]+1;
// sauvegarde dans la session
$_SESSION['cart']=$cart;

header('location:cart.php');

include "footer.php";
?>

