<?php
include_once '../BaseDeDatos.php';

$baseDeDatos = new BaseDatos();

$seInicio = $baseDeDatos->Iniciar();

if($seInicio){
    echo "CONEXION EXITOSA CON LA BASE DE DATOS" . "\n";
}else{
    $errorCarga = $baseDeDatos->getError();
    echo $errorCarga;
}

