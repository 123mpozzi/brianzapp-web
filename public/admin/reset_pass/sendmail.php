<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__ . '\..\..\..\vendor\autoload.php';


include_once __DIR__ . "\..\..\auth.php";
include_once __DIR__ . ".\mail_bodies.php";

// !!! Per far funzionare GMAIL: consentire l'accesso alle app meno sicure !!!

function sendMail($config, $subject, $body)
{
    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 4;
    
    //TODO. dividere i log per volta
    $mail->Debugoutput = function($str, $level) {
        $log_folder = $_SERVER["DOCUMENT_ROOT"] . '\WebApp\private\logs\smtp\\';
        
        if (!is_dir($log_folder)) {
            // dir doesn't exist, make it
            mkdir($log_folder);
        }
        
        file_put_contents($log_folder . date('d-m-Y_hia') . '.log', gmdate('Y-m-d H:i:s'). "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
    };
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->SMTPAutoTLS = false;
    //Set the hostname of the mail server
    $mail->Host = gethostbyname('smtp.gmail.com');
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6
    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;
    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'tls';
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $config['email']['username'];
    //Password to use for SMTP authentication
    $mail->Password = $config['email']['password'];
    
    try
    {
        //Set who the message is to be sent from
        $mail->setFrom($config['email']['username'], 'Protezione Civile');
        //Set an alternative reply-to address
        $mail->addReplyTo('jury.donofrio@issgreppi.it', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress($config['email']['username'], 'Protezione Civile');
        //Set the subject line
        $mail->Subject = $subject;
        $mail->isHTML();
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents($content), __DIR__);
        $mail->Body = $body;
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment('prova.txt');
        //send the message, check for errors
        if (!$mail->send()) {
            alert("danger", "Mailer Error", "Non è stato possibile inviare la mail, riprovare e, se persiste, contattare i tecnici. Ci scusiamo per l'inconveniente.");
    
            $errors[] = ["Mailer Error: ", $mail->ErrorInfo];
            //reportErrors($errors, false);
            
            //TODO: cosa fare col token? è stato generato?
        } else {
            alert("success", "Messaggio Inviato", "Email inviata con successo.");
            
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    }
    catch (Exception $e)
    {
        alert("danger", "Mailer Error", "Errore nell'invio della mail di reset password, riprovare e, se persiste, contattare i tecnici. Ci scusiamo per l'inconveniente.");
        
        $errors[] = ["Errore nell'invio della mail di reset password! Exception: ", $e];
        //reportErrors($errors, false);
    }
    
    # Controlla se il modulo SSL è abilitato nel config php.ini
    $errors[] = ["DEBUG: ", extension_loaded('openssl') ? 'SSL module loaded' : 'SSL module not loaded'];
    reportErrors($errors, false);
}

?>