<?php

require_once 'Database.php';
require_once 'InvoicePayment.php';
use InvoiceGenerator\Config\Database;
use InvoiceGenerator\Entity\InvoicePayment;



class PaymentCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }

    public function getPaymentById(int $paymentId)
    {
      $query = "SELECT * FROM invoicePayment where id = :id LIMIT 1";
      $stmt = self::$pdo->prepare($query);
      $stmt->bindParam(':id',$paymentId);
      $stmt->execute();
      if($stmt->rowCount() === 0)
      {
          return null;
      }
      return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\InvoicePayment");
    }

    //Get all payments
    public function getPaymentsByInvoice($invoiceId): ?array
    {
        $query = "SELECT * FROM invoicePayment WHERE invoice = :invoiceId";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':invoiceId', $invoiceId);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Save the payment invoice
    public function savePayment(InvoicePayment $invoicePayment): ?string
    {
        $query = "INSERT INTO invoicePayment (invoice, method, date, amount, publicnote, privatenote)
        VALUES (:invoice, :method, :date, :amount, :publicnote, :privatenote)";

        $stmt = self::$pdo->prepare($query);

        $invoice = $invoicePayment->getInvoice();
        $method = $invoicePayment->getMethod();
        $date = $invoicePayment->getDate()->format('Y-m-d');
        $amount = $invoicePayment->getAmount();
        $publicNote = $invoicePayment->getPublicNote();
        $privateNote = $invoicePayment->getPrivateNote();

        $stmt->bindParam(':invoice', $invoice);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':publicnote', $publicNote);
        $stmt->bindParam(':privatenote', $privateNote);
        $stmt->execute();

        if($stmt->rowCount() === 0)
        {
            return "Failed to save the invoice payment";
        }

        return "Invoice payment saved successfully";
    }

    //Edit the payment
    public function editPayment(InvoicePayment $invoicePayment): ?string
    {
        $query = "UPDATE invoicePayment SET method = :method, date = :date, amount = :amount, publicnote = :publicnote, privatenote = :privatenote
            WHERE id = :id";
        $stmt = self::$pdo->prepare($query);

        $id = $invoicePayment->getId();
        $method = $invoicePayment->getMethod();
        $date = $invoicePayment->getDate()->format('Y-m-d');
        $amount = $invoicePayment->getAmount();
        $publicNote = $invoicePayment->getPublicNote();
        $privateNote = $invoicePayment->getPrivateNote();


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':publicnote', $publicNote);
        $stmt->bindParam(':privatenote', $privateNote);
        $stmt->execute();
        if( $stmt->rowCount() === 0 )
        {
            return "InvoicePayment not found";
        }
        return "InvoicePayment updated successfully";
    }

    //Delete the payment
    public function deletePayment($id): ?string
    {
        $query = "DELETE FROM invoicePayment WHERE id=:id";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if( $stmt->rowCount() === 0 )
        {
            return "InvoicePayment not found";
        }
        return "InvoicePayment deleted successfully";
    }

    //Delete the invoice payment by invoice
    public function deletePaymentByInvoice($idInvoice): void
    {
        $query = "DELETE FROM invoicePayment WHERE invoice=:idInvoice";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':idInvoice', $idInvoice);
        $stmt->execute();
    }
}
