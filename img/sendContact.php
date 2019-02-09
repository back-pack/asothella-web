<?php

function sendMail($email_to, $email_from, $email_message, $email_subject) {
    $headers = 'From: '.$email_from."\r\n".
    'Reply-To: '.$email_from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
    $sent = @mail($email_to, $email_subject, $email_message, $headers);

    if(!$sent) {
        return json_encode([
            'code' => 200,
            'msg' => 'No se pudo enviar el correo. Intente nuevamente mas tarde.'
        ]);
    }

    return json_encode([
        'code' => 200,
        'msg' => '¡Correo enviado! Gracias por contactarse con nosotros.'
    ]);
}

if(!isset($_POST['nombre']) || !isset($_POST['localidad']) || !isset($_POST['email']) || !isset($_POST['telefono']) || !isset($_POST['comments'])) {
    return json_encode([
        'code' => 400,
        'msg' => 'Complete todos los campos por favor.'
    ]);
}
if (empty($_POST['recaptcha'])) {
	exit('Please set recaptcha variable');
}
$secret = "6LcSzl0UAAAAAEkXYLcVHoxZGVp70U0ixVJnGGnQ";
$captcha = $_POST['recaptcha'];

$post = http_build_query(
    array (
        'response' => $captcha,
        'secret' => $secret,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    )
);
$opts = array('http' => 
   array (
       'method' => 'POST',
       'header' => 'application/x-www-form-urlencoded',
       'content' => $post
   )
);
$context = stream_context_create($opts);
$serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
if (!$serverResponse) {
    $response = json_encode([
        'code' => 400,
        'msg' => 'Falla en la validación del reCaptcha.'
    ]);
    exit($response);
}
$captcha_success = json_decode($serverResponse);
if(!$captcha_success->success) {
    $response = json_encode([
        'code' => 400,
        'msg' => '¡reCaptcha invalido!'
    ]);
    exit($response);
}
//  $email_to = "info@tejas-asothella.com";
 $email_to = "oscar.eber@gmail.com";
 $email_from = $_POST['email'];
 $email_subject = "Contacto desde el sitio web";
 $email_message = "Detalles del formulario de contacto:\n\n";
 $email_message .= "Nombre: " . $_POST['nombre'] . "\n";
 $email_message .= "Localidad: " . $_POST['localidad'] . "\n";
 $email_message .= "E-mail: " . $_POST['email'] . "\n";
 $email_message .= "Teléfono: " . $_POST['telefono'] . "\n";
 $email_message .= "Comentarios: " . $_POST['comments'] . "\n\n";

 $response = sendMail($email_to, $email_from, $email_message, $email_subject);

 echo $response;

