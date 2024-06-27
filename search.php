<?php
include "header.php";

$search = $_GET['search'];

$pdo = new \PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statementEvents = $pdo->prepare("SELECT event.*, GROUP_CONCAT(artist.name) AS artists
                            FROM event
                            LEFT JOIN event_has_artist ON event.idevent = event_has_artist.idevent
                            LEFT JOIN artist ON event_has_artist.idartist = artist.idartist
                            WHERE event.category LIKE :search 
                            OR event.eventName LIKE :search 
                            OR artist.name LIKE :search
                            GROUP BY event.idevent");
$statementEvents->bindValue(':search', "%$search%", PDO::PARAM_STR);
$statementEvents->execute();
$events = $statementEvents->fetchAll(PDO::FETCH_ASSOC);

?>

<?php
if (empty($_GET['search'])) {
    header("location:donkeyEvent.php");
} elseif (count($events) > 0) {
?>
    <section class="detailEvent" style="display: flex; justify-content: center;">
    <?php foreach ($events as $oneEvent) { ?>

        <div class="detailcontainer" style="width: 80%;">

            <div class="event-card" style="width: 100%;">
                <div class="event-image"><img src="<?= $oneEvent['picture'] ?>" style="width: 100%;"></div> 
                <div class="event-details" style="display: flex; flex-direction: row; width: 100%;"> 
                    <div style="flex-grow: 1;">
                        <h4><?= $oneEvent['eventName'] ?></h4>
                        <p class="event-info"><?= $oneEvent['category'] ?><br>
                            <?= $oneEvent['artists'] ?><br>
                        </p>
                        <?php
                        $statementDates = $pdo->prepare("SELECT date.date, date.time
                                            FROM date
                                            WHERE date.idevent = :idevent
                                            ORDER BY date.date ASC"); // Tri par ordre croissant
                        $statementDates->bindValue(':idevent', $oneEvent['idevent'], PDO::PARAM_INT);
                        $statementDates->execute();
                        $dates = $statementDates->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($dates as $date) {
                        ?>
                            <p><?= $date['date'] ?> <?= $date['time'] ?></p>
                        <?php
                        }
                        ?>
                        <button class="btn btn-outline-secondary" style='margin-bottom: 10px;'>Reservez</button>
                        <h4 class="price"><?= $oneEvent['price'] ?>€</h4>
                    </div>
                    <div id="calendar<?= $oneEvent['idevent'] ?>" style="margin-left: 20px;"></div>
                </div>
            </div>
        </div>
        <script>
            // JavaScript pour initialiser le calendrier pour chaque événement
            document.addEventListener('DOMContentLoaded', function() {
                let calendarEl<?= $oneEvent['idevent'] ?> = document.getElementById('calendar<?= $oneEvent['idevent'] ?>');

                let calendar<?= $oneEvent['idevent'] ?> = new FullCalendar.Calendar(calendarEl<?= $oneEvent['idevent'] ?>, {
                    initialView: 'dayGridMonth',
                    initialDate: '<?= $dates[0]['date'] ?>', // Utilisez la première date du concert
                    events: [
                        <?php foreach ($dates as $date) { ?> {
                                title: '<?= $oneEvent['eventName'] ?>',
                                start: '<?= $date['date'] ?>', // Date à colorer en vert
                                backgroundColor: 'green'
                            },
                        <?php } ?>
                    ],
                    eventClick: function(info) {
                        // Rediriger vers la page de réservation lorsque l'utilisateur clique sur un événement
                        window.location.href = "addcart.php";
                    },
                    locale: 'fr', // Définir la locale en français
                    dayHeaderFormat: { weekday: 'narrow' } // Formater le format d'en-tête de jour
                });

                calendar<?= $oneEvent['idevent'] ?>.render();
            });
        </script>
    <?php } ?>
</section>

<?php } ?>

<?php
include "footer.php";
?>
