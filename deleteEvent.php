<?php
include "header.php";

$id = $_GET['id'];
$pdo = new \PDO('mysql:host=localhost;dbname=donkeyEvent', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Démarre une transaction
    $pdo->beginTransaction();

    // Supprime toutes les dates liées à l'événement
    $sqlDeleteDates = "DELETE FROM date WHERE idevent = :id";
    $stmtDeleteDates = $pdo->prepare($sqlDeleteDates);
    $stmtDeleteDates->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDeleteDates->execute();

    // Supprime toutes les liaisons d'artistes à cet événement
    $sqlDeleteArtists = "DELETE FROM event_has_artist WHERE idevent = :id";
    $stmtDeleteArtists = $pdo->prepare($sqlDeleteArtists);
    $stmtDeleteArtists->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDeleteArtists->execute();

    // Supprime l'événement
    $sqlDeleteEvent = "DELETE FROM event WHERE idevent = :id";
    $stmtDeleteEvent = $pdo->prepare($sqlDeleteEvent);
    $stmtDeleteEvent->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDeleteEvent->execute();

    // Commit la transaction
    $pdo->commit();
    header("location:agenda.php");
} catch (Exception $e) {
    // Une erreur est survenue, annuler toutes les modifications
    $pdo->rollBack();
    echo "Erreur lors de la suppression de l'événement : " . $e->getMessage();
}

include "footer.php";

?>