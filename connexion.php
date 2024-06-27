<?php

include "header.php";
?>


<div>
    <h2 class="text-center mt-3">Connexion</h2>
    <div class="container mt-4">
        <form action="connexion_user.php" method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="email" aria-label="email" name="email"><br>
                <input class="form-control" type="text" placeholder="password" aria-label="password" name="password"><br>
                <button class="btn btn-outline-secondary" type="submit">Valider</button>
        </form>
    </div>
</div>
</div>



<?php
include "footer.php";
?>