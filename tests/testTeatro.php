<?php
include '../EdificioTeatro.php';
include '../Actividad.php';
include '../Cine.php';
include '../Musical.php';
include '../ObraTeatro.php';

function crearTeatro()
{
    echo "Ingrese el nombre del teatro: ";
    $nombreTeatro = trim(fgets(STDIN));
    echo "Ingrese la direccion del teatro: ";
    $direccionTeatro = trim(fgets(STDIN));
    echo "Ingrese la ciudad en la que se encuentra el teatro: ";
    $ciudadTeatro = trim(fgets(STDIN));

    //creo objeto teatro
    $teatro = new EdificioTeatro($nombreTeatro, $direccionTeatro, $ciudadTeatro);
    echo "INSERTANDO TEATRO EN BASE DE DATOS..." . "\n";
    $respuesta = $teatro->insertar();

    if ($respuesta) {
        echo "OPERACION DE INSERCION EXITOSA" . "\n";
        echo "EL ID DEL NUEVO TEATRO ES: " . $teatro->getIdEdificioTeatro() . "\n";
        $colTeatros = $teatro->listar("");
        foreach ($colTeatros as $unTeatro) {
            echo $unTeatro;
            echo "-------------------------------------------------------" . "\n";
        }
    } else {
        echo "ERROR EN LA INSERCION: " . $teatro->getmensajeoperacion() . "\n";
    }
}

function datosModificarTeatro($objTeatro)
{
    // NOMBRE
    echo "Desea modificar el nombre del teatro? si/no: ";
    $respuestaNombre = trim(fgets(STDIN));
    if ($respuestaNombre == 'si') {
        echo "Nombre actual: " . $objTeatro->getNombre() . "\n";
        echo "Ingrese el nuevo nombre: ";
        $nuevoNombreTeatro = trim(fgets(STDIN));
    } else {
        $nuevoNombreTeatro = $objTeatro->getNombre();
    }
    // DIRECCION
    echo "Desea modificar la direccion del teatro? si/no: ";
    $respuestaDireccion = trim(fgets(STDIN));
    if ($respuestaDireccion == 'si') {
        echo "Direccion actual: " . $objTeatro->getDireccion() . "\n";
        echo "Ingrese la nueva direccion: ";
        $nuevaDireccionTeatro = trim(fgets(STDIN));
    } else {
        $nuevaDireccionTeatro = $objTeatro->getDireccion();
    }
    // CIUDAD
    echo "Desea modificar la ciudad del teatro? si/no: ";
    $respuestaCiudad = trim(fgets(STDIN));
    if ($respuestaCiudad == 'si') {
        echo "Ciudad actual: " . $objTeatro->getCiudad() . "\n";
        echo "Ingrese la nueva ciudad: ";
        $nuevaCiudadTeatro = trim(fgets(STDIN));
    } else {
        $nuevaCiudadTeatro = $objTeatro->getCiudad();
    }
    $arrayInfoTeatro = array('nombre' => $nuevoNombreTeatro, 'direccion' => $nuevaDireccionTeatro, 'ciudad' => $nuevaCiudadTeatro);
    return $arrayInfoTeatro;
}

