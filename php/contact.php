<?php

define("RECAPTCHA_V3_SECRET_KEY", '6LdX1b4aAAAAAEvpccs0xWKzFfG2BQY-SD-WFtnd');

if(!$_POST) exit;

  // Email verificacion, NO Editar
  function isEmail($email) {
  	return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$email));
}

if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");


  $name     = $_POST['name'];
  $email    = $_POST['email'];
  $phone    = $_POST['phone'];
  $honeypot = $_POST['algosimple'];
  $comments = $_POST['comments'];


  if(trim($name) == '') {
  	echo '<div class="error_message">Debe ingresar su nombre.</div>';
  	exit();
  }

  if(trim($email) == '') {
  	echo '<div class="error_message">Ingrese un email válido.</div>';
  	exit();
  } else if(!isEmail($email)) {
  	echo '<div class="error_message">Ha ingresado un email inválido. Intente nuevamente.</div>';
  	exit();
}

if(trim($phone) == '') {
	echo '<div class="error_message">Debe ingresar su teléfono.</div>';
	exit();
}

if(!empty($honeypot)){
	exit();
}

if(trim($comments) == '') {
	echo '<div class="error_message">Ingrese su mensaje.</div>';
	exit();
}

if(get_magic_quotes_gpc()) {
	$comments = stripslashes($comments);
}

$token = $_POST['token'];
$action = $_POST['action'];

// call curl to POST request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => RECAPTCHA_V3_SECRET_KEY, 'response' => $token)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$arrResponse = json_decode($response, true);

// verify the response
if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
    // valid submission
    // go ahead and do necessary stuff
  $dest = "cristian.ramirez@urbanus.cl";

  $headers = "From: ".$email."\r\n";
  $headers .= "X-Mailer: PHP5\n";
  $headers .= 'MIME-Version: 1.0' . "\n";
  $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

  $asunto = "Contacto Desde Sitio Web";
  $cuerpo = "Nombre: ".$name."<br>";
  $cuerpo .= "Email: ".$email."<br>";
  $cuerpo .= "Fono: ".$phone."<br>";
  $cuerpo .= "Mensaje: ".$comments."<br>";

  if(mail($dest, $asunto, $cuerpo, $headers)) {

    // Email enviado, echo página de éxito

    echo "<fieldset>";
    echo "<div id='success_page'>";
    echo "<h3>Email Enviado</h3>";
    echo "<p>¡Genial <strong>$name</strong>!, Le contactaremos a la brevedad.</p>";
    echo "</div>";
    echo "</fieldset>";

  } else {

    echo 'Error, favor reintente.';

  }

} else {
  // spam submission
  // show error message
  echo '<div class="error_message">Error en Captcha.</div>';
    exit();
}











