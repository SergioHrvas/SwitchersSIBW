<?php

// Inicializamos el motor de plantillas
require_once '/usr/local/lib/php/vendor/autoload.php';
include "mysql.php";

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);
// Averiguo que la página que se quiere mostrar es la del producto 12,
// porque hemos accedido desde http://localhost/?producto=12
// Busco en la base de datos la información del producto y lo
// almaceno en las variables $productoNombre, $productoMarca, $productoFoto...
$idEv = 1;
if (isset($_GET['ev'])) {
    $idEv = $_GET['ev'];
}
else {
    $idEv = 1;
}
$mysqli = new Database();

$mysqli->identificarse();
$evento = $mysqli->getJuegos($idEv);

echo $twig->render('portada.html', ['evento' => $evento]); //Pasamos información de juegos para la portada a la plantilla 

?>
