<?php
include "header.php";

$id = $_GET['id'];

$pdo = new \PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root');

$statement = $pdo->prepare("SELECT *, DATE_FORMAT(date.time, '%H:%i') AS time_formatted FROM event JOIN date ON event.idevent = date.idevent WHERE event.idevent = :id ORDER BY date.date ASC");
$statement->bindParam(':id', $id, PDO::PARAM_INT);
$statement->execute();
$events = $statement->fetchAll(PDO::FETCH_ASSOC);
$statement2 = $pdo->prepare("SELECT name FROM artist 
                            JOIN event_has_artist ON event_has_artist.idartist = artist.idartist
                            JOIN event ON event_has_artist.idevent = event.idevent
                            WHERE event.idevent = :id");
$statement2->bindParam(':id', $id, PDO::PARAM_INT);
$statement2->execute();
$artists = $statement2->fetchAll(PDO::FETCH_ASSOC);

// Créer un tableau de dates à passer à JavaScript
$dates_vertes = [];
$dates_rouges = [];
foreach ($events as $event) {
    if ($event['numberPlaces'] < 1000) {
        $dates_vertes[] = $event['date'];
    } else {
        $dates_rouges[] = $event['date'];
    }
}

// Encoder les tableaux en JSON
$dates_vertes_json = json_encode($dates_vertes);
$dates_rouges_json = json_encode($dates_rouges);


// Pass iddate to JavaScript
$iddates = [];
foreach ($events as $event) {
    $iddates[] = $event['iddate'];
}
$iddates_json = json_encode($iddates);
?>


<section class="detailEvent" style="display: flex; justify-content: center;">
    <div class="detailcontainer" style="width: 80%;">
        <div class="event-card" style="width: 100%;">
            <div class="event-image"><img src="<?= $event['picture'] ?>" style="width: 100%;" ;></div>
            <div class="event-details" style="display: flex; flex-direction: row; width: 100%;">
                <div style="flex-grow: 1;">
                    <h4><?= $events[0]['eventName'] ?></h4>
                    <p class="event-info"><?= $events[0]['category'] ?><br>
                        <?php foreach ($artists as $artist) { ?>
                            <?= $artist['name'] ?></p>
                        <?php } ?>
                    <p>Date(s) : <br>
                        <?php foreach ($events as $event) { ?>
                            <?= $event['date'] . ': ' . $event['time_formatted'] ?></p>
                        <?php } ?>
                    <div>
                        <span class="price"><?= $events[0]['price'] ?>€</span>
                    </div>
                </div>
                <div id="calendar" style="margin-left: 20px;"></div>
            </div>
        </div>
    </div>
</section>

<script>
    // Récupérer les données JSON dans JavaScript
    let datesVertes = <?php echo $dates_vertes_json; ?>;
    let datesRouges = <?php echo $dates_rouges_json; ?>;
    let iddates = <?php echo $iddates_json; ?>;

    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');

        let calendar = new FullCalendar.Calendar(calendarEl, {

            initialView: 'dayGridMonth',
            initialDate: datesVertes[0],
            locale: 'fr',
            events: [
                <?php foreach ($dates_vertes as $key => $date) { ?> {
                        title: '<?= $events[$key]['eventName'] ?>',
                        start: '<?= $date ?>', // Date à colorer en vert
                        url: '<?= "addcart.php?id=" . $iddates[$key] ?>', // Assigning iddate to the event object
                        backgroundColor: 'green',

                    },
                <?php } ?>
                <?php foreach ($dates_rouges as $key => $date) { ?> {
                        title: 'Complet',
                        start: '<?= $date ?>', // Date à colorer en rouge
                        backgroundColor: 'red',

                    },
                <?php } ?>
            ],
            eventClick: function(info) {
                // Redirect to booking.php when clicking on a green date
                if (info.event.backgroundColor === 'green') {
                    window.location.href = info.event.url;
                }
            }
        });

        calendar.render();
    });
</script>



<?php
include "footer.php";
?>