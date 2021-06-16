<?php
include_once 'Actividad.php';
class ObraTeatro extends Actividad{
	private $mensajeoperacion;

    public function __construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio){
		parent::__construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio);
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
    public function Buscar($idActividad){
		$base=new BaseDatos();
		$consultaActividad="Select * from obrateatro where idActividad=".$idActividad;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
				    $idActiv = ($row['idActividad']);

					parent::Buscar($idActiv);
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
                    $idActividad = ($row2['idActividad']);

					$objObraMusical = new ObraTeatro("", "", "", "", "", "", "", "");
					$objObraMusical->Buscar($idActividad);
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
		if(parent::insertar()){
			$idActividad = parent::getIdActividad();
			$consultaInsertar="INSERT INTO obrateatro(idActividad) VALUES (".$idActividad.")";
			if($base->Iniciar()){
				if($base->Ejecutar($consultaInsertar)){
					$resp=true;
				}else{
					$this->setmensajeoperacion($base->getError());	
				}
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}
		
		return $resp;
	}
	
	public function modificar(){
	    $resp =false; 
	    $base=new BaseDatos();
		if(parent::modificar()){
			$idActividad = parent::getIdActividad();
			$consultaModifica= "UPDATE obrateatro SET idActividad=".$idActividad." WHERE idActividad=".$idActividad;
			if($base->Iniciar()){
				if($base->Ejecutar($consultaModifica)){
					$resp=  true;
				}else{
					$this->setmensajeoperacion($base->getError());
				}
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}
		return $resp;
	}
	
	public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
			$idActividad = parent::getIdActividad();
			$consultaBorra="DELETE FROM obramusical WHERE idActividad=".$idActividad;
			if($base->Ejecutar($consultaBorra)){
				if(parent::eliminar()){
					$resp=  true;
				}
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
        return  parent::__toString() . "\n";
    }

    
    
}
