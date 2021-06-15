<?php

class Cine{
    private $idCine;
    private $idActividad;
    private $genero;
    private $paisOrigen;
	private $mensajeoperacion;

    public function __construct($idActividad, $genero, $paisOrigen){
        $this->idActividad = $idActividad;
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
    public function getIdActividad()
    {
        return $this->idActividad;
    }
    public function setIdActividad($idActividad)
    {
        $this->idActividad = $idActividad;
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
				    $this->setIdActividad($row['idActividad']);
                    $this->setGenero($row['genero']);
                    $this->setPaisOrigen($row['paisOrigen']);
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
		$consultaCine="Select * from cine ";
		if ($condicion!=""){
		    $consultaCine=$consultaCine.' where '.$condicion;
		}
		$consultaCine.=" order by genero";
		//echo $consultaCine;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaCine)){				
				$arregloCine= array();
				while($row2=$base->Registro()){
                    $idCine = ($row2['idCine']);
                    $idActividad = ($row2['idActividad']);
                    $genero = ($row2['genero']);
                    $paisOrigen = ($row2['paisOrigen']);
				
					$objCine = new Cine($idActividad, $genero, $paisOrigen);
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
		$consultaInsertar="INSERT INTO cine(idActividad, genero, paisOrigen) VALUES (".$this->getIdActividad().",'".$this->getGenero()."','".$this->getPaisOrigen()."')";
		
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
		$consultaModifica= "UPDATE cine SET idActividad=".$this->getIdActividad().",genero='".$this->getGenero()."',paisOrigen='".$this->getPaisOrigen()."' WHERE idCine=".$idCine;
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
        return  "ID Actividad: " . $this->getIdActividad() . "\n" . 
				"ID Cine: " . $this->getIdCine() . "\n" . 
                "Genero: " . $this->getGenero() . "\n" . 
                "Pais de Origen: " . $this->getPaisOrigen() . "\n"
                ;
    }
    
}