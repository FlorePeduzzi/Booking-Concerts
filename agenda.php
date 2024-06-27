<?php
include "header.php";

$pdo = new \PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statement = $pdo->prepare("SELECT event.*, GROUP_CONCAT(date.date) AS dates, GROUP_CONCAT(date.time) AS times
                            FROM event
                            LEFT JOIN date ON event.idevent = date.idevent
                            GROUP BY event.idevent
                            ORDER BY MIN(date.date)");

$statement->execute();
$events = $statement->fetchAll(PDO::FETCH_ASSOC);
?>


<h1 class="text-center mt-5">Tous nos concerts</h1>

<?php if (isset($_SESSION["admin"]) && $_SESSION["admin"]) { ?>
    <a href="addEvent.php" class="btn btn-outline-secondary" style="margin-left:30px;">Ajouter un nouvel évent</a>
<?php } ?>

<div class="container">
    <div class="row justify-content-center">
        <?php foreach ($events as $oneEvent) { ?>
            <div class="col-md-3 mb-3">
                <div class="card" style="height: 100%;">
                    <img src="<?= $oneEvent['picture'] ?>" class="card-img-top" style="object-fit: contain; height: 200px; margin-top:15px;">
                    <div class="card-body text-center">
                        <h3 class="card-title"><a href="detailEvent.php?id=<?= $oneEvent['idevent'] ?>"><?= $oneEvent['eventName'] ?></a></h3>
                        <div class="card-text">
                            <h4><?= $oneEvent['category'] ?></h4>
                        </div>
                        <div class="cardtext text-center">
                            <?php
                            $dates = explode(',', $oneEvent['dates']);
                            $times = explode(',', $oneEvent['times']);
                            echo '<h5>';
                            foreach ($dates as $index => $date) {
                                $dayOfWeek = date('N', strtotime($date . ' ' . $times[$index]));
                                $daysInFrench = array(
                                    1 => 'Lundi',
                                    2 => 'Mardi',
                                    3 => 'Mercredi',
                                    4 => 'Jeudi',
                                    5 => 'Vendredi',
                                    6 => 'Samedi',
                                    7 => 'Dimanche'
                                );
                                echo $daysInFrench[$dayOfWeek] . '<br>' . date('d-m-Y H:i', strtotime($date . ' ' . $times[$index])) . '<br>';
                            }
                            echo '</h5>';
                            ?>
                        </div>
                        <div>
                            <h6><?= $oneEvent['price'] ?> €</h6>
                        </div><br>
                        <div>
                            <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"]) { ?>
                                <a href="deleteEvent.php?id=<?= $oneEvent['idevent'] ?>" class="btn btn-outline-secondary" style="margin-bottom: 10px;">Supprimer</a>
                                <a href="modifyEvent.php?id=<?= $oneEvent['idevent'] ?>" class="btn btn-outline-secondary" style="margin-bottom: 10px;">Modifier</a>
                            <?php } else { ?>
                                <a href="detailEvent.php?id=<?= $oneEvent['idevent'] ?>" class="btn btn-outline-secondary" style="margin-bottom: 10px;">Billeterie</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php

include "footer.php";

?>