<?php
session_start();

// Inicializamos el motor de plantillas
require_once '/usr/local/lib/php/vendor/autoload.php';
include "mysql.php";

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$mysqli = new Database();
$mysqli->identificarse();

if (isset($_GET['ev'])) {
  $idEv = $_GET['ev'];
} else {
  $idEv = 1;
}

if (isset($_SESSION['nickUsuario'])) {
  $nombreUsuario = $_SESSION['nickUsuario'];
  $usuario = $mysqli->getDatosUsuario($nombreUsuario);
}

$user = $mysqli->getDatosUsuario($idEv);

if (($user[0]['id'] == $usuario[0]['id'] or $usuario[0]['super'] == 1 || $usuario[0]['super'] == 1)&& $usuario != null) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['imagenperfil'])) {

      $errors = array();
      $file_name = $_FILES['imagenperfil']['name'];
      $file_size = $_FILES['imagenperfil']['size'];
      $file_tmp = $_FILES['imagenperfil']['tmp_name'];
      $file_type = $_FILES['imagenperfil']['type'];
      $file_ext = strtolower(end(explode('.', $_FILES['imagenperfil']['name'])));

      $extensions = array("jpeg", "jpg", "png");

      if (in_array($file_ext, $extensions) === false) {
        $errors[] = "Extensión no permitida, elige una imagen JPEG o PNG.";
      }

      if ($file_size > 2097152) {
        $errors[] = 'Tamaño del fichero demasiado grande';
      }

      if (empty($errors) == true) {
        move_uploaded_file($file_tmp, "./status/image/" . $file_name);
        $varsParaTwig['imagenperfil'] = "./status/image/" . $file_name;
      }

      if (sizeof($errors) > 0) {
        $varsParaTwig['errores'] = $errors;
      }

      $_POST['username'] = $user[0]['username'];
      $_POST['imagenperfil'] = $file_name;
      if ($_POST['imagenperfil'] == "") {
        $_POST['imagenperfil'] = $user[0]['imagen_perfil'];
      }

      if ($_POST['telefono'] == "") {
        $_POST['telefono'] = $user[0]['telefono'];
      }
      if ($_POST['nombre'] == "") {
        $_POST['nombre'] = $user[0]['nombre'];
      }
      if ($_POST['apellidos'] == "") {
        $_POST['apellidos'] = $user[0]['apellidos'];
      }
      if ($_POST['pais'] == "") {
        $_POST['pais'] = $user[0]['pais'];
      }
      if ($_POST['correo'] == "") {
        $_POST['correo'] = $user[0]['correo'];
      }
      if ($_POST['twitter'] == "") {
        $_POST['twitter'] = $user[0]['twitter'];
      }
      if ($_POST['facebook'] == "") {
        $_POST['facebook'] = $user[0]['facebook'];
      }
      if ($_POST['instagram'] == "") {
        $_POST['instagram'] = $user[0]['instagram'];
      }

      $mysqli->modificarPerfil($_POST, $nombreUsuario);
      $usuario = $mysqli->getDatosUsuario($nombreUsuario);
      $user = $mysqli->getDatosUsuario($idEv);
    }
  }
  $paises = $mysqli->getPaises();
  echo $twig->render('modificarperfil.html', ['paises' => $paises, 'usuario' => $usuario, 'user' => $user]); //Pasamos información de juegos para la portada a la plantilla 
} else {
  $link="perfil/".$user[0]['username'];
  echo $twig->render('mensaje.html', ['link'=>$link,'usuario' => $usuario, 'tipo' => 'Error', 'mensaje' => "No tiene acceso a esta información"]); //Pasamos información de juegos para la portada a la plantilla 

}
?>