<?php

class Cine{
    private $idCine;
    private $objActividad;
    private $genero;
    private $paisOrigen;
	private $mensajeoperacion;

    public function __construct($objActividad, $genero, $paisOrigen){
        $this->objActividad = $objActividad;
        $this->genero = $genero;
        $this->paisOrigen = $paisOrigen;
    }
    
    public function getIdCine()
    {
        return $this->idCine;
    }
    public function setIdCine($idCine)
    {
        $this->idCine = $idCine;
    }
    public function getObjActividad()
    {
        return $this->objActividad;
    }
    public function setObjActividad($objActividad)
    {
        $this->objActividad = $objActividad;
    }
    public function getGenero()
    {
        return $this->genero;
    }
    public function setGenero($genero)
    {
        $this->genero = $genero;
    }
    public function getPaisOrigen()
    {
        return $this->paisOrigen;
    }
    public function setPaisOrigen($paisOrigen)
    {
        $this->paisOrigen = $paisOrigen;
    }
    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}
    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;
	}

    // CONSULTAS PARA EL ORM
		
    public function Buscar($idCine){
		$base=new BaseDatos();
		$consultaActividad="Select * from cine where idCine=".$idCine;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
				    $this->setIdCine($row['idCine']);
				    $idActividad = ($row['idActividad']);
                    $this->setGenero($row['genero']);
                    $this->setPaisOrigen($row['paisOrigen']);

					$hora = array('hora' => '', 'minutos' => '');
					$fecha = array('anio' => '', 'mes' => '', 'dia' => '');
					$objActividad = new Actividad('', '', $hora, $fecha, '', '');

					$objActividad->Buscar($idActividad);
					$this->setObjActividad($objActividad);
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
    
	public function listar($condicion){
	    $arregloCine = null;
		$base=new BaseDatos();
		$consultaCine="SELECT * FROM actividad INNER JOIN cine ON actividad.idActividad=cine.idActividad ";
		if ($condicion!=""){
		    $consultaCine=$consultaCine.' where '.$condicion;
		}
		$consultaCine.=" order by actividad.fecha";
		//echo $consultaCine;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaCine)){				
				$arregloCine= array();
				while($row2=$base->Registro()){
                    $idCine = ($row2['idCine']);
                    $idActividad = ($row2['idActividad']);
                    $genero = ($row2['genero']);
                    $paisOrigen = ($row2['paisOrigen']);

					$hora = array('hora' => '', 'minutos' => '');
					$fecha = array('anio' => '', 'mes' => '', 'dia' => '');
					$objActividad = new Actividad('', '', $hora, $fecha, '', '');

					$objActividad->Buscar($idActividad);
				
					$objCine = new Cine($objActividad, $genero, $paisOrigen);
					$objCine->setIdCine($idCine);
					array_push($arregloCine,$objCine);
				}
		 	}else{
		 		$this->setmensajeoperacion($base->getError());
			}
		}else{
		 	$this->setmensajeoperacion($base->getError());
		}	
		return $arregloCine;
	}	

	public function insertar(){
		$base=new BaseDatos();
		$resp=false;
		$objActividad = $this->getObjActividad();
		$consultaInsertar="INSERT INTO cine(idActividad, genero, paisOrigen) VALUES (".$objActividad->getIdActividad().",'".$this->getGenero()."','".$this->getPaisOrigen()."')";
		
        if($base->Iniciar()){
            $id = $base->devuelveIDInsercion($consultaInsertar);
			if($id<>null){
                $this->setIdCine($id);
			    $resp=true;
			}else{
				$this->setmensajeoperacion($base->getError());	
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	public function modificar($idCine){
	    $resp =false; 
	    $base=new BaseDatos();
		$objActividad = $this->getObjActividad();
		$consultaModifica= "UPDATE cine SET idActividad=".$objActividad->getIdActividad().",genero='".$this->getGenero()."',paisOrigen='".$this->getPaisOrigen()."' WHERE idCine=".$idCine;
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
	
	public function eliminar($idCine){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM cine WHERE idCine=".$idCine;
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
        return  $this->getObjActividad() . "\n" . 
				"ID Cine: " . $this->getIdCine() . "\n" . 
                "Genero: " . $this->getGenero() . "\n" . 
                "Pais de Origen: " . $this->getPaisOrigen() . "\n"
                ;
    }
    
}