function crearActividad($objActividadBase, $idTeatro)
{
    $coleccionActividadades = $objActividadBase->listar("");

    echo "Ingrese el nombre de la funcion: ";
    $nombreActividad = trim(fgets(STDIN));
    echo "Ingrese el precio de la funcion " . $nombreActividad . ": ";
    $precioActividad = trim(fgets(STDIN));
    echo "Ingrese la duracion de la funcion en minutos: ";
    $duracionActividad = trim(fgets(STDIN));
    echo "Ingrese la fecha de la funcion: " . "\n";
    echo "Dia: ";
    $dia = trim(fgets(STDIN));
    echo "Mes: ";
    $mes = trim(fgets(STDIN));
    echo "Año: ";
    $anio = trim(fgets(STDIN));

    $fecha = array('anio' => $anio, 'mes' => $mes, 'dia' => $dia);

    do {
        $existeHorarioActividad = false;
        echo "Ingrese el horario de la funcion" . "\n";
        echo "Hora: ";
        $horaActividad = trim(fgets(STDIN));
        echo "Minutos: ";
        $minutosActividad = trim(fgets(STDIN));
        
        //convierto el horario de comienzo de la nueva funcion a minutos
        $horarioFuncionEnMinutos = ($horaActividad * 60) + $minutosActividad;
        $horarioFinalizacionFuncion = $horarioFuncionEnMinutos + $duracionActividad;
        if($horarioFinalizacionFuncion>=1200){
            echo "DEBIDO A LAS MEDIDAS SANITARIAS IMPUESTA POR EL GOBIERNO DE LA NACION" . "\n";
            echo "EL ESTABLECIMIENTO SE ENCUENTRA CERRADO A PARTIR DE LAS 20 HORAS" . "\n";
            echo "SE LE SOLICITA INGRESAR UN HORARIO MENOS AL ESTABLECIDO" . "\n";
            $existeHorarioActividad = true;
        }else{
            foreach ($coleccionActividadades as $actividad) {
                // Extraigo la hora de inicio, duracion y fecha de la actividad
                $horaInicioArray = $actividad->getHoraInicio();
                $duracionActualActividad = $actividad->getDuracionActividad(); //me ayudo a identificar mi error augusto, sumele un punto porque lo va a necesitar xd
                $fechaActualActividad = $actividad->getFecha();
                // Desgloso la hora y fecha de la actividad para utilizarlas en la corroboracion de existencia mas abajo
                $horaInicio = $horaInicioArray['hora'];
                $minutoInicio = $horaInicioArray['minutos'];
                $diaActual = $fechaActualActividad['dia'];
                $mesActual = $fechaActualActividad['mes'];
                // Inicializo el horario de inicio y finalizacion de la actividad actual
                $horaInicioActividad = ($horaInicio * 60) + $minutoInicio;
                $horaFinalizacion = $horaInicioActividad + $duracionActualActividad;
                // Reviso si la actividad que se desea cargar es el mismo dia de la actividad actual
                if (($diaActual == $dia) && ($mesActual == $mes)) { //En caso de no ser el mismo pasa de largo y va a la siguiente actividad

                    // Reviso si el horario que se desea cargar se encuentra entre el mismo horario de la actividad actual
                    if (($horarioFuncionEnMinutos >= $horaInicioActividad) && ($horarioFuncionEnMinutos <= $horaFinalizacion)) {
                        $existeHorarioActividad = true;
                    }
                }
            }
            if ($existeHorarioActividad) {
                echo "Ya hay una actividad en este horario, ingrese otro horario para esta actividad por favor" . "\n";
            } else {
                echo "El horario esta disponible. Horario seteado para la actividad: " . $nombreActividad . "\n";
            }
        }
    } while($existeHorarioActividad);

    $hora = array('hora' => $horaActividad, 'minutos' => $minutosActividad);

    // CREO EL OBJETO ACTIVIDAD Y LO INSERTO EN LA BASE DE DATOS
    $objActividad = new Actividad($idTeatro, $nombreActividad, $hora, $fecha, $duracionActividad, $precioActividad);

    echo "INSERTANDO ACTIVIDAD EN LA BASE DE DATOS..." . "\n";
    $respuestaInsercion = $objActividad->insertar();

    if ($respuestaInsercion) {
        echo "OPERACION DE INSERCION EXITOSA" . "\n";

        echo "EL ID DE LA NUEVA ACTIVIDAD ES: " . $objActividad->getIdActividad() . "\n";

        echo "-------------COLECCION ACTIVIDADES ACTIVAS-------------" . "\n";
        $colActividades = $objActividad->listar("");
        foreach ($colActividades as $unaActividad) {
            echo $unaActividad;
            echo "-------------------------------------------------------" . "\n";
        }
    } else {
        echo $objActividad->getmensajeoperacion() . "\n";
    }
    // PIDO EL TIPO DE ACTIVIDAD QUE SE ACABA DE CREAR PARA PODER INSERTARLA RESPECTIVAMENTE EN SU TABLA
    do {
        echo "---------------------------------------" . "\n";
        echo "ELIJA TIPO DE ACTIVIDAD QUE ACABA DE CARGAR: " . "\n";
        echo "1: OBRA DE TEATRO" . "\n";
        echo "2: CINE" . "\n";
        echo "3: MUSICAL" . "\n";
        echo "OPCION: ";
        $tipoActividad = trim(fgets(STDIN));
    } while (($tipoActividad > "3") && ($tipoActividad < "3"));
    echo "---------------------------------------" . "\n";

    switch ($tipoActividad) {
        case '1':
            $objObraTeatro = new ObraTeatro($objActividad->getIdActividad());
            echo "INSERTANDO OBRA DE TEATRO EN LA BASE DE DATOS..." . "\n";
            $respuestaInsercion = $objObraTeatro->insertar();
            if ($respuestaInsercion) {
                echo "OPERACION DE INSERCION EXITOSA" . "\n";

                echo "EL ID DE LA OBRA DE TEATRO ES: " . $objObraTeatro->getIdObraTeatro() . "\n";

                echo "-------------COLECCION OBRAS DE TEATRO ACTIVAS-------------" . "\n";
                $colObrasTeatro = $objObraTeatro->listar("");
                foreach ($colObrasTeatro as $unaObraTeatro) {
                    echo $unaObraTeatro;
                    echo "-------------------------------------------------------" . "\n";
                }
            } else {
                echo $objObraTeatro->getmensajeoperacion() . "\n";
            }
            break;
        case '2':
            // echo "Su opcion fue CINE." . "\n";
            echo "Ingrese el Genero de la pelicula: ";
            $generoCine = trim(fgets(STDIN));
            echo "Ingrese el Pais de Origen: ";
            $paisDeOrigen = trim(fgets(STDIN));

            $objCine = new Cine($objActividad->getIdActividad(), $generoCine, $paisDeOrigen);

            echo "INSERTANDO PELICULA EN LA BASE DE DATOS..." . "\n";
            $respuestaInsercion = $objCine->insertar();
            if ($respuestaInsercion) {
                echo "OPERACION DE INSERCION EXITOSA" . "\n";

                echo "EL ID DE LA PELICULA ES: " . $objCine->getIdCine() . "\n";

                echo "-------------COLECCION PELICULAS ACTIVAS-------------" . "\n";
                $colPeliculas = $objCine->listar("");
                foreach ($colPeliculas as $unaPelicula) {
                    echo $unaPelicula;
                    echo "-------------------------------------------------------" . "\n";
                }
            } else {
                echo $objCine->getmensajeoperacion() . "\n";
            }
            break;
        case '3':
            echo "Ingrese el Director del musical: ";
            $director = trim(fgets(STDIN));
            echo "Ingrese la Cantidad de Personas en Escena: ";
            $cantPersonasEnEscena = trim(fgets(STDIN));

            $objMusical = new Musical($objActividad->getIdActividad(), $director, $cantPersonasEnEscena);

            echo "INSERTANDO MUSICAL EN LA BASE DE DATOS..." . "\n";
            $respuestaInsercion = $objMusical->insertar();
            if ($respuestaInsercion) {
                echo "OPERACION DE INSERCION EXITOSA" . "\n";

                echo "EL ID DEL MUSICAL ES: " . $objMusical->getIdMusical() . "\n";

                echo "-------------COLECCION MUSICALES ACTIVOS-------------" . "\n";
                $colMusicales = $objMusical->listar("");
                foreach ($colMusicales as $unMusical) {
                    echo $unMusical;
                    echo "-------------------------------------------------------" . "\n";
                }
            } else {
                echo $objMusical->getmensajeoperacion() . "\n";
            }
            break;
    }
}

