<?php
include "header.php";

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

if ($start_date === null || $end_date === null) {
    exit("Les dates de début et de fin doivent être spécifiées.");
}

$pdo = new \PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statementEvents = $pdo->prepare("SELECT event.*, GROUP_CONCAT(artist.name) AS artists
                            FROM event
                            LEFT JOIN event_has_artist ON event.idevent = event_has_artist.idevent
                            LEFT JOIN artist ON event_has_artist.idartist = artist.idartist
                            WHERE EXISTS (
                                SELECT 1 FROM date 
                                WHERE date.idevent = event.idevent
                                AND date.date BETWEEN :start_date AND :end_date
                            )
                            GROUP BY event.idevent");
$statementEvents->bindValue(':start_date', $start_date, PDO::PARAM_STR);
$statementEvents->bindValue(':end_date', $end_date, PDO::PARAM_STR);
$statementEvents->execute();
$events = $statementEvents->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if (count($events) > 0) { ?>
    <section class="detailEvent">
        <div class="container">
            <div class="row justify-content-center">
                <?php foreach ($events as $oneEvent) { ?>
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <img src="<?= $oneEvent['picture'] ?>" class="card-img" alt="Event Image" style="margin-top: 20px; margin-left: 20px;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $oneEvent['eventName'] ?></h5>
                                        <p class="card-text"><?= $oneEvent['category'] ?></p>
                                        <p class="card-text"><?= $oneEvent['artists'] ?></p>
                                        <?php
                                        $statementDates = $pdo->prepare("SELECT date.date, date.time
                                            FROM date
                                            WHERE date.idevent = :idevent
                                            AND date.date BETWEEN :start_date AND :end_date");
                                        $statementDates->bindValue(':idevent', $oneEvent['idevent'], PDO::PARAM_INT);
                                        $statementDates->bindValue(':start_date', $start_date, PDO::PARAM_STR);
                                        $statementDates->bindValue(':end_date', $end_date, PDO::PARAM_STR);
                                        $statementDates->execute();
                                        $dates = $statementDates->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($dates as $date) {
                                            ?>
                                            <p><?= $date['date'] ?> <?= $date['time'] ?></p>
                                        <?php } ?>
                                        <a href="detailEvent.php?id=<?= $oneEvent['idevent'] ?>" class="btn btn-outline-secondary" style="margin-bottom: 10px;">Réservez</a>
                                        <h4 class="price"><?= $oneEvent['price'] ?>€</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>


<?php
include "footer.php";
?>