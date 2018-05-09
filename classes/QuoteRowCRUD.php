<?php

require_once 'Database.php';
require_once 'QuoteRow.php';
use InvoiceGenerator\Config\Database;
use InvoiceGenerator\Entity\QuoteRow;

class QuoteRowCRUD{

    private static $pdo;
    public function __construct()
    {
        self::$pdo = Database::getConnection();
    }

    //Get all quote rows
    public function getQuoteRows(): ?array
    {
        $query = "SELECT * FROM quoteRow";
        $stmt = self::$pdo->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get all quote rows by quote
    public function getQuoteRowsByQuote($quote): ?array
    {
        $query = "SELECT * FROM quoteRow where quote = :quote";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':quote', $quote);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\QuoteRow");
    }

    //Save the quote row
    public function saveQuoteRow(QuoteRow $quoteRow): ?string
    {
        $query = "INSERT INTO quoteRow (quote, label, quantity, tax, unityprice, note)
        VALUES (:quote, :label, :quantity, :tax, :unityprice, :note)";

        $stmt = self::$pdo->prepare($query);
        $quote = $quoteRow->getQuote();
        $label = $quoteRow->getLabel();
        $quantity = $quoteRow->getQuantity();
        $tax = $quoteRow->getTax();
        $unityPrice = $quoteRow->getUnityPrice();
        $note = $quoteRow->getNote();
        $stmt->bindParam(':quote', $quote);
        $stmt->bindParam(':label', $label);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':tax', $tax);
        $stmt->bindParam(':unityprice', $unityPrice);
        $stmt->bindParam(':note', $note);
        $stmt->execute();

        if($stmt->rowCount() === 0 )
        {
            return "QuoteRow failed to save";
        }
        return "QuoteRow saved successfully";
    }

    //Edit the quote row
    public function editQuoteRow(QuoteRow $quoteRow): ?string
    {
        $query = "UPDATE quoteRow SET label = :label, quantity = :quantity, tax = :tax, unityprice = :unityprice, note = :note
            WHERE id = :id";

        $stmt = self::$pdo->prepare($query);
        $id = $quoteRow->getId();
        $label = $quoteRow->getLabel();
        $quantity = $quoteRow->getQuantity();
        $tax = $quoteRow->getTax();
        $unityprice = $quoteRow->getUnityPrice();
        $note = $quoteRow->getNote();


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':label', $label);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':tax', $tax);
        $stmt->bindParam(':unityprice', $unityprice);
        $stmt->bindParam(':note', $note);
        $stmt->execute();
        
        return "QuoteRow updated successfully";
    }

    //Show the quote row
    public function showQuoteRow(int $id): ?array
    {
        $query = "SELECT * FROM quoteRow WHERE id=:id LIMIT 1";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if($stmt->rowCount() === 0)
        {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "InvoiceGenerator\Entity\QuoteRow");
    }

    //Delete the quote row
    public function deleteQuoteRow($id): ?string
    {
        $query = "DELETE FROM quoteRow WHERE id=:id";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if( $stmt->rowCount() === 0 )
        {
            return "QuoteRow not found";
        }
        return "QuoteRow deleted successfully";
    }


    //Delete the quote row
    public function deleteQuoteRowByQuote($idQuote): void
    {
        $query = "DELETE FROM quoteRow WHERE quote=:idQuote";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindParam(':idQuote', $idQuote);
        $stmt->execute();
    }
}
