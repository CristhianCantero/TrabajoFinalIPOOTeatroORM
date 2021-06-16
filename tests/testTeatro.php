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

function crearActividad($objActividadBase, $objTeatro)
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
                $teatroActual = $actividad->getTeatro();
                $idTeatroActual = $teatroActual->getIdEdificioTeatro();
                // Desgloso la hora y fecha de la actividad para utilizarlas en la corroboracion de existencia mas abajo
                $horaInicio = $horaInicioArray['hora'];
                $minutoInicio = $horaInicioArray['minutos'];
                $diaActual = $fechaActualActividad['dia'];
                $mesActual = $fechaActualActividad['mes'];
                // Inicializo el horario de inicio y finalizacion de la actividad actual
                $horaInicioActividad = ($horaInicio * 60) + $minutoInicio;
                $horaFinalizacion = $horaInicioActividad + $duracionActualActividad;
                $idTeatro = $objTeatro->getIdEdificioTeatro();
                // Si el id del teatro actual es igual al id del teatro en el que se desea cargar la funcion entro
                if($idTeatroActual == $idTeatro){
                    // Reviso si la actividad que se desea cargar es el mismo dia de la actividad actual
                    if (($diaActual == $dia) && ($mesActual == $mes)) { //En caso de no ser el mismo pasa de largo y va a la siguiente actividad
                        // Reviso si el horario que se desea cargar se encuentra entre el mismo horario de la actividad actual
                        if (($horarioFuncionEnMinutos >= $horaInicioActividad) && ($horarioFuncionEnMinutos <= $horaFinalizacion)) {
                            $existeHorarioActividad = true;
                        }
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

    // NO CREO LA FUNCION PRIMERO, A PARTIR DE LAS FUNCIONES HIJAS CREO LA FUNCION PADRE 
    // CREO EL OBJETO ACTIVIDAD Y LO INSERTO EN LA BASE DE DATOS
    // $objActividad = new Actividad($objTeatro, $nombreActividad, $hora, $fecha, $duracionActividad, $precioActividad);
    
    // echo "INSERTANDO ACTIVIDAD EN LA BASE DE DATOS..." . "\n";
    // $respuestaInsercion = $objActividad->insertar();
    
    // if ($respuestaInsercion) {
        //     echo "OPERACION DE INSERCION EXITOSA" . "\n";
        
        //     echo "EL ID DE LA NUEVA ACTIVIDAD ES: " . $objActividad->getIdActividad() . "\n";
        
        //     echo "-------------COLECCION ACTIVIDADES ACTIVAS-------------" . "\n";
        //     $colActividades = $objActividad->listar("");
        //     foreach ($colActividades as $unaActividad) {
            //         echo $unaActividad;
            //         echo "-------------------------------------------------------" . "\n";
            //     }
            // } else {
                //     echo $objActividad->getmensajeoperacion() . "\n";
                // }
                // PIDO EL TIPO DE ACTIVIDAD QUE SE ACABA DE CREAR PARA PODER INSERTARLA RESPECTIVAMENTE EN SU TABLA
    $hora = array('hora' => $horaActividad, 'minutos' => $minutosActividad);
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
            $objObraTeatro = new ObraTeatro($objTeatro, $nombreActividad, $hora, $fecha, $duracionActividad, $precioActividad);
            echo "INSERTANDO OBRA DE TEATRO EN LA BASE DE DATOS..." . "\n";
            $respuestaInsercion = $objObraTeatro->insertar();
            if ($respuestaInsercion) {
                echo "OPERACION DE INSERCION EXITOSA" . "\n";

                echo "EL ID DE LA OBRA DE TEATRO ES: " . $objObraTeatro->getIdActividad() . "\n";

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

            $objCine = new Cine($objTeatro, $nombreActividad, $hora, $fecha, $duracionActividad, $precioActividad, $generoCine, $paisDeOrigen);

            echo "INSERTANDO PELICULA EN LA BASE DE DATOS..." . "\n";
            $respuestaInsercion = $objCine->insertar();
            if ($respuestaInsercion) {
                echo "OPERACION DE INSERCION EXITOSA" . "\n";

                echo "EL ID DE LA PELICULA ES: " . $objCine->getIdActividad() ."\n";

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

            $objMusical = new Musical($objTeatro, $nombreActividad, $hora, $fecha, $duracionActividad, $precioActividad, $director, $cantPersonasEnEscena);

            echo "INSERTANDO MUSICAL EN LA BASE DE DATOS..." . "\n";
            $respuestaInsercion = $objMusical->insertar();
            if ($respuestaInsercion) {
                echo "OPERACION DE INSERCION EXITOSA" . "\n";

                echo "EL ID DEL MUSICAL ES: " . $objMusical->getIdActividad() . "\n";

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
    $objetoTeatro = $objActividad->getTeatro();
    $arrayInfoActividad = array('teatro'=> $objetoTeatro, 'nombre' => $nuevoNombreActividad, 'horaInicio' => $objActividad->getHoraInicio(), 'fecha' => $objActividad->getFecha(), 'duracion' => $objActividad->getDuracionActividad(), 'precio' => $nuevoPrecioActividad);
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

$objCineBase = new Cine("", "", $hora, $fecha, "", "", "", "");
$objMusicalBase = new Musical("", "", $hora, $fecha, "", "", "", "");
$objObraTeatroBase = new ObraTeatro("", "", $hora, $fecha, "", "", "", "");

do {
    echo "---------------------MENU---------------------" . "\n";
    echo "ELIJA UNA OPCION: " . "\n";
    echo "1: CREAR UN TEATRO" . "\n";
    echo "2: MODIFICAR INFORMACION DE UN TEATRO" . "\n";
    echo "3: VER INFORMACION COMPLETA DE UN TEATRO" . "\n";
    echo "4: ELIMINAR UN TEATRO" . "\n";
    echo "5: CREAR UNA FUNCION" . "\n";
    echo "6: MODIFICAR INFORMACION DE UNA FUNCION" . "\n";
    echo "7: VER FUNCIONES GENERALES" . "\n";
    echo "8: VER PELICULAS" . "\n";
    echo "9: VER MUSICALES" . "\n";
    echo "10: VER OBRAS DE TEATRO" . "\n";
    echo "11: ELIMINAR UNA FUNCION" . "\n";
    echo "12: VER COSTOS DE UTILIZACION: " . "\n";
    echo "13: SALIR" . "\n";
    echo "OPCION: ";
    $opcion = trim(fgets(STDIN));
    echo "------------------------------------------" . "\n";
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

                $resultadoModificar = $objTeatroModificar->modificar();

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

            $objTeatroEliminar = new EdificioTeatro('', '', '');

            $respuestaBusqueda = $objTeatroEliminar->Buscar($idTeatroEliminar);
            
            if ($respuestaBusqueda) {
                echo "--------TEATRO ENCONTRADO--------" . "\n";
                echo $objTeatroEliminar;
                echo "---------------------------------" . "\n";
                
                $respuestaEliminacion = $objTeatroEliminar->eliminar();
                if($respuestaEliminacion) {
                    echo "OPERACION DE ELIMINACION EXITOSA" . "\n";
                    $colTeatros = $objActividadBase->listar("");
    
                    foreach ($colTeatros as $unTeatro) {
                        echo $unTeatro;
                        echo "-------------------------------------------------------" . "\n";
                    }
                }else{
                    echo "ERROR EN LA ELIMINACION: " . $objActividadBase->getmensajeoperacion() . "\n";
                }
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objTeatroBusqueda->getmensajeoperacion() . "\n";
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
            $objTeatroBusqueda = new EdificioTeatro('', '', '');
            $respuestaBusqueda = $objTeatroBusqueda->Buscar($respuestaIDTeatro);
            if ($respuestaBusqueda) {
                echo "--------TEATRO ENCONTRADO--------" . "\n";
                echo $objTeatroBusqueda;
                echo "---------------------------------" . "\n";
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objTeatroBusqueda->getmensajeoperacion() . "\n";
            }
            crearActividad($objActividadBase, $objTeatroBusqueda);
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

                $resultadoModificar = $objActividadModificar->modificar();

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
            echo "--------COLECCION TEATROS--------" . "\n";
            $coleccionTeatros = $objTeatroBase->listar("");
            foreach ($coleccionTeatros as $unTeatro) {
                echo $unTeatro;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID del teatro del que desea ver las funciones: ";
            $respuestaIDTeatro = trim(fgets(STDIN));

            $objTeatroBusqueda = new EdificioTeatro('', '', '');
            $respuestaBusqueda = $objTeatroBusqueda->Buscar($respuestaIDTeatro);
            if ($respuestaBusqueda) {
                echo "--------TEATRO ENCONTRADO--------" . "\n";
                echo $objTeatroBusqueda;
                echo "---------------------------------" . "\n";
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objTeatroBusqueda->getmensajeoperacion() . "\n";
            }
            $coleccionActividadades = $objTeatroBusqueda->getColeccionFunciones();
            echo "------COLECCION ACTIVIDADES ACTIVAS PARA EL TEATRO " . strtoupper($objTeatroBusqueda->getNombre()) . "------" . "\n";
            foreach ($coleccionActividadades as $unaActividad) {
                echo $unaActividad;
                echo "-------------------------------------------------------" . "\n";
            }
        break;
        case '8':
            $colPelicula = $objCineBase->listar("");
            if(count($colPelicula)>0){
                echo "-------------COLECCION PELICULAS ACTIVAS-------------" . "\n";
                foreach ($colPelicula as $unaPelicula) {
                    echo $unaPelicula;
                    echo "-------------------------------------------------------" . "\n";
                }
            }else{
                echo "NO HAY PELICULAS ACTIVAS \n";
            }
        break;
        case '9':
            $colMusicales = $objMusicalBase->listar("");
            if(count($colMusicales)>0){
                echo "-------------COLECCION MUSICALES ACTIVAS-------------" . "\n";
                foreach ($colMusicales as $unMusical) {
                    echo $unMusical;
                    echo "-------------------------------------------------------" . "\n";
                }
            }else{
                echo "NO HAY MUSICALES ACTIVOS \n";
            }
        break;
        case '10':
            $colObrasTeatro = $objObraTeatroBase->listar("");
            if(count($colObrasTeatro)>0){
                echo "-------------COLECCION OBRAS DE TEATRO ACTIVAS-------------" . "\n";
                foreach ($colObrasTeatro as $unaObraTeatro) {
                    echo $unaObraTeatro;
                    echo "-------------------------------------------------------" . "\n";
                }
            }else{
                echo "NO HAY OBRAS DE TEATRO ACTIVAS \n";
            }
            
        break;
        case '11':
            echo "-------------COLECCION ACTIVIDADES ACTIVAS-------------" . "\n";
            $colActividades = $objActividadBase->listar("");
            foreach ($colActividades as $unaActividad) {
                echo $unaActividad;
                echo "-------------------------------------------------------" . "\n";
            }
            echo "Ingrese el ID de la actividad que desea eliminar: ";
            $idActividadEliminar = trim(fgets(STDIN));
            $hora = array('hora' => '', 'minutos' => '');
            $fecha = array('anio' => '', 'mes' => '', 'dia' => '');
            $objActividadEliminar = new Actividad('', '', $hora, $fecha, '', ''); //idTeatro, nombre, hora, fecha, duracion, precio

            $respuestaBusqueda = $objActividadEliminar->Buscar($idActividadEliminar);
            
            if ($respuestaBusqueda) {
                echo "--------ACTIVIDAD ENCONTRADA--------" . "\n";
                echo $objActividadEliminar;
                echo "---------------------------------" . "\n";
                
                $respuestaEliminacion = $objActividadEliminar->eliminar();
                if($respuestaEliminacion) {
                    echo "OPERACION DE ELIMINACION EXITOSA" . "\n";
                    $colActividades = $objActividadBase->listar("");
    
                    foreach ($colActividades as $unaActividad) {
                        echo $unaActividad;
                        echo "-------------------------------------------------------" . "\n";
                    }
                }else{
                    echo "ERROR EN LA ELIMINACION: " . $objActividadBase->getmensajeoperacion() . "\n";
                }
            } else {
                echo "ERROR EN LA BUSQUEDA: " . $objTeatroBusqueda->getmensajeoperacion() . "\n";
            }
        break;
        case '12':
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

            echo "EL COSTO FINAL DE MANTENIMIENTO PARA EL TEATRO " . $respuestaIDTeatro . " EN EL MES " . $mesDeseado . " ES: $" . $costoFinal . "\n";
            break;
    }
} while ($opcion <> 13);

echo "PROGRAMA FINALIZADO" . "\n";
echo "GRACIAS POR UTILIZARLO. VUELVA PRONTOSSSS";
