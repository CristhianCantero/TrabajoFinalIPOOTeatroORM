<?php
include_once "Actividad.php";
class Cine extends Actividad{
    private $genero;
    private $paisOrigen;
	private $mensajeoperacion;

    public function __construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio, $genero, $paisOrigen){
		parent::__construct($teatro, $nombre, $horaInicio, $fecha, $duracionActividad, $precio);
        $this->genero = $genero;
        $this->paisOrigen = $paisOrigen;
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
		
    public function Buscar($idActividad){
		$base=new BaseDatos();
		$consultaActividad="Select * from cine where idActividad=".$idActividad;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaActividad)){
				if($row=$base->Registro()){
				    $idActiv = ($row['idActividad']);
                    $this->setGenero($row['genero']);
                    $this->setPaisOrigen($row['paisOrigen']);

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
                    $idActividad = ($row2['idActividad']);
				
					$objCine = new Cine('', '', '', '','', '', '', '',);
					$objCine->Buscar($idActividad);
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
		// llamar al insertar del padre
		if(parent::insertar()){
			$idActividad = parent::getIdActividad();
			$consultaInsertar="INSERT INTO cine(idActividad, genero, paisOrigen) VALUES (".$idActividad.",'".$this->getGenero()."','".$this->getPaisOrigen()."')";
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
		// modelar herencia
		if(parent::modificar()){
			$idActividad = parent::getIdActividad();
			$consultaModifica= "UPDATE cine SET genero='".$this->getGenero()."',paisOrigen='".$this->getPaisOrigen()."' WHERE idActividad=".$idActividad;
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
		// modelar herencia
		if($base->Iniciar()){
			$idActividad = parent::getIdActividad();
			$consultaBorra="DELETE FROM cine WHERE idActividad=".$idActividad;
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
                "Genero: " . $this->getGenero() . "\n" . 
                "Pais de Origen: " . $this->getPaisOrigen() . "\n"
                ;
    }
    
}