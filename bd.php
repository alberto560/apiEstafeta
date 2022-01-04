<?php
class BD {
    //conexion con base de datos
    //public $cadena0 = "mysql:host=127.0.0.1;dbname=";
    //public $cadena1 = "idCliente00XX";
    //public $cadena;
    //public $cadena = "mysql:host=65.99.252.170;dbname=bitnami_wordpress";
    public $cadena = "mysql:host=localhost;dbname=bd";//3306
    public $user = "bd_user";
    public $password = "bd_pass";
    public $conn;
    public $depuracion = true;

    /*function __construct() {
       $this->cadena = $this->cadena0.$this->cadena1;
   }*/
    function open() {
        try {
            $this->conn = new PDO($this->cadena, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set character set utf8");
        } catch (PDOException $e) {
            if ($this->depuracion)
                echo $e->getMessage();
            $this->conn = NULL;
            die();
        }
    }
    function CerrarConexion() {
        $this->conn = NULL;
    }
//$this->bd->ConsultaPreparada("SELECT * FROM tabla WHERE correo=? AND password=? AND tipousuario=?", array($correo, $password, $tipo));
    function ConsultaPreparada($sql, $parametros) {
        if ($this->conn == NULL)
            $this->open();
        $sentencia = $this->conn->prepare($sql);
        //echo $sql;
        if ($sentencia->execute($parametros)) {
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } else {
            if ($this->depuracion)
                echo var_dump($sentencia->errorInfo());
            return null;
        }
    }

//this->bd->InsertarRegistrosPreparada("INSERT INTO tabla (id,campo2) VALUES (?,?)", array($ide, $camp)))
    public function InsertarRegistrosPreparada($sql, $parametros) {
        if ($this->conn == NULL)
            $this->open();
        $sentencia = $this->conn->prepare($sql);
        if ($sentencia->execute($parametros)) {
            return TRUE;
        } else {
            if ($this->depuracion)
            //var_dump($sentencia->errorInfo());
                return FALSE;
        }
    }
//$this->bd->ModificarRegistrosPreparada("UPDATE tabla SET titulo = ? WHERE id = ? AND estatus <> ?", array($nombre, $id, 2)))
    public function ModificarRegistrosPreparada($sql, $parametros) {
        if ($this->conn == NULL)
            $this->open();
        $sentencia = $this->conn->prepare($sql);
        if ($sentencia->execute($parametros)) {
            return TRUE;
        } else {
            if ($this->depuracion)
            // echo var_dump($sentencia->errorInfo());
                return FALSE;
        }
    }
    //$this->EliminarRegistro("DELETE FROM granja WHERE id_granja=?", array($id));
        public function EliminarRegistro($sql,$parametros) {
        if ($this->conn == NULL)
            $this->open();
        $sentencia = $this->conn->prepare($sql);
        if ($sentencia->execute($parametros)) {
            return TRUE;
        } else {
            if ($this->depuracion)
            // echo var_dump($sentencia->errorInfo());
                return FALSE;
        }
    }
    function ConsultaAsociativaOrdenada($tabla, $parametros) {
        if ($this->conn == NULL)
            $this->open();
        $sentencia = $this->conn->prepare("SELECT * FROM " . $tabla);
        if ($sentencia->execute($parametros)) {
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } else {
            if ($this->depuracion)
                echo var_dump($sentencia->errorInfo());
            return null;
        }
    }

    function ModificacionAlter($sql, $parametros) {
        if ($this->conn == NULL)
            $this->open();
        $sentencia = $this->conn->prepare($sql);
        //echo $sql;
        if ($sentencia->execute($parametros)) {
            return TRUE;
        } else {
            if ($this->depuracion)
            // echo var_dump($sentencia->errorInfo());
                return FALSE;
        }

    }

}
?>
