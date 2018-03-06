<?php

$page_title = "HomePage";

include("../auth.php");

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">

<div class="homepage-container custom-card">
    <!-- HomePage Header: titolo e pulsante logout + filtri -->
    <div class="homepage-header">
        <!-- TitleBar: titolo + pulsante logout -->
        <div class="homepage-titlebar">
            <h1>Proci</h1>
            <!-- Filtri di ricerca -->
            <button id="homepage-filter-icon" class="btn btn-primary icon-font-container" title="Filtra risultati">
                <i class="material-icons">filter_list</i>
            </button>
            <!-- Logout Button -->
            <a id="homepage-title-logout" class="btn btn-warning icon-font-container" title="Logout" href="logout.php">
                <i class="material-icons">exit_to_app</i>
            </a>
        </div>
        <div class="homepage-searchbar">
            <button name="sort_titolo" type="button" class="btn btn-link">
                Titolo
                <i class="material-icons"><?php if (isset($up) and $up) echo 'arrow_upward'; else echo 'arrow_downward'; ?></i>
            </button>
            <button name="sort_stelle" type="button" class="btn btn-link">
                Stelle
                <i class="material-icons"><?php if (isset($up) and $up) echo 'arrow_upward'; else echo 'arrow_downward'; ?></i>
            </button>
            <button name="sort_date" type="button" class="btn btn-link">
                Data
                <i class="material-icons"><?php if (isset($up) and $up) echo 'arrow_upward'; else echo 'arrow_downward'; ?></i>
            </button>
        </div>
    </div>
    <!-- Homepage Filters for Mobiles, hidden by default -->
    <div id="homepage-mobile-filters">
        <form id="homepage-mobile-filter-form" action="homepage.php" method="get">
            Titolo
            <input name="filter_titolo" class="form-control" type="text" placeholder="cerca titoli..." value="<?php if(isset($titolo)) echo $titolo ?>">
            Provenienza
            <select name="filter_provenienza" class="form-control" title="Stelle">
                <option value="-1" selected="selected">Tutte</option>
                <?php
                $q = "SELECT id, nome FROM provenienza";
                $r = $dbc->query($q);
    
                while($row = $r->fetch_row()) {
                    //var_dump($row);
        
                    echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                }
    
                //mysqli_close($dbc);
                
                ?>
            </select>
            Stelle
            <select name="filter_stelle" class="form-control" title="Stelle">
                <option value="-1" selected="selected">Tutte</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
            Data Iniziale
            <input name="filter_start_date" class="form-control" type="date" title="Data iniziale">
            Data Finale
            <input name="filter_end_date" class="form-control" type="date" title="Data finale">
            <input name="filter-btn" class="btn btn-primary" type="submit" value="Filtra"/>
        </form>
    </div>
    <!-- HomePage Content Wrapper: wrapper del contenitore delle notifiche -->
    <div class="homepage-content-wrapper">
        <!-- Contenitore delle notifiche -->
        <div class="homepage-content">
            <?php
                $q = "SELECT * FROM notifica WHERE ? = 'a'";//è stato messo il where sempre vero perchè senza ? non va
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
