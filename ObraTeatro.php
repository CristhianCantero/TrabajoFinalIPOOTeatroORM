<?php

class ObraTeatro{
    private $idObraTeatro;
    private $objActividad;
	private $mensajeoperacion;

    public function __construct($objActividad){
        
        $this->objActividad = $objActividad;
    }

    public function getIdObraTeatro()
    {
        return $this->idObraTeatro;
    }
    public function setIdObraTeatro($idObraTeatro)
    {
        $this->idObraTeatro = $idObraTeatro;
    }
    public function getObjActividad()
    {
        return $this->objActividad;
    }
    public function setObjActividad($objActividad)
    {
        $this->objActividad = $objActividad;
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
				    $idActividad = ($row['idActividad']);

					$hora = array('hora' => '', 'minutos' => '');
					$fecha = array('anio' => '', 'mes' => '', 'dia' => '');
					$objActividad = new Actividad('', '', $hora, $fecha, '', '');

					$objActividad->Buscar($idActividad);
					$this->setObjActividad($objActividad);
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
		$consultaMusical="SELECT * FROM actividad INNER JOIN obrateatro ON actividad.idActividad=obrateatro.idActividad ";
		if ($condicion!=""){
		    $consultaMusical=$consultaMusical.' where '.$condicion;
		}
		$consultaMusical.=" order by actividad.fecha";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaMusical)){				
				$arregloObraMusical= array();
				while($row2=$base->Registro()){
                    $idObraTeatro = ($row2['idObraTeatro']);
                    $idActividad = ($row2['idActividad']);

					$hora = array('hora' => '', 'minutos' => '');
					$fecha = array('anio' => '', 'mes' => '', 'dia' => '');
					$objActividad = new Actividad('', '', $hora, $fecha, '', '');

					$objActividad->Buscar($idActividad);
				
					$objObraMusical = new ObraTeatro($objActividad);
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
		$objActividad = $this->getObjActividad();
		$consultaInsertar="INSERT INTO obrateatro(idActividad) VALUES (".$objActividad->getIdActividad().")";
		
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
		$objActividad = $this->getObjActividad();
		$consultaModifica= "UPDATE obrateatro SET idActividad=".$objActividad->getIdActividad()." WHERE idObraTeatro=".$idObraTeatro;
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
        return  $this->getObjActividad() . "\n" .
				"ID Obra Teatro: " . $this->getIdObraTeatro() . "\n"
				;
    }

    
    
}
