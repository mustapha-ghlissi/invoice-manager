<?php

require_once 'Database.php';
require_once 'Invoice.php';
use InvoiceGenerator\Config\Database;
use InvoiceGenerator\Entity\Invoice;

class InvoiceCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }


    //Get all invoices
    public function getInvoices(): ?array
    {
        $query = "SELECT * FROM invoice";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     //Get all invoices
     public function getOrderedInvoices(string $orderBy, string $dir, int $start, int $length): ?array
     {
         $query = "SELECT * FROM invoice ORDER BY $orderBy $dir LIMIT $start ,$length";
         $stmt = self::$pdo->prepare($query);
         $stmt->execute();
         if($stmt->rowCount() === 0)
         {
             return null;
         }
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

    public function getInvoicesByCriteria(string $criteria,string $orderBy, string $dir, int $start, int $length)
    {
      $query = "SELECT * FROM invoice where 1=1";
      $query .= " AND id LIKE :criteria";
      $query .= " OR repertory LIKE :criteria";
      $query .= " OR quote LIKE :criteria";
      $query .= " OR ref LIKE :criteria";
      $query .= " OR startDate LIKE :criteria";
      $query .= " OR endDate LIKE :criteria";
      $query .= " OR status LIKE :criteria"; 
      $query .= " OR publicnote LIKE :criteria"; 
      $query .= " OR privatenote LIKE :criteria";
      $query .= " ORDER BY $orderBy $dir LIMIT $start ,$length";
      $stmt = self::$pdo->prepare($query);

      $criteria = $criteria.'%';
      
      $stmt->bindParam(':criteria',$criteria);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoiceByRef(string $ref)
    {
      $query = "SELECT * FROM invoice where ref = :ref LIMIT 1";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':ref',$ref);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\Invoice");
    }

    
    public function getInvoiceByQuote(int $quote)
    {
      $query = "SELECT * FROM invoice where quote = :quote LIMIT 1";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':quote',$quote);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\Invoice");
    }


    //Save the invoice
    public function saveInvoice(Invoice $invoice): ?int
    {
        $query = "INSERT INTO invoice (repertory, quote, ref, startDate, endDate, status , publicnote, privatenote)
        VALUES (:repertory, :quote, :ref, :startDate, :endDate, :status, :publicnote, :privatenote)";

        $stmt = self::$pdo->prepare($query);

        $repertory = $invoice->getRepertory();
        $quote = $invoice->getQuote();
        $ref = $invoice->getRef();
        $startDate = $invoice->getStartDate()->format('Y-m-d');
        $endDate = $invoice->getEndDate()->format('Y-m-d');
        $status = $invoice->getStatus();
        $publicNote = $invoice->getPublicNote();
        $privateNote = $invoice->getPrivateNote();

        $stmt->bindParam(':repertory', $repertory);
        $stmt->bindParam(':quote', $quote);
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':publicnote', $publicNote);
        $stmt->bindParam(':privatenote', $privateNote);
        $stmt->execute();

        return self::$pdo->lastInsertId();
    }

    //Edit the invoice
    public function editInvoice(Invoice $invoice, int $id): ?string
    {
        $query = "UPDATE invoice SET repertory = :repertory, ref = :ref, startDate = :startDate, endDate = :endDate, status = :status, publicnote = :publicnote, privatenote = :privatenote
            WHERE id = :id";
        $stmt = self::$pdo->prepare($query);

        $repertory = $invoice->getRepertory();
        $ref = $invoice->getRef();
        $startDate = $invoice->getStartDate()->format('Y-m-d');
        $endDate = $invoice->getEndDate()->format('Y-m-d');
        $status = $invoice->getStatus();
        $publicNote = $invoice->getPublicNote();
        $privateNote = $invoice->getPrivateNote();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':repertory', $repertory);
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':publicnote', $publicNote);
        $stmt->bindParam(':privatenote', $privateNote);
        $stmt->execute();

        return "Invoice updated successfully";
    }

    //Show the invoice
    public function showInvoice(int $id): ?array
    {
        $query = "SELECT * FROM invoice WHERE id=:id LIMIT 1";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\Invoice");
    }

    //Delete the invoice
    public function deleteInvoice($id): ?string
    {
        $query = "DELETE FROM invoice WHERE id=:id";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return "Invoice deleted successfully";
    }

    public function deleteInvoiceByQuote(int $quote)
    {
      $query = "DELETE FROM invoice where quote = :quote";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':quote', $quote);
      $stmt->execute();
    }
}
