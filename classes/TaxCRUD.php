<?php

require_once 'Database.php';
use InvoiceGenerator\Config\Database;

class TaxCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }

    public function getTaxById(int $id): ?array
    {
      $query = "SELECT * FROM tax WHERE id = :id LIMIT 1";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get all taxes
    public function getTaxes(): ?array
    {
        $query = "SELECT * FROM tax";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaxesByAmount($amount): ?array
    {
      $query = "SELECT * FROM tax WHERE amount <= :amount";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':amount', $amount);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
