<?php
/*class Database{
    private $host;
    private $username;
    private $db_name;
    private $dbpass;
    private $con;
    // ...........

    public function __construct()
    {
        $this->host = "localhost";
        $this->db_name = "myapp";
        $this->username = "root";
        $this->dbpass = "";
        
    }

    protected function connect(){
        try {
            $this->con = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name, $this->username, $this->dbpass);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->con;
    
       } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}
}*/
?>