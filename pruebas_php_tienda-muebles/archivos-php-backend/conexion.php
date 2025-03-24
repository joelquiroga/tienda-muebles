<?php

  $host = 'XXXXXXXXXXX';
  $database = 'XXXXXXXXX';
  $user = 'XXXXXXXXXXXX';
  $password = 'XXXXXXXXXXX';

  // Crear conexión
  $conexion = new mysqli($host, $user, $password, $database);

  // Verificar conexión
  if ($conexion->connect_error) {
      die('<p>Error al conectar con servidor MySQL: '. $conexion->connect_error .'</p>');
  }//else {
   // echo '<p>Se ha establecido la conexión al servidor MySQL con éxito.</p>';
  //}
?>
