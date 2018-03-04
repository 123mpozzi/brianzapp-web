<?php

include("../auth.php");


?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">

<div class="homepage-container custom-card">
    <!-- HomePage Header: titolo e pulsante logout + filtri -->
    <div class="homepage-header">
        <!-- TitleBar: titolo + pulsante logout -->
        <div class="homepage-titlebar">
            <h1>Proci</h1>
            <a id="homepage-title-logout" class="btn btn-warning icon-font-container" title="Logout" href="logout.php">
                <i class="material-icons">exit_to_app</i>
            </a>
        </div>
        <!-- Filtri di ricerca -->
        <div class="homepage-searchbar">
            <button id="homepage-filter-icon" class="btn btn-primary icon-font-container" title="Filtra risultati">
                <i class="material-icons">filter_list</i>
            </button>
            <form id="homepage-filter-form" action="homepage.php" method="get">
                <input name="filter_titles" class="form-control" type="text" placeholder="cerca titoli..." pattern="^[a-zA-Z0-9 ]+$"  title="Solo lettere, spazi e numeri!" value="' . $tit . '">
                <input name="filter_authors" class="form-control" type="text" placeholder="cerca autori..." pattern="^[a-zA-Z ]+$"  title="Solo lettere e spazi!" value="' . $aut . '">
                <input name="filter_titles" class="form-control" type="text" placeholder="cerca titoli..." pattern="^[a-zA-Z0-9 ]+$"  title="Solo lettere, spazi e numeri!" value="' . $tit . '">
                <input name="filter_titles" class="form-control" type="text" placeholder="cerca titoli..." pattern="^[a-zA-Z0-9 ]+$"  title="Solo lettere, spazi e numeri!" value="' . $tit . '">
                <input class="btn btn-primary" type="submit" value="Filtra"/>
            </form>
        </div>
    </div>
    <!-- HomePage Content Wrapper: wrapper del contenitore delle notifiche -->
    <div class="homepage-content-wrapper">
        <!-- Contenitore delle notifiche -->
        <div class="homepage-content">
            <?php
                $q = "SELECT * FROM notifiche WHERE ? = 'a'";//è stato messo il where sempre vero perchè senza ? non va
                $stmt = executePrep($dbc, $q, "s", ["a"]);
                $notifiche = $stmt->get_result();
                foreach($notifiche as $notifica){
                    //print_r($notifica);
                    echo '<div class="homepage-item alert-danger" style="background-color: #' . $notifica['colore'] . '">
                            <h3>' . $notifica['pdf'] . '</h3>
                            <div class="priority alert-danger" style="background-color: #' . $notifica['colore'] . '">';
                               for($i = 0; $i < $notifica['stelle']; $i++ ) {
                                   echo '<i class="material-icons">star</i>';
                               }
                        echo '</div>                        
                                <p>Testo mandato in data: ' . $notifica['data'] . ' di colore ' . $notifica['colore'] . ' .
                                </p>
                                <a href="../../PDF/' . $notifica['pdf'] . '" download="' . $notifica['pdf'] . '">SCARICA FILE</a>
                              </div>';
                    }
            ?>
            <!-- Notifiche -->
            <div class="homepage-item alert-danger">
                <h3>Titolo Notizia - testo corto</h3>
                <div class="priority alert-danger">
                    <i class="material-icons">star</i>
                    <i class="material-icons">star</i>
                    <i class="material-icons">star</i>
                </div>
                
                <p>Questo è il testo della notizia.
                </p>
            </div>
            <div class="homepage-item alert-success">
                <h3>Testo medio</h3>
                <div class="priority alert-success">
                    <i class="material-icons">star</i>
                </div>
                <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
            </div>
            <div class="homepage-item alert-warning">
                <h3>testo lungo</h3>
                <div class="priority alert-warning">
                    <i class="material-icons">star</i>
                    <i class="material-icons">star</i>
                </div>
                <p>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
            </div>
            <div class="homepage-item">
                <h3>Titolo Notizia - testo corto</h3>
                <p>Questo è il testo della notizia.
                </p>
            </div>
            <div class="homepage-item">
                <h3>Titolo Notizia - testo corto</h3>
                <p>Questo è il testo della notizia.
                </p>
            </div>
            <div class="homepage-item">
                <h3>Titolo Notizia - testo corto</h3>
                <p>Questo è il testo della notizia.
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
