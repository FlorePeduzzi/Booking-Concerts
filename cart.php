<?php
include "header.php";

if (!isset($_SESSION['user'])) {
    header("location:connexion.php");
    exit;
}

?>

<div class="container">
    <h1 class="mt-5">Votre Panier</h1>

    <?php
    // Vérifier si le panier est vide
    if (empty($_SESSION['cart'])) {
        echo "<p>Votre panier est vide.</p>";
    } else {
        $pdo = new PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root', '');
        $iddates = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($iddates), '?'));

        $query = "SELECT event.idevent, event.eventName, event.category, date.date, date.iddate, artist.name, event.price
                  FROM event
                  JOIN date ON event.idevent = date.idevent
                  JOIN event_has_artist ON event.idevent = event_has_artist.idevent
                  JOIN artist ON event_has_artist.idartist = artist.idartist
                  WHERE date.iddate IN ($placeholders)";
        $statement = $pdo->prepare($query);
        $statement->execute($iddates);
        $events = $statement->fetchAll(PDO::FETCH_ASSOC);
        $eventsByIddate = [];
        foreach ($events as $event) {
            $eventsByIddate[$event['iddate']] = $event;
        }

        $totalPrice = 0;
    ?>

        <div class="table-responsive mt-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Date</th>
                        <th>Nom du concert</th>
                        <th>Prix (€)</th>
                        <th>Date</th>
                        <th>Quantité</th>
                        <th>Total (€)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $iddate => $quantity) {
                        if (isset($eventsByIddate[$iddate])) {
                            $event = $eventsByIddate[$iddate];
                            $total = $event['price'] * $quantity; ?>
                            <tr>
                                <td><?= htmlspecialchars($iddate) ?></td>
                                <td><?= htmlspecialchars($event['eventName']) ?></td>
                                <td><?= htmlspecialchars($event['price']) ?></td>
                                <td><?= htmlspecialchars($event['date']) ?></td>
                                <td>
                                    <a href="decrease_cart.php?id=<?= htmlspecialchars($iddate) ?>" class="btn btn-sm btn-outline-secondary">-</a>
                                    <?= htmlspecialchars($quantity) ?>
                                    <a href="increase_cart.php?id=<?= htmlspecialchars($iddate) ?>" class="btn btn-sm btn-outline-secondary">+</a>
                                </td>
                                <td><?= htmlspecialchars($total) ?></td>
                                <td><a href="removecart.php?id=<?= htmlspecialchars($iddate) ?>" class="btn btn-sm btn-outline-secondary">Supprimer</a></td>
                            </tr>
                    <?php $totalPrice += $total;
                        }
                    } ?>
                </tbody>
            </table>
        </div>
        <p class="mt-3">Total : <?= htmlspecialchars($totalPrice) ?> €</p>

        <button class="btn btn-outline-secondary mr-2" onclick="window.location.href='emptycart.php'">Vider le panier</button>
        <a class="btn btn-outline-secondary" href="invoice.php?id=<?= $iddate ?>">Valider votre commande</a>


    <?php } ?>
</div>

<?php
include "footer.php";
?>