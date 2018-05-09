<?php

require_once 'Database.php';
use InvoiceGenerator\Config\Database;

class ArticleCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }


    //Get all invoices
    public function getArticles(): ?array
    {
        $query = "SELECT * FROM x_stockarticle";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticlesByTerm($label)
    {
      $query = "SELECT * FROM x_stockarticle WHERE label LIKE :label";
      $stmt = self::$pdo->prepare($query);
      $label = '%'.$label.'%';
      $stmt->bindParam(':label', $label);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
