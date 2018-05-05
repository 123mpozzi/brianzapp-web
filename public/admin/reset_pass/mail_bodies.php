<?php

include_once('sendmail.php');

/*
 * Classe contenente funzioni che generano i vari oggetti HTML che rappresentano i diversi tipi di email
 */

/**
 * Ottiene l'header presente in ogni email, principalmente tutti gli stili css da utilizzare nelle mail.
 *
 * @return string Header delle email
 */
function getMailHeader()
{
    // nelle email gli stili è meglio applicarli inline (senza file esterni) per evitare errori
    return "<!doctype html>
<html>
  <head>
    <meta name=\"viewport\" content=\"width=device-width\">
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
    <title>Simple Transactional Email</title>
    <style>
@media only screen and (max-width: 620px) {
  table[class=body] h1 {
    font-size: 28px !important;
    margin-bottom: 10px !important;
  }

  table[class=body] p,
table[class=body] ul,
table[class=body] ol,
table[class=body] td,
table[class=body] span,
table[class=body] a {
    font-size: 16px !important;
  }

  table[class=body] .wrapper,
table[class=body] .article {
    padding: 10px !important;
  }

  table[class=body] .content {
    padding: 0 !important;
  }

  table[class=body] .container {
    padding: 0 !important;
    width: 100% !important;
  }

  table[class=body] .main {
    border-left-width: 0 !important;
    border-radius: 0 !important;
    border-right-width: 0 !important;
  }

  table[class=body] .btn table {
    width: 100% !important;
  }

  table[class=body] .btn a {
    width: 100% !important;
  }

  table[class=body] .img-responsive {
    height: auto !important;
    max-width: 100% !important;
    width: auto !important;
  }
}
@media all {
  .ExternalClass {
    width: 100%;
  }

  .ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
    line-height: 100%;
  }

  .apple-link a {
    color: inherit !important;
    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    text-decoration: none !important;
  }

  .btn-primary table td:hover {
    background-color: #34495e !important;
  }

  .btn-primary a:hover {
    background-color: #34495e !important;
    border-color: #34495e !important;
  }
}
</style>
  </head>";
}

/**
 * Genera l'oggetto HTML rappresentante l'email da cui resettare la password
 *
 * @param string $link Link per resettare la password
 *
 * @return string Oggetto HTML rappresentante l'email da cui resettare la password
 */
function getSuccessMailStyle($link)
{
    return getMailHeader() . "
  <body class=\"\" style=\"background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;\">
    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"body\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;\">
      <tr>
        <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">&nbsp;</td>
        <td class=\"container\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;\">
          <div class=\"content\" style=\"box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;\">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class=\"preheader\" style=\"color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;\">Resetta la password per il servizio BrianzApp.</span>
            <table class=\"main\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;\">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class=\"wrapper\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;\">
                  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">
                    <tr>
                      <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\"></p><h1>BrianzApp Web</h1><p></p>
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\">E' stata mandata una richiesta per resettare la password per accedere al servizio, cliccare sul pulsante sottostante per proseguire con il reset della password</p>
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"btn btn-primary\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;\">
                          <tbody>
                            <tr>
                              <td align=\"left\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;\">
                                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;\">
                                  <tbody>
                                    <tr>
                                      <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;\"> <a href=\"" . $link . "\" target=\"_blank\" style=\"display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;\">Imposta Nuova Password</a> </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\">Vi auguriamo una buona giornata.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>

            <!-- START FOOTER -->
            <div class=\"footer\" style=\"clear: both; Margin-top: 10px; text-align: center; width: 100%;\">
              <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">
                <tr>
                  <td class=\"content-block\" style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;\">
                    <span class=\"apple-link\" style=\"color: #999999; font-size: 12px; text-align: center;\">Protezione Civile Brianza</span>
                  </td>
                </tr>
                <tr>
                  <td class=\"content-block powered-by\" style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;\">
                    Powered by <a href=\"http://htmlemail.io\" style=\"color: #999999; font-size: 12px; text-align: center; text-decoration: none;\">HTMLemail</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          <!-- END CENTERED WHITE CONTAINER -->
          </div>
        </td>
        <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
    ";
}

/**
 * Genera l'oggetto HTML rappresentante l'email di errore nella procedura di reset password
 *
 * @return string Oggetto HTML rappresentante l'email di errore nella procedura di reset password
 */
