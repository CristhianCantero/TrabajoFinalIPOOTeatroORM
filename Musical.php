<?php

class Musical{
    private $idMusical;
    private $objActividad;
    private $director;
    private $cantidadPersonasEscena;
	private $mensajeoperacion;

    public function __construct($objActividad, $director, $cantidadPersonasEscena){
        $this->objActividad = $objActividad;
        $this->director = $director;
        $this->cantidadPersonasEscena = $cantidadPersonasEscena;
    }
 
    public function getIdMusical()
    {
        return $this->idMusical;
    }
    public function setIdMusical($idMusical)
    {
        $this->idMusical = $idMusical;
    }
    public function getObjActividad()
    {
        return $this->objActividad;
    }
    public function setObjActividad($objActividad)
    {
        $this->objActividad = $objActividad;
    }
    public function getDirector()
    {
        return $this->director;
    }
    public function setDirector($director)
    {
        $this->director = $director;
    }
    public function getCantidadPersonasEscena()
    {
        return $this->cantidadPersonasEscena;
    }
    public function setCantidadPersonasEscena($cantidadPersonasEscena)
    {
        $this->cantidadPersonasEscena = $cantidadPersonasEscena;
    }
	public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}
    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;
	}

    // CONSULTAS PARA EL ORM

    /**
	 * @param int $idMusical
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($idMusical){
		$base=new BaseDatos();
		$consultaActividad="Select * from musical where idMusical=".$idMusical;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
				    $this->setIdMusical($row['idMusical']);
				    $idActividad = ($row['idActividad']);
                    $this->setDirector($row['director']);
                    $this->setCantidadPersonasEscena($row['cantidadPersonasEscena']);
					
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
	    $arregloMusical = null;
		$base=new BaseDatos();
		$consultaMusical="Select * from musical ";
		if ($condicion!=""){
		    $consultaMusical=$consultaMusical.' where '.$condicion;
		}
		$consultaMusical.=" order by director";
		//echo $consultaMusical;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaMusical)){				
				$arregloMusical= array();
				while($row2=$base->Registro()){
                    $idMusical = ($row2['idMusical']);
                    $idActividad = ($row2['idActividad']);
                    $director = ($row2['director']);
                    $cantidadPersonasEscena = ($row2['cantidadPersonasEscena']);
					
					$hora = array('hora' => '', 'minutos' => '');
					$fecha = array('anio' => '', 'mes' => '', 'dia' => '');
					$objActividad = new Actividad('', '', $hora, $fecha, '', '');

					$objActividad->Buscar($idActividad);

					$objMusical = new Musical($objActividad, $director, $cantidadPersonasEscena);
					$objMusical->setIdMusical($idMusical);
					array_push($arregloMusical,$objMusical);
				}
		 	}else{
		 		$this->setmensajeoperacion($base->getError());
			}
		}else{
		 	$this->setmensajeoperacion($base->getError());
		}	
		return $arregloMusical;
	}	

	public function insertar(){
		$base=new BaseDatos();
		$resp=false;
		$objActividad = $this->getObjActividad();
		$consultaInsertar="INSERT INTO musical(idActividad, director, cantidadPersonasEscena) VALUES (".$objActividad->getIdActividad().",'".$this->getDirector()."',".$this->getCantidadPersonasEscena().")";
		
        if($base->Iniciar()){
            $id = $base->devuelveIDInsercion($consultaInsertar);
			if($id<>null){
                $this->setIdMusical($id);
			    $resp=true;
			}else{
				$this->setmensajeoperacion($base->getError());	
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	public function modificar($idMusical){
	    $resp =false; 
	    $base=new BaseDatos();
		$objActividad = $this->getObjActividad();
		$consultaModifica= "UPDATE musical SET idActividad=".$objActividad->getIdActividad().",director='".$this->getDirector()."',cantidadPersonasEscena=".$this->getCantidadPersonasEscena()." WHERE idMusical=".$idMusical;
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
	
	public function eliminar($idMusical){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM musical WHERE idMusical=".$idMusical;
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
				"ID Musical: " . $this->getIdMusical() . "\n" . 
                "Director del Musical: " . $this->getDirector() . "\n" . 
                "Cantidad de personas en Escena: " . $this->getCantidadPersonasEscena() . "\n"
                ;
    }

    

    
}
