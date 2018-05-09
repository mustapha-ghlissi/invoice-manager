<?php

require './vendor/autoload.php';
require './classes/EmailSender.php';
require './classes/QuoteCRUD.php';
require './classes/InvoiceCRUD.php';
require './classes/QuoteRowCRUD.php';
require './classes/InvoiceRowCRUD.php';
require './classes/TaxCRUD.php';
require './classes/RepertoryCRUD.php';
require './classes/PDFGenerator.php';
use InvoiceGenerator\Entity\Quote;
use InvoiceGenerator\Entity\Invoice;
use InvoiceGenerator\Entity\QuoteRow;
use InvoiceGenerator\Entity\InvoiceRow;
use PDF\Generator\PDFGenerator;

use Email\Sender\EmailSender;

if(isset($_POST['email']))
{
  if(isset($_POST['sentQuoteId'])){
    $quoteCRUD = new QuoteCRUD();
    $quoteRowCRUD = new QuoteRowCRUD();
    $taxCRUD = new TaxCRUD();
    $repertoryCRUD = new RepertoryCRUD();
    $taxes = $taxCRUD->getTaxes();

    $quote = $quoteCRUD->showQuote($_POST['sentQuoteId'])[0];
    $repertory = $repertoryCRUD->getOneById($quote->getRepertory())[0];
    $quoteRows = $quoteRowCRUD->getQuoteRowsByQuote($quote->getId());

    if($quote instanceof Quote)
    {
      ob_start();
      ?>
      <page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">

        <table style="width: 100%">
          <tr>
            <td colspan="2">
              <h1 style="color: #62A8EA">
                Invoice Manager
              </h1>
            </td>
          </tr>

          <tr>
            <td>
              <!-- You can add your company name here -->
              <h4>
                My company name
              </h4>
            </td>
            <td>
              Quoted to: <br>
              <h4><?php echo $repertory['name'] ?></h4>
            </td>
          </tr>
          <tr valign="center">
            <td style="width: 50%">
              <h1 style="color: #AAA">
                Quote N°<?php echo ($quote->getId()) ?>
              </h1>
            </td>
            <td style="width: 50%; color: #AAA ; padding-bottom: 40px">
              Reference N° <?php echo ($quote->getRef()); ?><br><br>
              Creation date <?php echo ($quote->getCreationDate()); ?><br><br>
              Due date <?php echo ($quote->getDueDate()); ?>
            </td>
          </tr>
        </table>


        <table style="width: 100%; border: 1px solid #EEE;">
          <thead>
            <tr style="background-color: #EEE; text-align: center;">
              <th style=" width: 10%; padding: 10px">
                N°
              </th>
              <th style=" width: 40%">
                Article
              </th>
              <th style=" width: 10%">
                Quantity
              </th>
              <th style=" width: 20%">
                Unity price
              </th>
              <th style=" width: 10%">
                Tax
              </th>
              <th style=" width: 10%">
                Subtotal
              </th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($quoteRows as $key => $quoteRow)
            {
              ?>
              <tr style="text-align: center;  border-bottom: 1px solid #EEE">
                <td style="padding: 10px;">
                  <?php echo ($key + 1); ?>
                </td>
                <td>
                  <?php echo ($quoteRow->getLabel()); ?>
                </td>
                <td>
                  <?php echo ($quoteRow->getQuantity()); ?>
                </td>
                <td>
                  <?php echo ($quoteRow->getUnityPrice()); ?>
                </td>
                <td>

                  <?php
                  $i = 0;
                  while ($taxes[$i]['id'] != $quoteRow->getTax() && $i<count($taxes))
                  {
                    $i++;
                  }
                  echo $taxes[$i]['amount'];
                  ?>
                </td>
                <td>
                  <?php
                  $tax = ($quoteRow->getTax()*($quoteRow->getUnityPrice() * $quoteRow->getQuantity()))/100;
                  echo  ($quoteRow->getUnityPrice() * $quoteRow->getQuantity()) + $tax;
                  ?>
                </td>
              </tr>
              <?php
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" style="padding-top: 60px"></td>
            </tr>
            <tr style="background-color: #EEE;">
              <th colspan="5" style="padding: 10px">
                Total
              </th>
              <th style="text-align: center">
                <?php echo ($quote->getAmount()) ?>
              </th>
            </tr>
          </tfoot>
        </table>


        <table style="width: 100%; margin-top: 50px">
          <tr>
            <td style="width: 40%">
            </td>
            <td style="width: 20%"></td>
            <td style="width: 40%; color: #000; padding-bottom: 100px">
              Buyer: <?php echo $repertory['name'] ?>
            </td>
          </tr>
        </table>

        <page_footer>
          <table style="width: 100%;">
            <tr>
              <td style="width: 40%">
                <hr>
                <h4 style="color: #000">
                  Signature & stamp
                </h4>
              </td>
              <td style="width: 20%"></td>
              <td style="width: 40%; color: #AAA">
                <hr>
                <h4 style="color: #000">
                  Signature & stamp
                </h4>
              </td>
            </tr>
          </table>
        </page_footer>

      </page>

      <?php
      $template = ob_get_clean();
      $fileName = uniqid().'.pdf';
      $pdfContent = PDFGenerator::generatePDF($fileName, $template, 'P', 'A4', 'S');
    }
  }
  elseif (isset($_POST['sentInvoiceId'])) {
    $invoiceCRUD = new InvoiceCRUD();
    $invoiceRowCRUD = new InvoiceRowCRUD();
    $taxCRUD = new TaxCRUD();
    $repertoryCRUD = new RepertoryCRUD();
    $taxes = $taxCRUD->getTaxes();

    $invoice = $invoiceCRUD->showInvoice($_POST['sentInvoiceId'])[0];
    $repertory = $repertoryCRUD->getOneById($invoice->getRepertory())[0];
    $invoiceRows = $invoiceRowCRUD->getInvoiceRowsByInvoice($invoice->getId());

    if($invoice instanceof Invoice)
    {
      ob_start();

      ?>

      <page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">

        <table style="width: 100%">
          <tr>
            <td colspan="2">
              <h1 style="color: #62A8EA">
                Invoice Manager
              </h1>
            </td>
          </tr>

          <tr>
            <td>
              <!-- You can add your company name here -->
              <h4>
                My company name
              </h4>
            </td>
            <td>
              Billed to: <br>
              <h4><?php echo $repertory['name'] ?></h4>
            </td>
          </tr>
          <tr valign="center">
            <td style="width: 50%">
              <h1 style="color: #AAA">
                Invoice N°<?php echo ($invoice->getId()) ?>
              </h1>
            </td>
            <td style="width: 50%; color: #AAA ; padding-bottom: 40px">
              Reference N° <?php echo ($invoice->getRef()); ?><br><br>
              Start date <?php echo ($invoice->getStartDate()); ?><br><br>
              End date <?php echo ($invoice->getEndDate()); ?>
            </td>
          </tr>
        </table>


        <table style="width: 100%; border: 1px solid #EEE;">
          <thead>
            <tr style="background-color: #EEE; text-align: center;">
              <th style=" width: 10%; padding: 10px">
                N°
              </th>
              <th style=" width: 40%">
                Article
              </th>
              <th style=" width: 10%">
                Quantity
              </th>
              <th style=" width: 20%">
                Unity price
              </th>
              <th style=" width: 10%">
                Tax
              </th>
              <th style=" width: 10%">
                Subtotal
              </th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total = 0;
            foreach ($invoiceRows as $key => $invoiceRow)
            {
              ?>
              <tr style="text-align: center;  border-bottom: 1px solid #EEE">
                <td style="padding: 10px;">
                  <?php echo ($key + 1); ?>
                </td>
                <td>
                  <?php echo ($invoiceRow->getLabel()); ?>
                </td>
                <td>
                  <?php echo ($invoiceRow->getQuantity()); ?>
                </td>
                <td>
                  <?php echo ($invoiceRow->getUnityPrice()); ?>
                </td>
                <td>

                  <?php
                  $i = 0;
                  while ($taxes[$i]['id'] != $invoiceRow->getTax() && $i<count($taxes))
                  {
                    $i++;
                  }
                  echo $taxes[$i]['amount'];
                  ?>
                </td>
                <td>
                  <?php
                  $tax = ($invoiceRow->getTax()*($invoiceRow->getUnityPrice() * $invoiceRow->getQuantity()))/100;
                  $total += ($invoiceRow->getUnityPrice() * $invoiceRow->getQuantity()) + $tax;
                  echo  (($invoiceRow->getUnityPrice() * $invoiceRow->getQuantity()) + $tax);
                  ?>
                </td>
              </tr>
              <?php
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" style="padding-top: 60px"></td>
            </tr>
            <tr style="background-color: #EEE;">
              <th colspan="5" style="padding: 10px">
                Total
              </th>
              <th style="text-align: center">
                <?php echo ($total); ?>
              </th>
            </tr>
          </tfoot>
        </table>


        <table style="width: 100%; margin-top: 50px">
          <tr>
            <td style="width: 40%">
            </td>
            <td style="width: 20%"></td>
            <td style="width: 40%; color: #000; padding-bottom: 100px">
              Buyer: <?php echo $repertory['name'] ?>
            </td>
          </tr>
        </table>

        <page_footer>
          <table style="width: 100%;">
            <tr>
              <td style="width: 40%">
                <hr>
                <h4 style="color: #000">
                  Signature & stamp
                </h4>
              </td>
              <td style="width: 20%"></td>
              <td style="width: 40%; color: #AAA">
                <hr>
                <h4 style="color: #000">
                  Signature & stamp
                </h4>
              </td>
            </tr>
          </table>
        </page_footer>

      </page>

      <?php
      $template = ob_get_clean();
      $fileName = uniqid().'.pdf';
      $pdfContent = PDFGenerator::generatePDF($fileName, $template, 'P', 'A4', 'S');
    }
  }



  $to = [$_POST['email'] => $_POST['name']];
  $subject = $_POST['subject'];
  $body = $_POST['body'];
  EmailSender::sendEmail($to, $subject, $body, $pdfContent, $fileName);
  $message = "Email sent successfully";
  $data = json_encode(array('message'=>$message));
  echo $data;
  exit;
}
?>
