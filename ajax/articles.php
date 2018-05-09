<?php


if(isset($_GET['q']) && !empty($_GET['q']))
{
  require '../classes/ArticleCRUD.php';
  $articleCRUD = new ArticleCRUD();
  $articles = $articleCRUD->getArticlesByTerm($_GET['q']);
  if(is_array($articles) && count($articles)>0)
  {
      foreach($articles as $key=>$article)
      {
          $data [] = 
          [
            'id'=>$article['id'], 
            'text'=>$article['label'], 
            'sellingPriceDutyFree'=>$article['sellingPriceDutyFree'], 
            'buyingprice'=>$article['buyingprice']
          ];
      }
  }
  else
  {
    $data[] = ['id'=>'0', 'text'=>'No articles found'];
  }
  exit(json_encode($data));
}
