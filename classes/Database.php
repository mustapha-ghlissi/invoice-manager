<?php

namespace InvoiceGenerator\Config;
use PDO;

class Database
{
	const DB_HOST = 'localhost';
	const DB_DRIVER = 'mysql';
	const DB_USER = 'root';
	const DB_PASS = '';
	const DB_NAME = 'invoice-2018';


	public static function getConnection(): PDO
	{
		try
		{
	    	$pdo = new PDO(self::DB_DRIVER.':host='.self::DB_HOST.';dbname='.self::DB_NAME,self::DB_USER, self::DB_PASS);
	    	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    	return $pdo;
	    }
		catch(PDOException $e)
	    {
	    	echo "Connection failed: " . $e->getMessage();
	    }
	}
}
