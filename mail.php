<?php
    require 'vendor/autoload.php';
    use Mailgun\Mailgun;
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
    session_start();
    $request_body = file_get_contents('php://input');
    $json = json_decode($request_body);
    foreach($json as $value){
        if($value === ""){
            echo "No llenó toda la información necesaria";
            http_response_code(404);
            return;
        }
    }
    if (!empty($json->token)) {
        if (hash_equals($_SESSION['token'], $json->token)) {
                //# Instantiate the client.
                $message = "
                    Email: $json->correo \n
                    Nombre: $json->nombre \n 
                    Empresa: $json->empresa \n 
                    Tipo de paquete: $json->paquete \n 
                    Comentarios: $json->comentarios
                ";
                $mgClient = new Mailgun(getenv("MAILGUN_KEY"));
                $domain = "mg.grupobiobc.com";

                # Make the call to the client.
                $result = $mgClient->sendMessage($domain, array(
                    'from'    => $json->nombre . '<mailgun@mg.grupobiobc.com>',
                    'to'      => 'Hector Mendoza <hectormjac@gmail.com>, Rufino Radilla <rufino.radilla@gmail.com>',
                    'subject' => 'Cliente Potencial Sistema',
                    'text'    => $message
                ));
                echo "Se ha enviado su mensaje. Nos pondremos en contacto lo más pronto posible.";
                return;
        } else {
            echo "Hubo un error enviando el mensaje. Favor de intentarlo de nuevo o recargar la página.";
            http_response_code(404);
            return;
        }
    }
    else{
        echo "Hubo un error enviando el mensaje. Favor de intentarlo de nuevo o recargar la página.";
        http_response_code(404);
        return;
    }
?>