<?php
include "header.php";

$iduser = isset($_SESSION['iduser']) ? $_SESSION['iduser'] : null;
$pdo = new PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root');

if (!isset($_SESSION['user'])) {
    header("location:connexion.php");
    exit;
}

?>

<div class="container">
    <h1 class="mt-5">Votre Facture</h1>

    <?php

    $pdo = new PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root', '');

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $totalPrice = 0;

        foreach ($_SESSION['cart'] as $iddate => $quantity) {
            $statement = $pdo->prepare("SELECT event.idevent, event.eventName, event.category, date.date, artist.name, event.price
                                    FROM event
                                    JOIN date ON event.idevent = date.idevent
                                    JOIN event_has_artist ON event.idevent = event_has_artist.idevent
                                    JOIN artist ON event_has_artist.idartist = artist.idartist
                                    WHERE date.iddate = ?");
            $statement->execute([$iddate]);
            $event = $statement->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                $total = $event['price'] * $quantity;
                $totalPrice += $total;
                ?>

                <div class="invoice mt-5">
                    <h2>Facture pour le concert de <?= htmlspecialchars($event['eventName']) ?></h2>
                    <p>Catégorie : <?= htmlspecialchars($event['category']) ?></p>
                    <p>Date : <?= htmlspecialchars($event['date']) ?></p>
                    <p>Artiste(s) : <?= htmlspecialchars($event['name']) ?></p>
                    <p>Prix unitaire : <?= htmlspecialchars($event['price']) ?> €</p>
                    <p>Quantité : <?= htmlspecialchars($quantity) ?></p>
                    <p>Total : <?= htmlspecialchars($total) ?> €</p>
                </div>

                <?php
                // Insérer dans la table booking
                $statementBooking = $pdo->prepare("INSERT INTO booking (iduser, idevent, quantity) VALUES (?, ?, ?)");
                $statementBooking->execute([$iduser, $event['idevent'], $quantity]);
            }
        }

        // Ajoutez le code pour vider le panier
        unset($_SESSION['cart']);
    } else {
        echo "<p>Votre panier est vide.</p>";
    }
    ?>
    
    <p class="mt-3">Total : <?= htmlspecialchars($totalPrice) ?> €</p>
    <button class="btn btn-outline-secondary mt-3"><a href="donkeyEvent.php">Retour à la liste des événements</a></button>
</div>

<?php
include "footer.php";
?>
