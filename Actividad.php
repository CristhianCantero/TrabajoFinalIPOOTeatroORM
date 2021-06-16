<?php
include_once 'BaseDeDatos.php';

class Actividad{
    private $teatro;
    private $idActividad;
    private $nombre;
    private $horaInicio = array();
    private $fecha = array();
    private $duracionActividad;
    private $precio;
    private $mensajeoperacion;

    public function __construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio)
    {
        $this->teatro = $teatro;
        $this->nombre = $nombre;
        $this->horaInicio = array(
            "hora"=> $horaInicio['hora'],
            "minutos"=> $horaInicio['minutos']
        );
        $this->fecha = array(
            "anio"=> $fecha['anio'],
            "mes"=> $fecha['mes'],
            "dia"=> $fecha['dia']
        );
        $this->duracionActividad = $duracionActividad;
        $this->precio = $precio;
    }

    public function getNombre(){
        return $this->nombre;
    }
    public function getHoraInicio(){
        return $this->horaInicio;
    }
    public function getDuracionActividad(){
        return $this->duracionActividad;
    }
    public function getPrecio(){
        return $this->precio;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function getIdActividad()
    {
        return $this->idActividad;
    }
    public function getmensajeoperacion(){
		return $this->mensajeoperacion;
	}

    public function setNombre($nom){
        $this->nombre=$nom;
    }
    public function setHoraInicio($horaIni){
        $this->horaInicio=$horaIni;
    }
    public function setDuracionActividad($duraObra){
        $this->duracionActividad=$duraObra;
    }
    public function setPrecio($pre){
        $this->precio=$pre;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function setIdActividad($idActividad)
    {
        $this->idActividad = $idActividad;
    }
    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;
	}

    public function getTeatro()
    {
        return $this->teatro;
    }
    public function setTeatro($teatro)
    {
        $this->teatro = $teatro;
    }

    public function darCostos(){
        $precio = $this->getPrecio();

        return $precio;
    }

    public function actualizarAtributos($arrayAtributos){
        $this->setTeatro($arrayAtributos['teatro']);
        $this->setNombre($arrayAtributos['nombre']);
        $this->setHoraInicio($arrayAtributos['horaInicio']);
        $this->setFecha($arrayAtributos['fecha']);
        $this->setDuracionActividad($arrayAtributos['duracion']);
        $this->setPrecio($arrayAtributos['precio']);
    }
    // Funcion para transformar un string en array de horaInicio
    public function transformarHoraInicioArray($horaInicioBD){
        $hora = explode(':', $horaInicioBD);
        $horaInicio = array(
            "hora"=>$hora[0],
            "minutos"=>$hora[1]
        );
        return $horaInicio;
    }
    // Funcion para transformar un string en array de fecha
    public function transformarFechaArray($fechaBD){
        $fechaExplode = explode('-', $fechaBD);
        $fecha = array(
            "anio"=>$fechaExplode[0],
            "mes"=>$fechaExplode[1],
            "dia"=>$fechaExplode[2]
        );
        return $fecha;
    }
    // Funcion para transformar un array en string horaInicio
    public function transformarHoraString(){
        $horaArray = $this->getHoraInicio();
        $horaString = $horaArray['hora'] . ":" . $horaArray['minutos'];

        return $horaString;
    }
    // Funcion para transformar un array en string fecha
    public function transformarFechaString(){
        $fechaArray = $this->getFecha();
        $fechaString = $fechaArray['anio'] . "-" . $fechaArray['mes'] . "-" . $fechaArray['dia'];

        return $fechaString;
    }

    // CONSULTAS PARA EL ORM
    
    // RECUPERA UNA ACTIVIDAD POR IDACTIVIDAD
    public function Buscar($idActividad){
		$base=new BaseDatos();
		$consultaActividad="Select * from actividad where idActividad=".$idActividad;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
                    $idTeatro = ($row['idTeatro']);
				    $this->setIdActividad($row['idActividad']);
				    $this->setNombre($row['nombre']);
                    $this->setDuracionActividad($row['duracionActividad']);
                    $this->setPrecio($row['precio']);
                    $horaInicioBD = $row['horaInicio'];
                    $fechaBD = $row['fecha'];
                    $horaInicioArray = $this->transformarHoraInicioArray($horaInicioBD);
                    $fechaArray = $this->transformarFechaArray($fechaBD);
                    $this->setHoraInicio($horaInicioArray);
                    $this->setFecha($fechaArray);

                    $teatro = new EdificioTeatro("", "", "");
                    $teatro->Buscar($idTeatro);

                    $this->setTeatro($teatro);
					$resp= true;
				}
		 	}else{
		 		$this->setmensajeoperacion($base->getError());
			}
		}else{
		 	$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}	
    
    // LISTA LAS ACTIVIDADES
	public function listar($condicion){
	    $arregloActividad = null;
		$base=new BaseDatos();
		$consultaActividad="Select * from actividad ";
		if ($condicion!=""){
		    $consultaActividad=$consultaActividad.' where '.$condicion;
		}
		$consultaActividad.=" order by fecha";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){				
				$arregloActividad= array();
				while($row2=$base->Registro()){
                    $idTeatro = ($row2['idTeatro']);
                    $actividad = ($row2['idActividad']);
				    $nombre = ($row2['nombre']);
                    $duracionActiv = ($row2['duracionActividad']);
                    $precio = ($row2['precio']);
                    $horaInicioArray = $this->transformarHoraInicioArray($row2['horaInicio']);
                    $fechaArray = $this->transformarFechaArray($row2['fecha']);

                    $teatro = new EdificioTeatro("", "", "");

                    $teatro->Buscar($idTeatro);
                    
					$objActividad = new Actividad($teatro, $nombre, $horaInicioArray, $fechaArray, $duracionActiv, $precio);
					$objActividad->setIdActividad($actividad);
					array_push($arregloActividad,$objActividad);
				}
		 	}else{
		 		$this->setmensajeoperacion($base->getError());
			}
		}else{
		 	$this->setmensajeoperacion($base->getError());
		}	
		return $arregloActividad;
	}	
    // INSERTA UNA NUEVA ACTIVIDAD EN LA BASE DE DATOS
	public function insertar(){
		$base=new BaseDatos();
		$resp=false;
        $horaString = $this->transformarHoraString();
        $fechaString = $this->transformarFechaString();
        $objTeatro = $this->getTeatro();
		$consultaInsertar="INSERT INTO actividad(idTeatro, nombre, horaInicio, fecha, duracionActividad, precio) VALUES (".$objTeatro->getIdEdificioTeatro().",'".$this->getNombre()."','".$horaString."','".$fechaString."',".$this->getDuracionActividad().",".$this->getPrecio().")";
		
        if($base->Iniciar()){
            $id = $base->devuelveIDInsercion($consultaInsertar);
			if($id<>null){
                $this->setIdActividad($id);
			    $resp=true;
			}else{
				$this->setmensajeoperacion($base->getError());	
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	// MODIFICAR UNA ACTIVIDAD DEPENDIENDO DE LA IDACTIV
	public function modificar($idActiv){
	    $resp =false; 
	    $base=new BaseDatos();
        $horaString = $this->transformarHoraString();
        $fechaString = $this->transformarFechaString();
        $objTeatro = $this->getTeatro();
		$consultaModifica="UPDATE actividad SET idTeatro=".$objTeatro->getIdEdificioTeatro().",nombre='".$this->getNombre()."',horaInicio='".$horaString."',fecha='".$fechaString."',duracionActividad=". $this->getDuracionActividad().",precio=". $this->getPrecio()." WHERE idActividad=".$idActiv;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	// ELIMINAR LA ACTIVIDAD MANDADA POR IDACTIVIDAD
	public function eliminar($idActividad){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM actividad WHERE idActividad=".$idActividad;
				if($base->Ejecutar($consultaBorra)){
				    $resp=  true;
				}else{
					$this->setmensajeoperacion($base->getError());
				}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp; 
	}

    public function __toString()
    {  
        $horario = $this->getHoraInicio();
        $fecha = $this->getFecha();
        return  $this->getTeatro() . "\n" .
                "ID Actividad: " . $this->getIdActividad() . "\n" . 
                "Nombre: " . $this->getNombre() . "\n" .
                "Precio: " . $this->getPrecio() . "\n" .
                "Fecha: " . $fecha['dia'] . "-" . $fecha["mes"] . "-" . $fecha["anio"] . "\n" .
                "Hora de inicio: " . $horario['hora'] . ":" . $horario['minutos'] . "\n" .
                "Duracion: " . $this->getDuracionActividad() . " minutos" . "\n"
                ;
    }

    
}




