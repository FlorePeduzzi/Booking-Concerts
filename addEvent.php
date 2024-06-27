<?php
include "header.php";

$pdo = new \PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        $sqlEvent = "INSERT INTO event (eventName, category, price) VALUES (:eventName, :category, :price)";
        $stmtEvent = $pdo->prepare($sqlEvent);
        $stmtEvent->bindParam(':eventName', $_POST['eventName']);
        $stmtEvent->bindParam(':category', $_POST['category']);
        $stmtEvent->bindParam(':price', $_POST['price']);
        $stmtEvent->execute();
        $eventId = $pdo->lastInsertId();

        if (!empty($_FILES['picture']['name'])) {
            $newFilename = uniqid() . '_' . basename($_FILES['picture']['name']);
            $dossierTempo = $_FILES['picture']['tmp_name'];
            $dossierSite = 'uploads/' . $newFilename;
            if (move_uploaded_file($dossierTempo, $dossierSite)) {
                $sqlPicture = "UPDATE event SET picture = :picture WHERE idevent = :id";
                $stmtPicture = $pdo->prepare($sqlPicture);
                $stmtPicture->bindParam(':picture', $dossierSite);
                $stmtPicture->bindParam(':id', $eventId);
                $stmtPicture->execute();
            }
        }

        foreach ($_POST['dates'] as $dateInfo) {
            $sqlDate = "INSERT INTO date (idevent, date, time) VALUES (:idevent, :date, :time)";
            $stmtDate = $pdo->prepare($sqlDate);
            $stmtDate->bindParam(':idevent', $eventId);
            $stmtDate->bindParam(':date', $dateInfo['date']);
            $stmtDate->bindParam(':time', $dateInfo['time']);
            $stmtDate->execute();
        }

        foreach ($_POST['artists'] as $artistName) {
            $sqlArtist = "INSERT INTO artist (name) VALUES (:name)";
            $stmtArtist = $pdo->prepare($sqlArtist);
            $stmtArtist->bindParam(':name', $artistName);
            $stmtArtist->execute();
            $artistId = $pdo->lastInsertId();

            $sqlEventArtist = "INSERT INTO event_has_artist (idevent, idartist) VALUES (:idevent, :idartist)";
            $stmtEventArtist = $pdo->prepare($sqlEventArtist);
            $stmtEventArtist->bindParam(':idevent', $eventId);
            $stmtEventArtist->bindParam(':idartist', $artistId);
            $stmtEventArtist->execute();
        }



        $pdo->commit();
        header("location:agenda.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erreur lors de l'ajout de l'événement : " . $e->getMessage();
    }
}
?>

<div class="detailcontainer">
    <div class="modify-event-card">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
            Ajouter un nouveau concert:
            <input class="form-label" name="eventName" type="text" placeholder="Nom de l'événement"><br>
            <div id="artistInputs">
                <div class="artist">
                    Nom de l'artiste:
                    <input class="form-label" name="artists[]" type="text" placeholder="Nom de l'artiste">

                </div>
                <button type="button" onclick="addArtist()">Ajouter un artiste</button>

            </div>
            Catégorie:
            <input class="form-label" name="category" type="text" placeholder="Catégorie"><br>
            Dates et heures:
            <div id="dateInputs">
                <div class="date-time">
                    <input type="hidden" name="dates[0][id]" value="0"> <!-- Champ caché pour l'ID -->
                    <input class="form-label" name="dates[0][date]" type="date">
                    <input class="form-label" name="dates[0][time]" type="time">
                </div>
            </div>
            <button type="button" onclick="addDateTime()">Ajouter une date et heure</button>
            ><br>
            Prix:
            <input class="form-label" name="price" type="text" placeholder="Prix"><br>
            Envoyer une image:
            <input type="file" name="picture" id="upload"><br>
            <input type="submit" value="Ajouter">
        </form>
    </div>
</div>

<script>
    function addDateTime() {
        const dateInputs = document.getElementById('dateInputs');
        const dateTimeDiv = document.createElement('div');
        dateTimeDiv.classList.add('date-time');
        dateTimeDiv.innerHTML = `
        <input class="form-label" name="dates[][date]" type="date">
        <input class="form-label" name="dates[][time]" type="time">
    `;
        dateInputs.appendChild(dateTimeDiv);
    }


    function addArtist() {
        const container = document.getElementById('artistInputs');
        const newField = document.createElement('div');
        newField.innerHTML = 'Nom de l\'artiste: <input class="form-label" name="artists[]" type="text" placeholder="Nom de l\'artiste">';
        container.appendChild(newField);
    }
</script>

<?php
include "footer.php";
?>