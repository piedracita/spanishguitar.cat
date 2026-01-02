<?php
$to = "arnaldpedros@gmail.com";
$subject = "Test simple";
$message = "Això és una prova de mail.";
$headers = "From: no-reply@spanishguitar.cat";

if(mail($to, $subject, $message, $headers)) {
    echo "Correu enviat pel servidor.";
} else {
    echo "El servidor NO ha pogut enviar el correu.";
}
?>