function datosModificarActividad($objActividad)
{
    // NOMBRE ACTIVIDAD
    echo "Desea modificar el nombre de la actividad? si/no: ";
    $respuestaNombre = trim(fgets(STDIN));
    if ($respuestaNombre == 'si') {
        echo "Nombre actual: " . $objActividad->getNombre() . "\n";
        echo "Ingrese el nuevo nombre: ";
        $nuevoNombreActividad = trim(fgets(STDIN));
    } else {
        $nuevoNombreActividad = $objActividad->getNombre();
    }
    // PRECIO ACTIVIDAD
    echo "Desea modificar el precio de la actividad? si/no: ";
    $respuestaPrecio = trim(fgets(STDIN));
    if ($respuestaPrecio == 'si') {
        echo "Precio actual: " . $objActividad->getPrecio() . "\n";
        echo "Ingrese el nuevo precio: ";
        $nuevoPrecioActividad = trim(fgets(STDIN));
    } else {
        $nuevoPrecioActividad = $objActividad->getPrecio();
    }
    $arrayInfoActividad = array('idTeatro'=> $objActividad->getIdTeatro(), 'nombre' => $nuevoNombreActividad, 'horaInicio' => $objActividad->getHoraInicio(), 'fecha' => $objActividad->getFecha(), 'duracion' => $objActividad->getDuracionActividad(), 'precio' => $nuevoPrecioActividad);
    return $arrayInfoActividad;
}

/**
 * PROGRAMA PRINCIPAL
 */

