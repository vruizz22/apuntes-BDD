<?php

// Ejemplo que muestra como conectarse a la base de datos y ejecutar queries
// Recuerda que tienes que establecer una contraseña como indica el README
// Reemplaza `grupoX` y `contraseña` por el identificador de tu grupo y la
// contraseña que estableciste respectivamente.

echo "hola mundo! pan con palta";

$db = pg_connect("host=localhost port=5432 dbname=grupo15 user=grupo15 password=Elefante$15");
$result = pg_query($db, "SELECT * FROM Peliculas");
$rows = pg_fetch_all($result);
echo json_encode($rows);

//uhasasd

// ausdauaisd 
// xdddd soy yo momentoxd
?>

