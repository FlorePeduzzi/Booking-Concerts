<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <title>DonkeyEvent</title>
</head>

<body>
    <?php
    session_start();

    $pdo = new PDO('mysql:host=localhost;dbname=DonkeyEvent', 'root');
    ?>
    <div class="content">
        <header>
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <a class="navbar-brand" href="donkeyEvent.php">DonkeyEvent</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="agenda.php">Agenda</a>
                            </li>
                        </ul>
                        <form action="dates.php" method="GET" class="d-flex align-items-center">
                            <label for="start">&nbsp&nbspdu&nbsp;</label>
                            <input type="date" id="start" name="start_date" value="2024-05-06" min="2024-05-06" max="2025-12-31" class="form-control me-2" />
                            <label for="end">&nbsp;au&nbsp;</label>
                            <input type="date" id="end" name="end_date" value="2024-05-06" min="2024-05-06" max="2025-12-31" class="form-control me-2" />
                            <button class="btn btn-outline-secondary" type="submit">Valider</button>
                        </form>
                        <form action="search.php" method="GET" class="d-flex align-items-center ms-3">
                            <input class="form-control me-2" type="search" placeholder="Concert, catégorie, artiste" aria-label="search" name="search" style="width: 280px;">
                            <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                        </form>
                        <div class="verticalline"></div>
                        <div>
                            <?php if (isset($_SESSION["user"]) || (isset($_SESSION["admin"]) && $_SESSION["admin"])) : ?>
                                <form action="deconnexion.php" class="d-flex">
                                    <button class="btn btn-outline-secondary" type="submit">Déconnexion</button>
                                </form>
                            <?php else : ?>
                                <form action="connexion.php" class="d-flex">
                                    <button class="btn btn-outline-secondary" type="submit">Connexion</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <div class="carticon ms-2">
                            <a href="cart.php" class="nav-link">
                                <i class="fa fa-shopping-cart"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

    </div>
    <?php
    ?>

    <?php

    if (isset($_SESSION["user"])) {
        echo $_SESSION["user"] . " vous êtes connecté(e)";
        $email = $_SESSION["user"];
        $statement = $pdo->prepare("SELECT iduser FROM user WHERE email = :email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $iduser = $statement->fetchColumn();
        $_SESSION['iduser'] = $iduser;
    }





    if (isset($_SESSION["admin"])) {
        echo $_SESSION["admin"] . " vous êtes connecté(e)";
    }
    ?>