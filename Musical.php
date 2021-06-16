<?php
include_once "Actividad.php";

class Musical extends Actividad{
    private $director;
    private $cantidadPersonasEscena;
	private $mensajeoperacion;

    public function __construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio, $director, $cantidadPersonasEscena){
        parent::__construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio);
        $this->director = $director;
        $this->cantidadPersonasEscena = $cantidadPersonasEscena;
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
    public function Buscar($idActividad){
		$base=new BaseDatos();
		$consultaActividad="Select * from musical where idActividad=".$idActividad;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
				    $idActiv = ($row['idActividad']);
                    $this->setDirector($row['director']);
                    $this->setCantidadPersonasEscena($row['cantidadPersonasEscena']);

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
	    $arregloMusical = null;
		$base=new BaseDatos();
		$consultaMusical="SELECT * FROM actividad INNER JOIN musical ON actividad.idActividad=musical.idActividad ";
		if ($condicion!=""){
		    $consultaMusical=$consultaMusical.' where '.$condicion;
		}
		$consultaMusical.=" order by actividad.fecha";
		//echo $consultaMusical;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaMusical)){				
				$arregloMusical= array();
				while($row2=$base->Registro()){
                    $idActividad = ($row2['idActividad']);

					$objMusical = new Musical("", "", "", "", "", "", "", "");
					$objMusical->Buscar($idActividad);
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
		if(parent::insertar()){
			$idActividad = parent::getIdActividad();
			$consultaInsertar="INSERT INTO musical(idActividad, director, cantidadPersonasEscena) VALUES (".$idActividad.",'".$this->getDirector()."',".$this->getCantidadPersonasEscena().")";
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
			$consultaModifica= "UPDATE musical SET director='".$this->getDirector()."',cantidadPersonasEscena=".$this->getCantidadPersonasEscena()." WHERE idActividad=".$idActividad;
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
			$consultaBorra="DELETE FROM musical WHERE idActividad=".$idActividad;
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
        return  parent::__toString() . "\n" .
                "Director del Musical: " . $this->getDirector() . "\n" . 
                "Cantidad de personas en Escena: " . $this->getCantidadPersonasEscena() . "\n"
                ;
    }

    

    
}
