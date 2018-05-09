<?php

require_once 'Database.php';
use InvoiceGenerator\Config\Database;

class RepertoryCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }


    //Get all invoices
    public function getRepertories(): ?array
    {
        $query = "SELECT * FROM x_repertory";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get all repertories
    public function getOneById(int $id): ?array
    {
        $query = "SELECT * FROM x_repertory where id = :id LIMIT 1";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRepertoriesByTerm($term)
    {
      $query = "SELECT * FROM x_repertory WHERE name LIKE :term";
      $stmt = self::$pdo->prepare($query);
      $term = '%'.$term.'%';
      $stmt->bindParam(':term', $term);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
