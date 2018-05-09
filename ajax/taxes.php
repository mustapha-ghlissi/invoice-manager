<?php

if(isset($_GET['q']) && !empty(trim($_GET['q'])))
{
  require '../classes/TaxCRUD.php';
  $taxCRUD = new TaxCRUD();
  $taxes = $taxCRUD->getTaxesByAmount($_GET['q']);
  if(is_array($taxes) && count($taxes)>0)
  {
      foreach($taxes as $key=>$tax)
      {
          $data [] = ['id'=>$tax['id'], 'text'=>$tax['amount']];
      }
  }
  else
  {
    $data[] = ['id'=>'0', 'text'=>'No taxes found'];
  }
  exit(json_encode($data));
}
