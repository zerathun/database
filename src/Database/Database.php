<?php
namespace Database;

use \mysqli;
//use EmbDev\models\FileLog;
//use EmbDev\models\ErrorHandler;

class Database {
    private $sqlC;
    private $mysql;
    private static $instance;
    private $query_counter = 0;
    private $server;
    private $db;
    private $uname;
    private $pw;
    private $dbh;
    
    public function __construct() {

    }
    
    public function connect() {
        if(empty($server) || empty($db) || empty($uname)) {
            throw new Exception("DB Credentials not set");
        }
        
        $dsn = 'mysql:dbname='.$db.';host='.$server;
        $user = $uname;
        $password = $pw;
        
        try {
            $this->dbh = new  \PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            FileLog::getInstance()->appendLog("SQL Failure: \n $sql\n".$e->getMessage());
            print_r('Connection failed: ' . $e->getMessage());
        }
    }
    
    public function setDatabase($db) {
        $this->db = $db;
    }
    
    public function setUsername($uname) {
        $this->uname = $uname;
    }
    
    public function setPassword($pw) {
        $this->pw = $pw;
    }
    
    public function setServer($server) {
        $this->server = $server;
    }
    
    
    public function getDBName() {
        return $this->db;
    }
    
    public static function getInstance() {
        if (empty ( Database::$instance )) {
            Database::startDatabaseConnection ();
        }
        return Database::$instance;
    }
    
    public static function startDatabaseConnection() {
        Database::$instance = new Database ();
    }
    
    public function getPDOConnection() {
        return $this->dbh;
    }
    
    /**
     * Query Function
     *
     * @param SQLQuery $query
     * @return Resource
     */
    public function sql_query($query) {
        
        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute();
            // Check if there is any error
            $err = $stmt->errorInfo();
            if(isset($err[0]) && intval($err[0]) != 0) {
                throw new \Exception($err[0]." ".$err[1]." ".$err[2]);
            }
            return $stmt;
        } catch(\Exception $e) {
            print "Error: ".$e->getMessage()."\n\n$query\n\n";
            FileLog::getInstance()->appendLog("SQL Failure: \n $sql\n".$e->getMessage());
            ErrorHandler::getErrorHandler()->addException($e);
        }
    }
    
    /**
     *
     * @param String $query
     * @throws \Exception
     * @return Resource
     */
    public function multiple_sql_query($query) {
        return $this->sql_query($query);
        
        $this->sqlC = mysqli_connect ( $this->server, $this->uname, $this->pw, $this->db );
        $_SESSION ['mysql_updates'] [UserManagement::getInstance ()->getCurrentUser ()->getId ()] ['ident_1'] = 1;
        $resource = mysqli_multi_query ( $this->sqlC, $query );
        mysqli_store_result ( $this->sqlC );
        $this->query_counter ++;
        
        if (mysqli_errno ( $this->sqlC ) == 0) {
            if (empty ( $resource )) {
                mysqli_close ( $this->sqlC );
                $_SESSION ['mysql_updates'] [UserManagement::getInstance ()->getCurrentUser ()->getId ()] ['ident_1'] = 0;
            } else {
                return $resource;
            }
        } else {
            throw new \Exception ( "SQL Query $query error ocurred <br><br>" . mysqli_error ( $this->sqlC ) );
        }
    }
    
    public function sql_fetch_object($resource) {
        if (empty ( $resource )) {
            throw new \Exception ( "Resource is empty " );
        }
        return $resource->fetch(\PDO::FETCH_OBJ);
    }
    
    public function sql_fetch_array($resource) {
        if (empty ( $resource )) {
            throw new \Exception ( "Resource is empty " );
        }
        return $resource->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function sql_fetch_row($resource) {
        return $this->sql_fetch_array($resource);
    }
    
    public function getQueryCount() {
        return $this->query_counter;
    }
    
    public function makeInjectionSafe($input) {
        preg_match('#\b(SELECT|DELETE|UPDATE|USER|DROP|CREATE|TABLE|ALTER)\b#', $input, $matches);
        if(count($matches)  > 0 || Database::isInjectionSafe($input) == false)
            throw new \Exception("SQL Injection Warning");
    }
    
    /**
     * returns true if the given string contains no dangerous keywords
     *
     * @param String $where_statement
     * @return Boolean
     */
    public static function isInjectionSafe($statement) {
        $needle = array (
            'DROP',
            'UPDATE',
            'DELETE',
            'PURGE',
            'ALTER',
            'CHANGE',
            'CREATE',
            'GRANT',
            'KILL',
            'RENAME',
            'RESTRICT',
            'SET',
            'UNDO',
            'UNLOCK',
            'LOCK',
            'WRITE'
        );
        $count = 0;
        foreach ( $needle as $substring ) {
            $count += substr_count ( $statement, $substring );
        }
        return ($count <= 0);
    }
    
    public function __destruct() {
    }
}

?>