<?php
class EdificioTeatro{
    private $idEdificioTeatro;
    private $nombre;
    private $direccion;
    private $ciudad;
    private $mensajeoperacion;
    private $coleccionFunciones;
    
    public function __construct($nombre, $direccion, $ciudad)
    {
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->ciudad = $ciudad;
        $this->coleccionFunciones = [];
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getDireccion(){
        return $this->direccion;
    }
    public function getIdEdificioTeatro()
    {
        return $this->idEdificioTeatro;
    }
    public function getCiudad()
    {
        return $this->ciudad;
    }
    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}
    public function getColeccionFunciones()
    {
        $hora = array('hora' => "", 'minutos' => "");
        $fecha = array('anio' => "", 'mes' => "", 'dia' => "");
        $condicion = " actividad.idTeatro=". $this->getIdEdificioTeatro();

        $objCineBase = new Cine("", "", $hora, $fecha, "", "", "", "");
        $objMusicalBase = new Musical("", "", $hora, $fecha, "", "", "", "");
        $objObraTeatroBase = new ObraTeatro("", "", $hora, $fecha, "", "", "", "");

        $colPelicula = $objCineBase->listar($condicion);
        $colMusicales = $objMusicalBase->listar($condicion);
        $colObrasTeatro = $objObraTeatroBase->listar($condicion);
        
        $colecFunciones = array_merge($colPelicula, $colMusicales, $colObrasTeatro);
        return $colecFunciones;
    }

