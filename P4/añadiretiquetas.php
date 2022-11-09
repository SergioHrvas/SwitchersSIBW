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
$mysqli = new Database();

$mysqli->identificarse();
session_start();

if (isset($_GET['ev'])) {
  $idEv = $_GET['ev'];
} else {
  $idEv = "leyendas";
}

if (isset($_SESSION['nickUsuario'])) {
  $nombreUsuario = $_SESSION['nickUsuario'];
  $usuario = $mysqli->getDatosUsuario($nombreUsuario);
}

$idjuego = $mysqli->getId($idEv);
$juego = $mysqli->getEvento($idjuego);

if ($usuario[0]['gestor'] == 1 or $usuario[0]['super'] == 1) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores = $_POST;
    foreach ($valores['label'] as $key => $value) {
      $mysqli->añadirEtiquetas($valores['label'][$key], $idjuego);
    }
    echo $twig->render('mensaje.html', ['tipo' => ':)', 'usuario' => $usuario, 'mensaje' => "Producto creado correctamente"]);
  } else {
    echo $twig->render('añadiretiquetas.html', ['usuario' => $usuario, 'link' => $juego['link']]);
  }
} else {
  echo $twig->render('mensaje.html', ['tipo' => 'Error','usuario' => $usuario, 'mensaje' => "No tiene acceso a esta información"]); //Pasamos información de juegos para la portada a la plantilla 

}
?>