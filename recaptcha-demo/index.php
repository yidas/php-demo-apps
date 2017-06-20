<?php

/**
 * reCAPTCHA.php
 *
 * @author 		Nick Tsai <myintaer@gmail.com>
 * @version 	v1.0.0 2016-10-19
 * @since 		Google reCaptcha v2.0.0 for PHP
 * @see 		https://github.com/google/recaptcha
 *				https://developers.google.com/recaptcha/intro				
 */

/**
 * Configuration
 */

// Register API keys at https://www.google.com/recaptcha/admin
$siteKey = '6LePiwkUAAAAAJ5xMfoRUrpfWNmbrBr9WAfd1nNd';
$secret = '6LePiwkUAAAAAD8hHV-UApmyJuWdJe4UEGO8uGVm';

// reCAPTCHA supported 40+ languages listed here: https://developers.google.com/recaptcha/docs/language
$lang = 'zh-tw';

// Trying turning this on while get invalid-json error 
$socketPostMethod = false;	


/* Autoloader by vendor */
require_once __DIR__ . '/vendor/autoload.php';

/* POST Process */
if (isset($_POST['g-recaptcha-response'])) {

	/* New ReCaptcha */
	if ($socketPostMethod) 
		$recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\SocketPost());
	else
		$recaptcha = new \ReCaptcha\ReCaptcha($secret);

	// Make the call to verify the response and also pass the user's IP address
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    // Output
    $result = ['post'=>$_POST];

    $result['isSuccess'] = $resp->isSuccess();

    if (!$result['isSuccess']) 
	    $result['errors'] = $resp->getErrorCodes();

    var_dump($result);exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>reCAPTCHA.php</title>
	<meta charset="UTF-8">
	<style type="text/css">
        body {
            margin: 1em 5em 0 5em;
            font-family: sans-serif;
        }
        fieldset {
            display: inline;
            padding: 1em;
        }
    </style>	
</head>
<body>

	<h1>reCAPTCHA Example</h1>

	<form action="" method="POST">
		<fieldset>

			<legend>Form</legend>

			<p>Example input: <input type="text" name="input" value="test"></p>

			<!-- Paste this snippet at the end of the <form> where you want the reCAPTCHA widget to appear: -->
			<div class="g-recaptcha" data-sitekey="<?=$siteKey?>"></div>

			<p>
				<button>Submit</button>
				<button type="button" onclick="submitCheck(this.form);">Submit With Check</button>
			</p>

			<!-- Paste this snippet before the closing </head> tag on your HTML template: -->
			<script src='https://www.google.com/recaptcha/api.js?hl=<?=$lang?>'></script>

		</fieldset>
	</form>

	<script type="text/javascript">
		
		function submitCheck(form) {

			if (grecaptcha.getResponse() == ""){

			    alert("Please check reCAPTCHA before submit");
			    return false;
			} 

			form.submit();
		}

	</script>

</body>
</html>