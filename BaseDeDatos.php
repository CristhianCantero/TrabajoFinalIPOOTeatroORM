<?php

class BaseDatos {
    private $HOSTNAME;
    private $BASEDATOS;
    private $USUARIO;
    private $CLAVE;
    private $CONEXION;
    private $QUERY;
    private $RESULT;
    private $ERROR;
    /**
     * Constructor de la clase que inicia ls variables instancias de la clase
     * vinculadas a la coneccion con el Servidor de BD
     */
    public function __construct(){
        $this->HOSTNAME = "127.0.0.1";
        $this->BASEDATOS = "teatro_pruebas";
        $this->USUARIO = "root";
        $this->CLAVE="";
        $this->RESULT=0;
        $this->QUERY="";
        $this->ERROR="";
    }
        /**
     * Get the value of HOSTNAME
     */ 
    public function getHOSTNAME()
    {
        return $this->HOSTNAME;
    }

    /**
     * Set the value of HOSTNAME
     */ 
    public function setHOSTNAME($HOSTNAME)
    {
        $this->HOSTNAME = $HOSTNAME;
    }

    /**
     * Get the value of BASEDATOS
     */ 
    public function getBASEDATOS()
    {
        return $this->BASEDATOS;
    }

    /**
     * Set the value of BASEDATOS
     */ 
    public function setBASEDATOS($BASEDATOS)
    {
        $this->BASEDATOS = $BASEDATOS;
    }

    /**
     * Get the value of USUARIO
     */ 
    public function getUSUARIO()
    {
        return $this->USUARIO;
    }

    /**
     * Set the value of USUARIO
     */ 
    public function setUSUARIO($USUARIO)
    {
        $this->USUARIO = $USUARIO;
    }

    /**
     * Get the value of CLAVE
     */ 
    public function getCLAVE()
    {
        return $this->CLAVE;
    }

    /**
     * Set the value of CLAVE
     */ 
    public function setCLAVE($CLAVE)
    {
        $this->CLAVE = $CLAVE;
    }

    /**
     * Get the value of CONEXION
     */ 
    public function getCONEXION()
    {
        return $this->CONEXION;
    }

    /**
     * Set the value of CONEXION
     */ 
    public function setCONEXION($CONEXION)
    {
        $this->CONEXION = $CONEXION;
    }

    /**
     * Get the value of QUERY
     */ 
    public function getQUERY()
    {
        return $this->QUERY;
    }

    /**
     * Set the value of QUERY
     */ 
    public function setQUERY($QUERY)
    {
        $this->QUERY = $QUERY;
    }

    /**
     * Get the value of RESULT
     */ 
    public function getRESULT()
    {
        return $this->RESULT;
    }

    /**
     * Set the value of RESULT
     */ 
    public function setRESULT($RESULT)
    {
        $this->RESULT = $RESULT;
    }

    /**
     * Funcion que retorna una cadena
     * con una peque�a descripcion del error si lo hubiera
     *
     * @return string
     */
    public function getError(){
        return "\n".$this->ERROR;
        
    }
    /**
     * Set the value of ERROR
     */ 
    public function setERROR($ERROR)
    {
        $this->ERROR = $ERROR;
    }
    
    /**
     * Inicia la coneccion con el Servidor y la  Base Datos Mysql.
     * Retorna true si la coneccion con el servidor se pudo establecer y false en caso contrario
     *
     * @return boolean
     */
    public function Iniciar(){
        $resp  = false;
        $conexion = mysqli_connect($this->getHOSTNAME(),$this->getUSUARIO(),$this->getCLAVE(),$this->getBASEDATOS());
        if ($conexion){
            if (mysqli_select_db($conexion,$this->getBASEDATOS())){
                $this->setCONEXION($conexion);
                unset($this->QUERY);
                unset($this->ERROR);
                $resp = true;
            }  else {
                $error = mysqli_errno($conexion) . ": " . mysqli_error($conexion);
                $this->setERROR($error); 
            }
        }else{
            $error = mysqli_errno($conexion) . ": " . mysqli_error($conexion);
            $this->setERROR($error); 
        }
        return $resp;
    }
    
    /**
     * Ejecuta una consulta en la Base de Datos.
     * Recibe la consulta en una cadena enviada por parametro.
     *
     * @param string $consulta
     * @return boolean
     */
    public function Ejecutar($consulta){
        $resp  = false;
        unset($this->ERROR);
        $this->setQUERY($consulta);
        $resultado = mysqli_query($this->CONEXION,$consulta);
        $this->setRESULT($resultado);
        if($resultado){
            $resp = true;
        } else {
            $error = mysqli_errno($this->getCONEXION()).": ". mysqli_error($this->getCONEXION());
            $this->setERROR($error);
        }
        return $resp;
    }
    
    /**
     * Devuelve un registro retornado por la ejecucion de una consulta
     * el puntero se despleza al siguiente registro de la consulta
     *
     * @return boolean
     */
    public function Registro() {
        $resp = null;
        $resultado = $this->getRESULT();
        if($resultado){
            unset($this->ERROR);
            $temp = mysqli_fetch_assoc($this->RESULT);
            if($temp){
                $resp = $temp;
            }else{
                mysqli_free_result($this->RESULT);
            }
        }else{
            $error = mysqli_errno($this->getCONEXION()).": ". mysqli_error($this->getCONEXION());
            $this->setERROR($error);
        }
        return $resp;
    }
    
    /**
     * Devuelve el id de un campo autoincrement utilizado como clave de una tabla
     * Retorna el id numerico del registro insertado, devuelve null en caso que la ejecucion de la consulta falle
     *
     * @param string $consulta
     * @return int id de la tupla insertada
     */
    public function devuelveIDInsercion($consulta){
        $resp = null;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if ($this->RESULT = mysqli_query($this->CONEXION,$consulta)){
            $id = mysqli_insert_id($this->CONEXION);
            $resp =  $id;
        } else {
            $conexion = $this->getCONEXION();
            $error = mysqli_errno($conexion) . ": " .mysqli_error($conexion);
            $this->setERROR($error); 
        }
    return $resp;
    }
}
?>