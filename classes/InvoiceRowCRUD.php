<?php

require_once 'Database.php';
require_once 'InvoiceRow.php';
use InvoiceGenerator\Config\Database;
use InvoiceGenerator\Entity\InvoiceRow;

class InvoiceRowCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }

    //Get all invoice rows
    public function getInvoiceRows(): ?array
    {
        $query = "SELECT * FROM invoiceRow";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get all invoice rows by invoice
    public function getInvoiceRowsByInvoice($invoice): ?array
    {
        $query = "SELECT * FROM invoiceRow where invoice = :invoice";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':invoice', $invoice);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\InvoiceRow");
    }


    //Save the invoice row
    public function saveInvoiceRow(InvoiceRow $invoiceRow): ?string
    {

        $query = "INSERT INTO invoiceRow (invoice, label, quantity, tax, unityprice, note)
        VALUES (:invoice, :label, :quantity, :tax, :unityprice, :note)";

        $stmt = self::$pdo->prepare($query);
        $invoice = $invoiceRow->getInvoice();
        $label = $invoiceRow->getLabel();
        $quantity = $invoiceRow->getQuantity();
        $tax = $invoiceRow->getTax();
        $unityPrice = $invoiceRow->getUnityPrice();
        $note = $invoiceRow->getNote();
        $stmt->bindParam(':invoice', $invoice);
        $stmt->bindParam(':label', $label);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':tax', $tax);
        $stmt->bindParam(':unityprice', $unityPrice);
        $stmt->bindParam(':note', $note);
        $stmt->execute();

        if($stmt->rowCount() === 0 )
        {
            return "InvoiceRow failed to save";
        }
        return "InvoiceRow saved successfully";
    }

    //Edit the invoice row
    public function editInvoiceRow(InvoiceRow $invoiceRow): ?string
    {
        $query = "UPDATE invoiceRow SET label = :label, quantity = :quantity, tax = :tax, unityprice = :unityprice, note = :note
            WHERE id = :id";

        $stmt = self::$pdo->prepare($query);
        $id = $invoiceRow->getId();
        $label = $invoiceRow->getLabel();
        $quantity = $invoiceRow->getQuantity();
        $tax = $invoiceRow->getTax();
        $unityprice = $invoiceRow->getUnityPrice();
        $note = $invoiceRow->getNote();


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':label', $label);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':tax', $tax);
        $stmt->bindParam(':unityprice', $unityprice);
        $stmt->bindParam(':note', $note);
        $stmt->execute();
        
        return "InvoiceRow updated successfully";
    }

    //Show the invoice row
    public function showInvoiceRow(int $id): ?array
    {
        $query = "SELECT * FROM invoiceRow WHERE id=:id LIMIT 1";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\InvoiceRow");
    }

    //Delete the invoice row
    public function deleteInvoiceRow($id): ?string
    {
        $query = "DELETE FROM invoiceRow WHERE id=:id";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if( $stmt->rowCount() === 0 )
        {
            return "InvoiceRow not found";
        }
        return "InvoiceRow deleted successfully";
    }

    //Delete the invoice row
    public function deleteInvoiceRowByInvoice($idInvoice): void
    {
        $query = "DELETE FROM invoiceRow WHERE invoice=:idInvoice";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':idInvoice', $idInvoice);
        $stmt->execute();
    }
}
