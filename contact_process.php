<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        var_dump($_POST);
        // access
        $secretKey = '__CLAVE SECRETA __';
        //$captcha = $_POST['g-recaptcha-response'];
       
        $captcha = $_POST['token'];
        if(!$captcha){
          echo '<p class="alert alert-warning">Por favor presiona el captcha.</p>';
          exit;
        }

        $mail_to = "tuemail@server.com";
        
        # Sender Data
        $subject = trim($_POST["subject"]);
        $name = str_replace(array("\r","\n"),array(" "," ") , strip_tags(trim($_POST["name"])));
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $phone = trim($_POST["phone"]);
        $city = trim($_POST["city"]);
        $option = trim($_POST["option"]);
            switch ($option) {
                case '1':
                $mail_to = 'tuemail@server.com';
                    break; 
                case '2':
                $mail_to = 'tuemail@server.com';
                    break;
                case '3':
                $mail_to = 'tuemail@server.com';
                    break;
                case '4':
                $mail_to = 'tuemail@server.com';
                    break;
                }
        $message = trim($_POST["message"]);

        if ( empty($name) OR !filter_var($email, FILTER_VALIDATE_EMAIL) OR empty($subject) OR empty($message)) {
            # Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo '<p class="alert alert-warning">Por favor completa los campos necesarios.</p>';
            exit;
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        //$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = [
            'secret' => $secretKey,
            'response' => $_POST['token'],
            'remoteip' => $ip
        ];

        $options = array(
            'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data)
            )
          );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

       $responseKeys = json_decode($response, true);
       
       

        if($responseKeys['success'] != true) {
          echo '<p class="alert alert-warning">Por favor presiona el captcha.</p>';
        } else {

         # Mail Content
            $content = "Name: $name\n";
            $content .= "Email: $email\n\n";
            $content .= "Phone: $phone\n";
            $content .= "City: $city\n";
            $content .= "Option: $option\n";
            $content .= "Message:\n$message\n";

            # email headers.
            $headers = "From: $name <$email>";

            # Send the email.
            $success = mail($mail_to, $subject, $content, $headers);
            if ($success) {
                # Set a 200 (okay) response code.
                http_response_code(200);
                echo '<p class="alert alert-success">Gracias! Tu mensaje fue enviado.</p>';
            } else {
                # Set a 500 (internal server error) response code.
                http_response_code(500);
                echo '<p class="alert alert-warning">Oops! Algo salió mal, revisa de nuevo.</p>';
            }
        
        
         }


    } else {
        # Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo '<p class="alert alert-warning">Hay algún problema en tu registro, inténtalo de nuevo.</p>';
    }

?>