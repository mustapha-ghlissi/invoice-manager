<?php
if(isset($_GET['q']) && !empty(trim($_GET['q'])))
{
  require '../classes/RepertoryCRUD.php';
  $repertoryCRUD = new RepertoryCRUD();
  $repertories = $repertoryCRUD->getRepertoriesByTerm($_GET['q']);
  if(is_array($repertories) && count($repertories)>0)
  {
      foreach($repertories as $key=>$repertory)
      {
          $data [] = ['id'=>$repertory['id'], 'text'=>$repertory['name']];
      }
  }
  else
  {
    $data[] = ['id'=>'0', 'text'=>'No repertories found'];
  }
  exit(json_encode($data));
}