// creo un objeto TEATRO base para poder hacer las consultas bases BUSCAR y LISTAR
$objTeatroBase = new EdificioTeatro('', '', '');

// creo un objeto actividad base para poder hacer las consultas bases BUSCAR y LISTAR
$hora = array('hora' => '', 'minutos' => '');
$fecha = array('anio' => '', 'mes' => '', 'dia' => '');
$objActividadBase = new Actividad('', '', $hora, $fecha, '', ''); //idTeatro, nombre, hora, fecha, duracion, precio

do {
    echo "ELIJA UNA OPCION: " . "\n";
    echo "1: CREAR UN TEATRO" . "\n";
    echo "2: MODIFICAR INFORMACION DE UN TEATRO" . "\n";
    echo "3: VER INFORMACION COMPLETA DE UN TEATRO" . "\n";
    echo "4: ELIMINAR UN TEATRO" . "\n";
    echo "5: CREAR UNA FUNCION" . "\n";
    echo "6: MODIFICAR INFORMACION DE UNA FUNCION" . "\n";
    echo "7: VER FUNCIONES" . "\n";
    echo "8: ELIMINAR UNA FUNCION" . "\n";
    echo "9: VER COSTOS DE UTILIZACION: " . "\n";
    echo "10: SALIR" . "\n";
    echo "OPCION: ";
    $opcion = trim(fgets(STDIN));
    switch ($opcion) {
        case '1':
            crearTeatro();
        break;
        case '2':
            echo "--------COLECCION TEATROS--------" . "\n";
            $coleccionTeatros = $objTeatroBase->listar("");
            foreach ($coleccionTeatros as $unTeatro) {
                echo $unTeatro;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID del teatro que desea modificar: ";
            $idTeatroDeseado = trim(fgets(STDIN));
            $objTeatroModificar = new EdificioTeatro('', '', '');
            $respuestaBusqueda = $objTeatroModificar->Buscar($idTeatroDeseado);
            if ($respuestaBusqueda) {
                echo "--------TEATRO ENCONTRADO--------" . "\n";
                echo $objTeatroModificar;
                echo "---------------------------------" . "\n";

                $arrayInfo = datosModificarTeatro($objTeatroModificar);

                $objTeatroModificar->cambiarDatos($arrayInfo);

                $resultadoModificar = $objTeatroModificar->modificar($objTeatroModificar->getIdEdificioTeatro());

                if ($resultadoModificar) {
                    echo "OPERACION DE MODIFICACION EXITOSA" . "\n";
                    $colTeatros = $objTeatroBase->listar("");
                    foreach ($colTeatros as $unTeatro) {
                        echo $unTeatro;
                        echo "-------------------------------------------------------" . "\n";
                    }
                } else {
                    echo "ERROR EN LA MODIFICACION: " . $objTeatroModificar->getmensajeoperacion() . "\n";
                }
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objTeatroModificar->getmensajeoperacion() . "\n";
            }
        break;
        case '3':
            echo "-----COLECCION ID'S TEATROS-----" . "\n";
            $coleccionTeatros = $objTeatroBase->listar("");
            foreach ($coleccionTeatros as $unTeatro) {
                echo "ID Teatro: " . $unTeatro->getIdEdificioTeatro() . "\n";
                echo "--------------------------------" . "\n";
            }
            echo "Ingrese el ID del teatro que sea ver la informacion: ";
            $respuestaID = trim(fgets(STDIN));
            $objTeatroBusqueda = new EdificioTeatro('', '', '');
            $respuestaBusqueda = $objTeatroBusqueda->Buscar($respuestaID);
            if ($respuestaBusqueda) {
                echo "--------TEATRO ENCONTRADO--------" . "\n";
                echo $objTeatroBusqueda;
                echo "---------------------------------" . "\n";
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objTeatroBusqueda->getmensajeoperacion() . "\n";
            }
        break;
        case '4':
            echo "--------COLECCION TEATROS--------" . "\n";
            $coleccionTeatros = $objTeatroBase->listar("");
            foreach ($coleccionTeatros as $unTeatro) {
                echo $unTeatro;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID del teatro que desea eliminar: ";
            $idTeatroEliminar = trim(fgets(STDIN));
            $respuestaEliminacion = $objTeatroBase->eliminar($idTeatroEliminar);
            if ($respuestaEliminacion) {
                echo "OPERACION DE ELIMINACION EXITOSA" . "\n";
                $colTeatros = $objTeatroBase->listar("");

                foreach ($colTeatros as $unTeatro) {
                    echo $unTeatro;
                    echo "-------------------------------------------------------" . "\n";
                }
            } else {
                echo "ERROR EN LA ELIMINACION: " . $objTeatroBase->getmensajeoperacion() . "\n";
            }
        break;
        case '5':
            echo "--------COLECCION TEATROS--------" . "\n";
            $coleccionTeatros = $objTeatroBase->listar("");
            foreach ($coleccionTeatros as $unTeatro) {
                echo $unTeatro;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID del teatro al que se desea agregar la funcion: ";
            $respuestaIDTeatro = trim(fgets(STDIN));
            crearActividad($objActividadBase, $respuestaIDTeatro);
        break;
        case '6':
            echo "-------------COLECCION ACTIVIDADES ACTIVAS-------------" . "\n";
            $colActividades = $objActividadBase->listar("");
            foreach ($colActividades as $unaActividad) {
                echo $unaActividad;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID de la actividad a la que desea modificar sus datos: ";
            $idActividadModificar = trim(fgets(STDIN));

            $hora = array('hora' => '', 'minutos' => '');
            $fecha = array('anio' => '', 'mes' => '', 'dia' => '');
            $objActividadModificar = new Actividad('', '', $hora, $fecha, '', ''); //idTeatro, nombre, hora, fecha, duracion, precio

            $respuestaBusqueda = $objActividadModificar->Buscar($idActividadModificar);

            if ($respuestaBusqueda) {
                echo "--------ACTIVIDAD ENCONTRADA--------" . "\n";
                echo $objActividadModificar;
                echo "---------------------------------" . "\n";

                $arrayInfo = datosModificarActividad($objActividadModificar);

                $objActividadModificar->actualizarAtributos($arrayInfo);

                $resultadoModificar = $objActividadModificar->modificar($objActividadModificar->getIdActividad());

                if ($resultadoModificar) {
                    echo "OPERACION DE MODIFICACION EXITOSA" . "\n";
                    $colFunciones = $objActividadBase->listar("");
                    foreach ($colFunciones as $unaActividad) {
                        echo $unaActividad;
                        echo "-------------------------------------------------------" . "\n";
                    }
                } else {
                    echo "ERROR EN LA MODIFICACION: " . $objActividadModificar->getmensajeoperacion() . "\n";
                }
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objActividadModificar->getmensajeoperacion() . "\n";
            }
        break;
        case '7':
            $colActividades = $objActividadBase->listar("");
            echo "-------------COLECCION ACTIVIDADES ACTIVAS-------------" . "\n";
            foreach ($colActividades as $unaActividad) {
                echo $unaActividad;
                echo "-------------------------------------------------------" . "\n";
            }
        break;
        case '8':
            echo "-------------COLECCION ACTIVIDADES ACTIVAS-------------";
            $colActividades = $objActividadBase->listar("");
            foreach ($colActividades as $unaActividad) {
                echo $unaActividad;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID de la actividad que desea eliminar: ";
            $idActividadEliminar = trim(fgets(STDIN));
            $respuestaEliminacion = $objActividadBase->eliminar($idActividadEliminar);
            if ($respuestaEliminacion) {
                echo "OPERACION DE ELIMINACION EXITOSA" . "\n";
                $colActividades = $objActividadBase->listar("");

                foreach ($colActividades as $unaActividad) {
                    echo $unaActividad;
                    echo "-------------------------------------------------------" . "\n";
                }
            } else {
                echo "ERROR EN LA ELIMINACION: " . $objActividadBase->getmensajeoperacion() . "\n";
            }
        break;
        case '9':
            echo "--------COLECCION TEATROS--------" . "\n";
            $coleccionTeatros = $objTeatroBase->listar("");
            foreach ($coleccionTeatros as $unTeatro) {
                echo $unTeatro;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID del teatro al que se desea conocer los gastos mensuales: ";
            $respuestaIDTeatro = trim(fgets(STDIN));
            echo "Ingrese el mes del cual quiere conocer los costos: ";
            $mesDeseado = trim(fgets(STDIN));
            
            $costoFinal = $objTeatroBase->darCostos($mesDeseado, $respuestaIDTeatro);

            echo "EL COSTO FINAL DE MANTENIMIENTO PARA EL TEATRO " . $respuestaIDTeatro . " ES: $" . $costoFinal . "\n";
            break;
    }
} while ($opcion <> 10);

echo "PROGRAMA FINALIZADO" . "\n";
echo "GRACIAS POR UTILIZARLO. VUELVA PRONTOSSSS";