    public function setColeccionFunciones($coleccionFunciones)
    {
        $this->coleccionFunciones = $coleccionFunciones;
    }
    
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }
    public function setNombre($n){
        $this->nombre = $n;
    }
    public function setDireccion($d){
        $this->direccion = $d;
    }
    public function setIdEdificioTeatro($idEdificioTeatro)
    {
        $this->idEdificioTeatro = $idEdificioTeatro;
    }
    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;
	}

    public function getPrecioActividad($tabla, $idTeatro, $mesDeseado){
        $sumaPrecios = 0;
        $base=new BaseDatos();
        $consultaPrecio="SELECT actividad.precio from actividad INNER JOIN ".$tabla." ON actividad.idActividad=".$tabla.".idActividad WHERE actividad.idTeatro=".$idTeatro." and MONTH(actividad.fecha)=".$mesDeseado;
        
        if($base->Iniciar()){
            if($base->Ejecutar($consultaPrecio)){
                $coleccionPrecios= array();
				while($row2=$base->Registro()){
                    $precio = ($row2['precio']);
                    array_push($coleccionPrecios, $precio);
				}
            }else{
                echo "ERROR EN LA EJECUCION DE LA BUSQUEDA DE PRECIO: " . $base->getError();
            }
        }else{
            echo "ERROR: " . $base->getError();
        }
        foreach($coleccionPrecios as $precio){
            $sumaPrecios = $sumaPrecios + $precio;
        }
        return $sumaPrecios;
    }

    public function darCostos($mes, $idTeatro){
        // Inicializo precios
        $precioFinalCine = 0;
        $precioFinalMusical = 0;
        $precioFinalObraTeatro = 0;
        // Genero un array con las tablas de mis actividades
        $arrayTablas = array("cine", "musical", "obrateatro");
        // Recorro el array de las tablas y con eso mando para hacer la consulta a la base de datos
        foreach ($arrayTablas as $tabla) {
            $precio = $this->getPrecioActividad($tabla, $idTeatro, $mes);
            // Seteo la suma de los precios obtenido de la base de datos dependiendo de cual tabla sea
            switch ($tabla) {
                case 'cine':
                    $precioCine = $precio;
                break;
                case 'musical':
                    $precioMusical = $precio;
                break;
                case 'obrateatro':
                    $precioObraTeatro = $precio;
                break;
            }
        }
        // Hago las cuentas con los porcentajes de incremento
        $precioFinalCine = $precioCine * 1.65;
        $precioFinalMusical = $precioMusical * 1.12;
        $precioFinalObraTeatro = $precioObraTeatro * 1.45;
        // echo "Precio cine: " . $precioCine . "\n";
        // echo "Precio musical: " . $precioMusical . "\n";
        // echo "Precio obra teatral: " . $precioObraTeatro . "\n";
        // echo "Precio final cine: " . $precioFinalCine . "\n";
        // echo "Precio final musical: " . $precioFinalMusical . "\n";
        // echo "Precio final obra musical: " . $precioFinalObraTeatro . "\n";
        // Hago la suma de los valores y lo devuelvo al programa principal
        $costoTotalTeatro = $precioFinalCine + $precioFinalMusical + $precioFinalObraTeatro;
        return $costoTotalTeatro;
    }

    public function cambiarDatos($arrayInfoTeatro){
        $this->setNombre($arrayInfoTeatro['nombre']);
        $this->setDireccion($arrayInfoTeatro['direccion']);
        $this->setCiudad($arrayInfoTeatro['ciudad']);
    }

    // CONSULTAS PARA EL ORM

    /**
	 * @param int $idTeatro
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($idTeatro){
		$base=new BaseDatos();
		$consultaTeatro="Select * from teatro where idTeatro=".$idTeatro;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaTeatro)){
				if($row=$base->Registro()){
				    $this->setIdEdificioTeatro($row['idTeatro']);
				    $this->setNombre($row['nombre']);
                    $this->setDireccion($row['direccion']);
                    $this->setCiudad($row['ciudad']);
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
	    $arregloTeatros = null;
		$base=new BaseDatos();
		$consultaTeatro="Select * from teatro ";
		if($condicion!=""){
		    $consultaTeatro=$consultaTeatro.' where '.$condicion;
		}
        $consultaTeatro.=" order by ciudad";

		if($base->Iniciar()){
			if($base->Ejecutar($consultaTeatro)){				
				$arregloTeatros= array();
				while($row2=$base->Registro()){
                    $idTeatro = ($row2['idTeatro']);
				
					$objEdificioTeatro = new EdificioTeatro('', '', '');
					$objEdificioTeatro->Buscar($idTeatro);

					array_push($arregloTeatros,$objEdificioTeatro);
				}
		 	}else{
		 		$this->setmensajeoperacion($base->getError());
			}
		}else{
		 	$this->setmensajeoperacion($base->getError());
		}	
		return $arregloTeatros;
	}	

	public function insertar(){
		$base=new BaseDatos();
		$resp=false;
		$consultaInsertar="INSERT INTO teatro(nombre, direccion, ciudad) VALUES ('".$this->getNombre()."','".$this->getDireccion()."','".$this->getCiudad()."')";
		
        if($base->Iniciar()){
            $id = $base->devuelveIDInsercion($consultaInsertar);
			if($id<>null){
                $this->setIdEdificioTeatro($id);
			    $resp=true;
			}else{
				$this->setmensajeoperacion($base->getError());	
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	public function modificar(){
	    $resp =false; 
	    $base=new BaseDatos();
		$consultaModifica= "UPDATE teatro SET nombre='".$this->getNombre()."',direccion='".$this->getDireccion()."',ciudad='".$this->getCiudad()."' WHERE idTeatro=".$this->getIdEdificioTeatro();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp= true;
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}else{
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM teatro WHERE idTeatro=".$this->getIdEdificioTeatro();
				if($base->Ejecutar($consultaBorra)){
				    $resp=true;
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
        return  "ID Teatro: " . $this->getIdEdificioTeatro() . "\n" . 
                "Nombre del teatro: " . $this->getNombre() . "\n" .
                "Direccion del teatro: " . $this->getDireccion() . "\n" .
                "Ciudad del teatro: " . $this->getCiudad() . "\n"
            ;

    }

    

    
}
