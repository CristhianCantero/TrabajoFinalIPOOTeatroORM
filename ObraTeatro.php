<?php

class ObraTeatro{
    private $idObraTeatro;
    private $idActividad;
	private $mensajeoperacion;

    public function __construct($idActividad){
        
        $this->idActividad = $idActividad;
    }

    public function getIdObraTeatro()
    {
        return $this->idObraTeatro;
    }
    public function setIdObraTeatro($idObraTeatro)
    {
        $this->idObraTeatro = $idObraTeatro;
    }
    public function getIdActividad()
    {
        return $this->idActividad;
    }
    public function setIdActividad($idActividad)
    {
        $this->idActividad = $idActividad;
    }
    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}
    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;
	}

    // CONSULTAS PARA EL ORM

    /**
	 * @param int $idObraTeatro
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($idObraTeatro){
		$base=new BaseDatos();
		$consultaActividad="Select * from obrateatro where idObraTeatro=".$idObraTeatro;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
				    $this->setIdObraTeatro($row['idObraTeatro']);
				    $this->setIdActividad($row['idActividad']);
					$resp=true;
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
	    $arregloObraMusical = null;
		$base=new BaseDatos();
		$consultaMusical="Select * from obrateatro ";
		if ($condicion!=""){
		    $consultaMusical=$consultaMusical.' where '.$condicion;
		}
		if($base->Iniciar()){
			if($base->Ejecutar($consultaMusical)){				
				$arregloObraMusical= array();
				while($row2=$base->Registro()){
                    $idObraTeatro = ($row2['idObraTeatro']);
                    $idActividad = ($row2['idActividad']);
				
					$objObraMusical = new ObraTeatro($idActividad);
					$objObraMusical->setIdObraTeatro($idObraTeatro);
					array_push($arregloObraMusical,$objObraMusical);
				}
		 	}else{
		 		$this->setmensajeoperacion($base->getError());
			}
		}else{
		 	$this->setmensajeoperacion($base->getError());
		}	
		return $arregloObraMusical;
	}	

	public function insertar(){
		$base=new BaseDatos();
		$resp=false;
		$consultaInsertar="INSERT INTO obrateatro(idActividad) VALUES (".$this->getIdActividad().")";
		
        if($base->Iniciar()){
            $id = $base->devuelveIDInsercion($consultaInsertar);
			if($id<>null){
                $this->setIdObraTeatro($id);
			    $resp=true;
			}else{
				$this->setmensajeoperacion($base->getError());	
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	public function modificar($idObraTeatro){
	    $resp =false; 
	    $base=new BaseDatos();
		$consultaModifica= "UPDATE obrateatro SET idActividad=".$this->getIdActividad()." WHERE idObraTeatro=".$idObraTeatro;
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
	
	public function eliminar($idObraTeatro){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM obramusical WHERE idObraTeatro=".$idObraTeatro;
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
				"ID Obra Teatro: " . $this->getIdObraTeatro() . "\n"
				;
    }

    
    
}
