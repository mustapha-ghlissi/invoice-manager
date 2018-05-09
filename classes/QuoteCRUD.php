<?php

require_once 'Database.php';
require_once 'Quote.php';
use InvoiceGenerator\Config\Database;
use InvoiceGenerator\Entity\Quote;

class QuoteCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }


    //Get all quotes
    public function getQuotes(): ?array
    {
        $query = "SELECT id,repertory,ref,creationDate,dueDate,status,amount FROM quote";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuoteByRef(string $ref)
    {
      $query = "SELECT * FROM quote where ref = :ref LIMIT 1";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':ref',$ref);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\Quote");
    }

    //Save the quote
    public function saveQuote(Quote $quote): ?int
    {
        $query = "INSERT INTO quote (repertory, ref, creationDate, dueDate, status, amount, publicnote, privatenote)
        VALUES (:repertory, :ref, :creationDate, :dueDate, :status, :amount, :publicnote, :privatenote)";

        $stmt = self::$pdo->prepare($query);

        $repertory = $quote->getRepertory();
        $ref = $quote->getRef();
        $creationDate = $quote->getCreationDate()->format('Y-m-d');
        $dueDate = $quote->getDueDate()->format('Y-m-d');
        $status = $quote->getStatus();
        $amount = $quote->getAmount();
        $publicNote = $quote->getPublicNote();
        $privateNote = $quote->getPrivateNote();
        $stmt->bindParam(':repertory', $repertory);
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':dueDate', $dueDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':publicnote', $publicNote);
        $stmt->bindParam(':privatenote', $privateNote);
        $stmt->execute();
        return self::$pdo->lastInsertId();
    }


    //Edit the quote
    public function editQuote(Quote $quote, int $id): ?string
    {
        $query = "UPDATE quote SET ref = :ref, creationDate = :creationDate, dueDate = :dueDate, status = :status, amount = :amount, publicnote = :publicnote, privatenote = :privatenote
            WHERE id = :id";
        $stmt = self::$pdo->prepare($query);
        $ref = $quote->getRef();

        if(is_string($quote->getCreationDate()))
        {
          $creationDate = $quote->getCreationDate();
        }
        else {
          $creationDate = $quote->getCreationDate()->format('Y-m-d');
        }

        if(is_string($quote->getCreationDate()))
        {
          $dueDate = $quote->getDueDate();
        }
        else {
          $dueDate = $quote->getDueDate()->format('Y-m-d');
        }



        $status = $quote->getStatus();
        $amount = $quote->getAmount();
        $publicNote = $quote->getPublicNote();
        $privateNote = $quote->getPrivateNote();
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':dueDate', $dueDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':publicnote', $publicNote);
        $stmt->bindParam(':privatenote', $privateNote);
        $stmt->execute();

        return "Quote updated successfully";
    }

    //Show the quote
    public function showQuote(int $id): ?array
    {
        $query = "SELECT * FROM quote WHERE id=:id LIMIT 1";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\Quote");
    }

    //Delete the quote
    public function deleteQuote($id): ?string
    {
        $query = "DELETE FROM quote WHERE id=:id";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return "Quote deleted successfully";
    }
}
