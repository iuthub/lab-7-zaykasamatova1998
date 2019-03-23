<?php
class Connection{
	private $conn;
	public function __construct(){
		$servername = "localhost";
		$username = "blog";
		$password = "12345";
		try {
			$this->conn = new PDO("mysql:host=$servername;dbname=blog", $username, $password,[
				PDO::ATTR_EMULATE_PREPARES   => false,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			]);
			// set the PDO error mode to exception
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			echo "Connection failed: " . $e->getMessage();
			exit;
			$this->conn = null;
		}
	}
	public function prepare($sql){

		if($this->conn){
			return $this->conn->prepare($sql);
		}
		return null;
	}
	protected function getConn(){
		return $this->conn;
	}
}