function getFailMailStyle()
{
    return getMailHeader() . "
  <body class=\"\" style=\"background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;\">
    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"body\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;\">
      <tr>
        <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">&nbsp;</td>
        <td class=\"container\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;\">
          <div class=\"content\" style=\"box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;\">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class=\"preheader\" style=\"color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;\">Resetta la password per il servizio BrianzApp</span>
            <table class=\"main\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;\">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class=\"wrapper\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;\">
                  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">
                    <tr>
                      <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\"></p><h1>BrianzApp Web</h1><p></p>
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\">Errore nella generazione del link per resettare la password, riprovare, e se l'errore persiste, contattare i tecnici.</p>
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\">Vi auguriamo una buona giornata.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>

            <!-- START FOOTER -->
            <div class=\"footer\" style=\"clear: both; Margin-top: 10px; text-align: center; width: 100%;\">
              <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">
                <tr>
                  <td class=\"content-block\" style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;\">
                    <span class=\"apple-link\" style=\"color: #999999; font-size: 12px; text-align: center;\">Protezione Civile Brianza</span>
                  </td>
                </tr>
                <tr>
                  <td class=\"content-block powered-by\" style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;\">
                    Powered by <a href=\"http://htmlemail.io\" style=\"color: #999999; font-size: 12px; text-align: center; text-decoration: none;\">HTMLemail</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          <!-- END CENTERED WHITE CONTAINER -->
          </div>
        </td>
        <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
    ";
}

/**
 * Genera l'oggetto HTML rappresentante l'email di segnalazione del compimento della procedura di reset password
 *
 * @return string Oggetto HTML rappresentante l'email di segnalazione del compimento della procedura di reset password
 */
function getBroadcastMailBody()
{
    return getMailHeader() . "
  <body class=\"\" style=\"background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;\">
    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"body\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;\">
      <tr>
        <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">&nbsp;</td>
        <td class=\"container\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;\">
          <div class=\"content\" style=\"box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;\">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class=\"preheader\" style=\"color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;\">E' stata cambiata la password per il servizio BrianzApp.</span>
            <table class=\"main\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;\">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class=\"wrapper\" style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;\">
                  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">
                    <tr>
                      <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\"></p><h1>BrianzApp Web</h1><p></p>
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\">E' stata cambiata la password di accesso alle ore <b>" . date("H:i") . "</b> del giorno <b>" . date("d/m/Y") . "</b></p>
                        <p style=\"font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;\">Vi auguriamo una buona giornata.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>

            <!-- START FOOTER -->
            <div class=\"footer\" style=\"clear: both; Margin-top: 10px; text-align: center; width: 100%;\">
              <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">
                <tr>
                  <td class=\"content-block\" style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;\">
                    <span class=\"apple-link\" style=\"color: #999999; font-size: 12px; text-align: center;\">Protezione Civile Brianza</span>
                  </td>
                </tr>
                <tr>
                  <td class=\"content-block powered-by\" style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;\">
                    Powered by <a href=\"http://htmlemail.io\" style=\"color: #999999; font-size: 12px; text-align: center; text-decoration: none;\">HTMLemail</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          <!-- END CENTERED WHITE CONTAINER -->
          </div>
        </td>
        <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
    ";
}

/**
 * Restituisce il corretto oggetto HTML rappresentante l' email corretta a seconda del risultato del procedimento
 * (successo o fallimento)
 *
 * @param mysqli          $dbc
 * @param string[]|string $alert Var that will contain the HTML alert div body
 *
 * @return string codice HTML dell'email corretta a seconda del risultato del procedimento (successo o fallimento)
 */
function getResetMailBody(mysqli $dbc, &$alert)
{
    $body = '';
    
    // se la generazione del link di reset password è riuscita
    if (isset($_SESSION[KEY_LOGRESET_LINK]))
    {
        // ottengo la mail normale per il reset password
        $body .= getSuccessMailStyle($_SESSION[KEY_LOGRESET_LINK]);
        unset($_SESSION[KEY_LOGRESET_LINK]);
    }
    else
    {
        // se ci sono errori, ottenere la mail di fallimento
        $body .= getFailMailStyle();
        
        // cerco allora almeno di resettare il token presente nel db relativo all'user loggato
        if (isset($_SESSION[KEY_LOGGED_IN]))
        {
            $user = $_SESSION[KEY_LOGGED_IN];
            
            // reset token to null
            $qu = "UPDATE utente SET token=NULL WHERE user=?;";
            $stmt = executePrep($dbc, $qu, "s", [$user]);
    
            // If it did not run OK.
            if (mysqli_affected_rows($dbc) != 1)
            {
                // segnala gli errori
                array_push($errors, mysqli_error($dbc), "Errore di Sistema!", "Non è stato possibile resettare il token per un errore di sistema, contattare i tecnici. Ci scusiamo per l'inconveniente.", "The Query did not run OK.", 'Query' . interpolateQuery($qu, [$user]));
                reportErrors($alert, $errors, false);
            }
            
            $stmt->close();
            
            // spinge l'utente a riprovare il reset della password resettando quindi i vecchi valori delle sessioni
            if (isset($_SESSION[KEY_FORCE_RESET_PASSWORD]))
            {
                $_SESSION[KEY_FORCE_RESET_PASSWORD] = false;
                unset($_SESSION[KEY_FORCE_RESET_PASSWORD]);
            }
        }
    }
    
    return $body;
}


?>