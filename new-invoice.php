<?php
require './classes/InvoiceCRUD.php';
require './classes/InvoiceRowCRUD.php';
require './classes/QuoteCRUD.php';
require './classes/QuoteRowCRUD.php';
require './classes/TaxCRUD.php';
require './classes/RepertoryCRUD.php';
require './classes/ArticleCRUD.php';
require './classes/PaymentCRUD.php';

use InvoiceGenerator\Entity\Invoice;
use InvoiceGenerator\Entity\InvoiceRow;
use InvoiceGenerator\Entity\Quote;
use InvoiceGenerator\Entity\QuoteRow;
use InvoiceGenerator\Entity\InvoicePayment;

    
if(isset($_GET['id']))
{
  $quoteCRUD = new QuoteCRUD();
  $quoteRowCRUD = new QuoteRowCRUD();
  $repertoryCRUD = new RepertoryCRUD();
  $taxCRUD = new TaxCRUD();
  $quote = $quoteCRUD->showQuote($_GET['id']);
  if($quote !== null)
  {
    $transformedQuote = $quote[0];
    $transformedRows = $quoteRowCRUD->getQuoteRowsByQuote($transformedQuote->getId());
    $taxes = [];
    foreach ($transformedRows as $key => $transformedRow) {
        $tax = $taxCRUD->getTaxById($transformedRow->getTax())[0];
        array_push($taxes, $tax);
    }
    $repertory = $repertoryCRUD->getOneById($transformedQuote->getRepertory())[0];
  }
  else {
    echo "Quote to be transformed to invoice not found";
    exit;
  }
}

if(isset($_POST['ref']))
{
  $invoiceCRUD = new InvoiceCRUD();
  $invoiceRowCRUD = new InvoiceRowCRUD();
  $invoice = $invoiceCRUD->getInvoiceByRef($_POST['ref']);
  if($invoice !== null)
  {
    $message = "The reference is already used, you have to generate another one";
    $data = json_encode(array('error'=>true,'message'=>$message));
    echo $data;
    exit;
  }

  $invoice = new Invoice();
  if(isset($transformedQuote)){
    $invoice->setQuote($transformedQuote->getId());
    $transformedQuote->setStatus(3);
    $quoteCRUD->editQuote($transformedQuote, $_GET['id']);
    $message = "Quote transformed into Invoice successfully";
  }
  else {
    $message = "Invoice saved successfully";
  }
  $invoice->setRepertory($_POST['repertory']);
  $invoice->setRef($_POST['ref']);
  $startDate = new \DateTime($_POST['startDate']);
  $endDate = new \DateTime($_POST['endDate']);
  $invoice->setStartDate($startDate);
  $invoice->setEndDate($endDate);
  $invoice->setStatus($_POST['status']);

  $invoice->setPublicNote($_POST['publicNote']);
  $invoice->setPrivateNote($_POST['privateNote']);
  $idInvoice = $invoiceCRUD->saveInvoice($invoice);

  for($i = 0; $i < count($_POST['label']); $i++)
  {
    if(isset(($_POST['label'])[$i]) && !empty(($_POST['label'])[$i]))
    {
      $invoiceRow = new InvoiceRow();
      $invoiceRow->setLabel(($_POST['label'])[$i]);
      $invoiceRow->setQuantity(intval(($_POST['quantity'])[$i]));
      $invoiceRow->setUnityPrice(floatval(($_POST['unityPrice'])[$i]));
      $invoiceRow->setTax(intval(($_POST['tax'])[$i]));
      $invoiceRow->setNote(($_POST['note'])[$i]);
      $invoiceRow->setInvoice($idInvoice);
      $invoiceRowCRUD->saveInvoiceRow($invoiceRow);
    }
  }

  if(intval($_POST['status']) === 3)
  {
    $paymentCRUD = new PaymentCRUD();
    $invoicePayment = new InvoicePayment();
    $date = new \DateTime('now');
    $invoicePayment->setInvoice($idInvoice);
    $invoicePayment->setDate($date);
    $invoicePayment->setAmount($_POST['amount']);
    $invoicePayment->setMethod(isset($_POST['method'])?$_POST['method']:1);
    $invoicePayment->setPublicNote("Paied upon invoice");
    $invoicePayment->setPrivateNote("Paied upon invoice");
    $paymentCRUD->savePayment($invoicePayment);
  }


  $data = json_encode(array('error'=>false,'message'=>$message));
  echo $data;
  exit;
}

?>

<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="bootstrap admin template">
  <meta name="author" content="">
  <title>PDFDocument Generator</title>
  <link rel="apple-touch-icon" href="template/base/assets/images/apple-touch-icon.png">
  <link rel="shortcut icon" href="template/base/assets/images/favicon.ico">
  <!-- Stylesheets -->
  <link rel="stylesheet" href="template/global/css/bootstrap.min.css">
  <link rel="stylesheet" href="template/global/vendor/bootstrap-datepicker/bootstrap-datepicker.css">
  <link rel="stylesheet" href="template/global/css/bootstrap-extend.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="template/base/assets/css/site.min.css">
  <!-- Plugins -->
  <link rel="stylesheet" href="template/global/vendor/animsition/animsition.css">
  <link rel="stylesheet" href="template/global/vendor/asscrollable/asScrollable.css">

  <link rel="stylesheet" href="template/global/vendor/switchery/switchery.css">
  <link rel="stylesheet" href="template/global/vendor/intro-js/introjs.css">
  <link rel="stylesheet" href="template/global/vendor/slidepanel/slidePanel.css">
  <link rel="stylesheet" href="template/global/vendor/flag-icon-css/flag-icon.css">
  <link rel="stylesheet" href="template/global/vendor/summernote/summernote.css">


  <link rel="stylesheet" href="template/global/fonts/web-icons/web-icons.min.css">
  <link rel="stylesheet" href="template/global/fonts/brand-icons/brand-icons.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.11/css/all.css" integrity="sha384-p2jx59pefphTFIpeqCcISO9MdVfIm4pNnsL08A6v5vaQc4owkQqxMV8kg4Yvhaw/" crossorigin="anonymous">


  <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
  <!--[if lt IE 9]>
  <script src="template/global/vendor/html5shiv/html5shiv.min.js"></script>
  <![endif]-->
  <!--[if lt IE 10]>
  <script src="template/global/vendor/media-match/media.match.min.js"></script>
  <script src="template/global/vendor/respond/respond.min.js"></script>
  <![endif]-->
  <!-- Scripts -->
  <script src="template/global/vendor/breakpoints/breakpoints.js"></script>
  <script>
  Breakpoints();
  </script>

</head>
<body class="animsition dashboard">
  <!--[if lt IE 8]>
  <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
  <![endif]-->
  <nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
      data-toggle="menubar">
      <span class="sr-only">Toggle navigation</span>
      <span class="hamburger-bar"></span>
    </button>
    <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
    data-toggle="collapse">
    <i class="icon wb-more-horizontal" aria-hidden="true"></i>
  </button>
  <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
    <span class="navbar-brand-text hidden-xs-down"> PDF Generator</span>
  </div>
  <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search"
  data-toggle="collapse">
  <span class="sr-only">Toggle Search</span>
  <i class="icon wb-search" aria-hidden="true"></i>
</button>
</div>
<div class="navbar-container container-fluid">
  <!-- Navbar Collapse -->
  <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
    <!-- Navbar Toolbar -->
    <ul class="nav navbar-toolbar">
      <li class="nav-item hidden-float" id="toggleMenubar">
        <a class="nav-link" data-toggle="menubar" href="#" role="button">
          <i class="icon hamburger hamburger-arrow-left">
            <span class="sr-only">Toggle menubar</span>
            <span class="hamburger-bar"></span>
          </i>
        </a>
      </li>
      <li class="nav-item hidden-sm-down" id="toggleFullscreen">
        <a class="nav-link icon icon-fullscreen" data-toggle="fullscreen" href="#" role="button">
          <span class="sr-only">Toggle fullscreen</span>
        </a>
      </li>
      <li class="nav-item hidden-float">
        <a class="nav-link icon wb-search" data-toggle="collapse" href="#" data-target="#site-navbar-search"
        role="button">
        <span class="sr-only">Toggle Search</span>
      </a>
    </li>
    <li class="nav-item dropdown dropdown-fw dropdown-mega">
      <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="fade"
      role="button">Mega <i class="icon wb-chevron-down-mini" aria-hidden="true"></i></a>
      <div class="dropdown-menu" role="menu">
        <div class="mega-content">
          <div class="row">
            <div class="col-md-4">
              <h5>UI Kit</h5>
              <ul class="blocks-2">
                <li class="mega-menu m-0">
                  <ul class="list-icons">
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="advanced/animation.html">Animation</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/buttons.html">Buttons</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/colors.html">Colors</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/dropdowns.html">Dropdowns</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/icons.html">Icons</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="advanced/lightbox.html">Lightbox</a>
                    </li>
                  </ul>
                </li>
                <li class="mega-menu m-0">
                  <ul class="list-icons">
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/modals.html">Modals</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/panel-structure.html">Panels</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="structure/overlay.html">Overlay</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/tooltip-popover.html ">Tooltips</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="advanced/scrollable.html">Scrollable</a>
                    </li>
                    <li><i class="wb-chevron-right-mini" aria-hidden="true"></i>
                      <a href="uikit/typography.html">Typography</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <div class="col-md-4">
              <h5>Media
                <span class="badge badge-pill badge-success">4</span>
              </h5>
              <ul class="blocks-3">
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/photos/placeholder.png" alt="..." />
                  </a>
                </li>
              </ul>
            </div>
            <div class="col-md-4">
              <h5 class="mb-0">Accordion</h5>
              <!-- Accordion -->
              <div class="panel-group panel-group-simple" id="siteMegaAccordion" aria-multiselectable="true"
              role="tablist">
              <div class="panel">
                <div class="panel-heading" id="siteMegaAccordionHeadingOne" role="tab">
                  <a class="panel-title" data-toggle="collapse" href="#siteMegaCollapseOne" data-parent="#siteMegaAccordion"
                  aria-expanded="false" aria-controls="siteMegaCollapseOne">
                  Collapsible Group Item #1
                </a>
              </div>
              <div class="panel-collapse collapse" id="siteMegaCollapseOne" aria-labelledby="siteMegaAccordionHeadingOne"
              role="tabpanel">
              <div class="panel-body">
                De moveat laudatur vestra parum doloribus labitur sentire partes, eripuit praesenti
                congressus ostendit alienae, voluptati ornateque accusamus
                clamat reperietur convicia albucius.
              </div>
            </div>
          </div>
          <div class="panel">
            <div class="panel-heading" id="siteMegaAccordionHeadingTwo" role="tab">
              <a class="panel-title collapsed" data-toggle="collapse" href="#siteMegaCollapseTwo"
              data-parent="#siteMegaAccordion" aria-expanded="false"
              aria-controls="siteMegaCollapseTwo">
              Collapsible Group Item #2
            </a>
          </div>
          <div class="panel-collapse collapse" id="siteMegaCollapseTwo" aria-labelledby="siteMegaAccordionHeadingTwo"
          role="tabpanel">
          <div class="panel-body">
            Praestabiliorem. Pellat excruciant legantur ullum leniter vacare foris voluptate
            loco ignavi, credo videretur multoque choro fatemur
            mortis animus adoptionem, bello statuat expediunt naturales.
          </div>
        </div>
      </div>
      <div class="panel">
        <div class="panel-heading" id="siteMegaAccordionHeadingThree" role="tab">
          <a class="panel-title collapsed" data-toggle="collapse" href="#siteMegaCollapseThree"
          data-parent="#siteMegaAccordion" aria-expanded="false"
          aria-controls="siteMegaCollapseThree">
          Collapsible Group Item #3
        </a>
      </div>
      <div class="panel-collapse collapse" id="siteMegaCollapseThree" aria-labelledby="siteMegaAccordionHeadingThree"
      role="tabpanel">
      <div class="panel-body">
        Horum, antiquitate perciperet d conspectum locus obruamus animumque perspici probabis
        suscipere. Desiderat magnum, contenta poena desiderant
        concederetur menandri damna disputandum corporum.
      </div>
    </div>
  </div>
</div>
<!-- End Accordion -->
</div>
</div>
</div>
</div>
</li>
</ul>
<!-- End Navbar Toolbar -->
<!-- Navbar Toolbar Right -->
<!-- <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
<li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" data-animation="scale-up"
aria-expanded="false" role="button">
<span class="flag-icon flag-icon-us"></span>
</a>
<div class="dropdown-menu" role="menu">
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
<span class="flag-icon flag-icon-gb"></span> English</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
<span class="flag-icon flag-icon-fr"></span> French</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
<span class="flag-icon flag-icon-cn"></span> Chinese</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
<span class="flag-icon flag-icon-de"></span> German</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
<span class="flag-icon flag-icon-nl"></span> Dutch</a>
</div>
</li>
<li class="nav-item dropdown">
<a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
data-animation="scale-up" role="button">
<span class="avatar avatar-online">
<img src="template/portraits/5.jpg" alt="...">
<i></i>
</span>
</a>
<div class="dropdown-menu" role="menu">
<a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-user" aria-hidden="true"></i> Profile</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-payment" aria-hidden="true"></i> Billing</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-settings" aria-hidden="true"></i> Settings</a>
<div class="dropdown-divider" role="presentation"></div>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> Logout</a>
</div>
</li>
<li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Notifications"
aria-expanded="false" data-animation="scale-up" role="button">
<i class="icon wb-bell" aria-hidden="true"></i>
<span class="badge badge-pill badge-danger up">5</span>
</a>
<div class="dropdown-menu dropdown-menu-right dropdown-menu-media" role="menu">
<div class="dropdown-menu-header">
<h5>NOTIFICATIONS</h5>
<span class="badge badge-round badge-danger">New 5</span>
</div>
<div class="list-group">
<div data-role="container">
<div data-role="content">
<a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<i class="icon wb-order bg-red-600 white icon-circle" aria-hidden="true"></i>
</div>
<div class="media-body">
<h6 class="media-heading">A new order has been placed</h6>
<time class="media-meta" datetime="2017-06-12T20:50:48+08:00">5 hours ago</time>
</div>
</div>
</a>
<a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<i class="icon wb-user bg-green-600 white icon-circle" aria-hidden="true"></i>
</div>
<div class="media-body">
<h6 class="media-heading">Completed the task</h6>
<time class="media-meta" datetime="2017-06-11T18:29:20+08:00">2 days ago</time>
</div>
</div>
</a>
<a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<i class="icon wb-settings bg-red-600 white icon-circle" aria-hidden="true"></i>
</div>
<div class="media-body">
<h6 class="media-heading">Settings updated</h6>
<time class="media-meta" datetime="2017-06-11T14:05:00+08:00">2 days ago</time>
</div>
</div>
</a>
<a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<i class="icon wb-calendar bg-blue-600 white icon-circle" aria-hidden="true"></i>
</div>
<div class="media-body">
<h6 class="media-heading">Event started</h6>
<time class="media-meta" datetime="2017-06-10T13:50:18+08:00">3 days ago</time>
</div>
</div>
</a>
<a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<i class="icon wb-chat bg-orange-600 white icon-circle" aria-hidden="true"></i>
</div>
<div class="media-body">
<h6 class="media-heading">Message received</h6>
<time class="media-meta" datetime="2017-06-10T12:34:48+08:00">3 days ago</time>
</div>
</div>
</a>
</div>
</div>
</div>
<div class="dropdown-menu-footer">
<a class="dropdown-menu-footer-btn" href="javascript:void(0)" role="button">
<i class="icon md-settings" aria-hidden="true"></i>
</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
All notifications
</a>
</div>
</div>
</li>
<li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Messages"
aria-expanded="false" data-animation="scale-up" role="button">
<i class="icon wb-envelope" aria-hidden="true"></i>
<span class="badge badge-pill badge-info up">3</span>
</a>
<div class="dropdown-menu dropdown-menu-right dropdown-menu-media" role="menu">
<div class="dropdown-menu-header" role="presentation">
<h5>MESSAGES</h5>
<span class="badge badge-round badge-info">New 3</span>
</div>
<div class="list-group" role="presentation">
<div data-role="container">
<div data-role="content">
<a class="list-group-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<span class="avatar avatar-sm avatar-online">
<img src="template/portraits/2.jpg" alt="..." />
<i></i>
</span>
</div>
<div class="media-body">
<h6 class="media-heading">Mary Adams</h6>
<div class="media-meta">
<time datetime="2017-06-17T20:22:05+08:00">30 minutes ago</time>
</div>
<div class="media-detail">Anyways, i would like just do it</div>
</div>
</div>
</a>
<a class="list-group-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<span class="avatar avatar-sm avatar-off">
<img src="template/portraits/3.jpg" alt="..." />
<i></i>
</span>
</div>
<div class="media-body">
<h6 class="media-heading">Caleb Richards</h6>
<div class="media-meta">
<time datetime="2017-06-17T12:30:30+08:00">12 hours ago</time>
</div>
<div class="media-detail">I checheck the document. But there seems</div>
</div>
</div>
</a>
<a class="list-group-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<span class="avatar avatar-sm avatar-busy">
<img src="template/portraits/4.jpg" alt="..." />
<i></i>
</span>
</div>
<div class="media-body">
<h6 class="media-heading">June Lane</h6>
<div class="media-meta">
<time datetime="2017-06-16T18:38:40+08:00">2 days ago</time>
</div>
<div class="media-detail">Lorem ipsum Id consectetur et minim</div>
</div>
</div>
</a>
<a class="list-group-item" href="javascript:void(0)" role="menuitem">
<div class="media">
<div class="pr-10">
<span class="avatar avatar-sm avatar-away">
<img src="template/portraits/5.jpg" alt="..." />
<i></i>
</span>
</div>
<div class="media-body">
<h6 class="media-heading">Edward Fletcher</h6>
<div class="media-meta">
<time datetime="2017-06-15T20:34:48+08:00">3 days ago</time>
</div>
<div class="media-detail">Dolor et irure cupidatat commodo nostrud nostrud.</div>
</div>
</div>
</a>
</div>
</div>
</div>
<div class="dropdown-menu-footer" role="presentation">
<a class="dropdown-menu-footer-btn" href="javascript:void(0)" role="button">
<i class="icon wb-settings" aria-hidden="true"></i>
</a>
<a class="dropdown-item" href="javascript:void(0)" role="menuitem">
See all messages
</a>
</div>
</div>
</li>
<li class="nav-item" id="toggleChat">
<a class="nav-link" data-toggle="site-sidebar" href="javascript:void(0)" title="Chat"
data-url="site-sidebar.tpl">
<i class="icon wb-chat" aria-hidden="true"></i>
</a>
</li>
</ul> -->
<!-- End Navbar Toolbar Right -->
</div>
<!-- End Navbar Collapse -->
<!-- Site Navbar Seach -->
<div class="collapse navbar-search-overlap" id="site-navbar-search">
  <form role="search">
    <div class="form-group">
      <div class="input-search">
        <i class="input-search-icon wb-search" aria-hidden="true"></i>
        <input type="text" class="form-control" name="site-search" placeholder="Search...">
        <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search"
        data-toggle="collapse" aria-label="Close"></button>
      </div>
    </div>
  </form>
</div>
<!-- End Site Navbar Seach -->
</div>
</nav>
<div class="site-menubar">
  <div class="site-menubar-body">
    <div>
      <div>
        <ul class="site-menu" data-plugin="menu">
          <li class="site-menu-item has-sub">
            <a href="javascript:void(0)">
              <i class="fa fa-file" aria-hidden="true"></i>
              <span class="site-menu-title">Sales</span>
              <span class="site-menu-arrow"></span>
            </a>
            <ul class="site-menu-sub">
              <li class="site-menu-item">
                <a class="animsition-link" href="list-invoices.php">
                  <span class="site-menu-title">List invoices</span>
                </a>
              </li>
              <li class="site-menu-item">
                <a class="animsition-link" href="<?php echo ($_SERVER['PHP_SELF']) ?>">
                  <span class="site-menu-title">Add invoice</span>
                </a>
              </li>
              <li class="site-menu-item">
                <a class="animsition-link" href="list-quotes.php">
                  <span class="site-menu-title">List quotes</span>
                </a>
              </li>
              <li class="site-menu-item">
                <a class="animsition-link" href="new-quote.php">
                  <span class="site-menu-title">Add quote</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="site-menubar-footer">
    <a href="javascript: void(0);" class="fold-show" data-placement="top" data-toggle="tooltip"
    data-original-title="Settings">
    <span class="icon wb-settings" aria-hidden="true"></span>
  </a>
  <a href="javascript: void(0);" data-placement="top" data-toggle="tooltip" data-original-title="Lock">
    <span class="icon wb-eye-close" aria-hidden="true"></span>
  </a>
  <a href="javascript: void(0);" data-placement="top" data-toggle="tooltip" data-original-title="Logout">
    <span class="icon wb-power" aria-hidden="true"></span>
  </a>
</div>
</div>
<div class="site-gridmenu">
  <div>
    <div>
      <ul>
        <li>
          <a href="apps/mailbox/mailbox.html">
            <i class="icon wb-envelope"></i>
            <span>Mailbox</span>
          </a>
        </li>
        <li>
          <a href="apps/calendar/calendar.html">
            <i class="icon wb-calendar"></i>
            <span>Calendar</span>
          </a>
        </li>
        <li>
          <a href="apps/contacts/contacts.html">
            <i class="icon wb-user"></i>
            <span>Contacts</span>
          </a>
        </li>
        <li>
          <a href="apps/media/overview.html">
            <i class="icon wb-camera"></i>
            <span>Media</span>
          </a>
        </li>
        <li>
          <a href="apps/documents/categories.html">
            <i class="icon wb-order"></i>
            <span>Documents</span>
          </a>
        </li>
        <li>
          <a href="apps/projects/projects.html">
            <i class="icon wb-image"></i>
            <span>Project</span>
          </a>
        </li>
        <li>
          <a href="apps/forum/forum.html">
            <i class="icon wb-chat-group"></i>
            <span>Forum</span>
          </a>
        </li>
        <li>
          <a href="index.html">
            <i class="icon wb-dashboard"></i>
            <span>Dashboard</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- Page -->
<div class="page">
  <div class="page-header">
    <h1 class="page-title"></h1>
    <p></p>
  </div>
  <div class="page-content container-fluid">
    <div class="row" data-plugin="matchHeight" data-by-row="true">
      <div class="col-lg-12">
        <div class="panel">
          <div class="panel-heading">
            <h3 class="panel-title">New invoice</h3>
          </div>
          <div class="panel-body">
            <?php if (isset($transformedQuote)){ ?>
              <form name="invoiceForm" action="<?php echo($_SERVER['PHP_SELF']) ?>" id="newFormInvoice" method="post">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="ref">
                      Ref.
                    </label>
                    <div class="input-group">
                      <input type="text" value="<?php echo ($transformedQuote->getRef()); ?>" name="ref" id="ref" class="form-control form-control-sm" placeholder="Generate reference">
                      <span class="input-group-btn" style="height: 2.288rem">
                        <button type="button" class="btn btn-secondary" id="btn-generate" name="button" style="height: 100%">
                          <i class="fa fa-random"></i>
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      Repertory
                    </label>
                    <select class="form-control form-control-sm js-data-repertories" name="repertory" id="repertory"></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      Start date
                    </label>
                    <input type="text" value="<?php echo ($transformedQuote->getCreationDate()); ?>" name="startDate" data-plugin="datepicker" id="startDate" class="form-control form-control-sm" placeholder="Pick start date">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      End date
                    </label>
                    <input type="text" value="<?php echo ($transformedQuote->getDueDate()); ?>" name="endDate" data-plugin="datepicker" id="endDate" class="form-control form-control-sm" placeholder="Pick end date">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      Status
                    </label>
                    <select name="status" id="status" class="form-control form-control-sm">
                      <option selected="" disabled="">
                        -- choose --
                      </option>
                      <option value="1">
                        Draft
                      </option>
                      <option value="2">
                        Sent
                      </option>
                      <option value="3">
                        Paied
                      </option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      Public note
                    </label>
                    <textarea rows="8" cols="80" name="publicNote" id="publicNote" class="form-control form-control-sm" placeholder="Enter public note ..."><?php echo ($transformedQuote->getPublicNote()); ?></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      Private note
                    </label>
                    <textarea rows="8" cols="80" name="privateNote" id="privateNote" class="form-control form-control-sm" placeholder="Enter private note ..."><?php echo ($transformedQuote->getPrivateNote()); ?></textarea>
                  </div>
                </div>
                <div class="col-md-12">
                  <table class="table table-responsive table-bordered">
                    <thead>
                      <tr>
                        <th>N°</th>
                        <th>Label</th>
                        <th>Quantity</th>
                        <th>Tax</th>
                        <th>Unity price</th>
                        <th>Note</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody id="invoiceRows">
                      <?php
                      foreach ($transformedRows as $i => $transformedRow) {
                        ?>
                        <tr id="invoiceRow<?php echo ($i + 1); ?>">
                          <td><?php echo ($i + 1); ?></td>
                          <td>
                            <input type="hidden" name="label[]" id="label<?php echo ($i + 1); ?>" value="">
                            <select class="form-control form-control-sm js-data-articles" data-id="<?php echo ($i + 1); ?>" id="js-data-articles<?php echo ($i + 1); ?>" name="js-data-articles[]"></select>
                          </td>
                          <td>
                            <input type="number" name="quantity[]" value="<?php echo ($transformedRow->getQuantity()); ?>" min="1" data-id="<?php echo ($i + 1); ?>" id="quantity<?php echo ($i + 1); ?>" class="form-control form-control-sm">
                          </td>
                          <td>
                            <select class="form-control form-control-sm js-data-taxes" data-id="<?php echo ($i + 1); ?>" id="js-data-taxes<?php echo ($i + 1); ?>" name="tax[]"></select>
                          </td>
                          <td>
                            <input type="text" name="unityPrice[]" value="<?php echo (floatval($transformedRow->getUnityPrice())); ?>" id="unityPrice<?php echo ($i + 1); ?>" data-id="<?php echo ($i + 1); ?>" class="form-control form-control-sm">
                          </td>
                          <td>
                            <input type="text" name="note[]" value="<?php echo ($transformedRow->getNote()); ?>" class="form-control form-control-sm">
                          </td>
                          <td>
                            <?php
                              $taxPercentage = $transformedRow->getTax();
                              $subtotal = $transformedRow->getQuantity() * $transformedRow->getUnityPrice();
                              $tax = ($taxPercentage * $subtotal)/100;
                              $subtotal += $tax;
                            ?>
                            <input type="text" value="<?php echo($subtotal); ?>" name="subtotal[]" data-id="<?php echo ($i + 1); ?>" id="subtotal<?php echo ($i + 1); ?>" class="form-control form-control-sm" readonly>
                          </td>
                        </tr>
                        <?php
                      }

                      if(count($transformedRows)<5)
                      {
                        for($i = count($transformedRows) + 1; $i <= 5; $i++)
                        {
                          ?>
                          <tr id="invoiceRow<?php echo ($i); ?>">
                            <td><?php echo ($i); ?></td>
                            <td>
                              <input type="hidden" name="label[]" id="label<?php echo ($i); ?>" value="">
                              <select class="form-control form-control-sm js-data-articles" data-id="<?php echo ($i); ?>" id="js-data-articles<?php echo ($i); ?>" name="js-data-articles[]"></select>

                            </td>
                            <td>
                              <input type="number" name="quantity[]" min="1" data-id="<?php echo ($i); ?>" id="quantity<?php echo ($i); ?>" class="form-control form-control-sm">
                            </td>
                            <td>
                              <select class="form-control form-control-sm js-data-taxes" data-id="<?php echo ($i); ?>" id="js-data-taxes<?php echo ($i); ?>" name="tax[]"></select>
                            </td>
                            <td>
                              <input type="text" name="unityPrice[]" id="unityPrice<?php echo ($i); ?>" data-id="<?php echo ($i); ?>" class="form-control form-control-sm">
                            </td>
                            <td>
                              <input type="text" name="note[]" class="form-control form-control-sm">
                            </td>
                            <td>
                              <input type="text" name="subtotal[]" data-id="<?php echo ($i); ?>" id="subtotal<?php echo ($i); ?>" class="form-control form-control-sm" readonly>
                            </td>
                          </tr>
                          <?php
                        }}
                        ?>
                      </tbody>

                    </tbody>

                    <tfoot>
                      <tr class="btn-options">
                        <th colspan="5">
                          <button class="btn btn-success" id="btn-add" type="button">
                            <i class="fa fa-plus"></i>
                          </button>
                          <button class="btn btn-danger invisible" id="btn-remove" type="button">
                            <i class="fa fa-minus"></i>
                          </button>
                        </th>
                        <th>
                          Total
                        </th>
                        <td>
                          <span class="total">
                            <?php echo($transformedQuote->getAmount()); ?>
                          </span>
                          <input type="hidden" name="amount" id="amount" value="<?php echo($transformedQuote->getAmount()); ?>" class="form-control form-control-sm" placeholder="Enter the amount">
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>


                <div class="col-md-12">
                  <div class="form-group text-right">
                    <button class="btn btn-primary" id="saveInvoice" name="saveInvoice">
                      Save invoice <i class="white-500 wb wb-file"></i>
                    </button>
                  </div>
                </div>
              </div>
            </form>

            <?php } else{ ?>
            <form name="invoiceForm" action="<?php echo($_SERVER['PHP_SELF']) ?>" id="newFormInvoice" method="post">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="ref">
                      Ref.
                    </label>
                    <div class="input-group">
                      <input type="text" name="ref" id="ref" class="form-control form-control-sm" placeholder="Generate reference">
                      <span class="input-group-btn" style="height: 2.288rem">
                        <button type="button" class="btn btn-secondary" id="btn-generate" name="button" style="height: 100%">
                          <i class="fa fa-random"></i>
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      Repertory
                    </label>
                    <select class="form-control form-control-sm js-data-repertories" name="repertory" id="repertory"></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      Start date
                    </label>
                    <input type="text" name="startDate" data-plugin="datepicker" id="startDate" class="form-control form-control-sm" placeholder="Pick start date">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      End date
                    </label>
                    <input type="text" name="endDate" data-plugin="datepicker" id="endDate" class="form-control form-control-sm" placeholder="Pick end date">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      Status
                    </label>
                    <select name="status" id="status" class="form-control form-control-sm">
                      <option selected="" disabled="">
                        -- choose --
                      </option>
                      <option value="1">
                        Draft
                      </option>
                      <option value="2">
                        Sent
                      </option>
                      <option value="3">
                        Paied
                      </option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      Public note
                    </label>
                    <textarea  name="publicNote" id="publicNote" class="form-control form-control-sm" placeholder="Enter public note ..." rows="8" cols="80"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      Private note
                    </label>
                    <textarea name="privateNote" id="privateNote" class="form-control form-control-sm" placeholder="Enter private note ..." rows="8" cols="80"></textarea>

                  </div>
                </div>
                <div class="col-md-12">
                  <table class="table table-responsive table-bordered">
                    <thead>
                      <tr>
                        <th>N°</th>
                        <th>Label</th>
                        <th>Quantity</th>
                        <th>Tax</th>
                        <th>Unity price</th>
                        <th>Note</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody id="invoiceRows">
                      <?php
                      for ( $i = 1; $i <= 5; $i++ )
                      {
                        ?>
                        <tr id="invoiceRow<?php echo ($i); ?>">
                          <td><?php echo ($i); ?></td>
                          <td>
                            <input type="hidden" name="label[]" id="label<?php echo ($i); ?>" value="">
                            <select class="form-control form-control-sm js-data-articles" data-id="<?php echo ($i); ?>" id="js-data-articles<?php echo ($i); ?>" name="js-data-articles[]"></select>

                          </td>
                          <td>
                            <input type="number" name="quantity[]" min="1" data-id="<?php echo ($i); ?>" id="quantity<?php echo ($i); ?>" class="form-control form-control-sm">
                          </td>
                          <td>
                            <select class="form-control form-control-sm js-data-taxes" data-id="<?php echo ($i); ?>" id="js-data-taxes<?php echo ($i); ?>" name="tax[]"></select>

                          </td>
                          <td>
                            <input type="text" name="unityPrice[]" id="unityPrice<?php echo ($i); ?>" data-id="<?php echo ($i); ?>" class="form-control form-control-sm">
                          </td>
                          <td>
                            <input type="text" name="note[]" class="form-control form-control-sm">
                          </td>
                          <td>
                            <input type="text" name="subtotal[]" data-id="<?php echo ($i); ?>" id="subtotal<?php echo ($i); ?>" class="form-control form-control-sm" readonly>
                          </td>
                        </tr>
                        <?php
                      }
                      ?>
                    </tbody>

                    <tfoot>
                      <tr class="btn-options">
                        <th colspan="5">
                          <button class="btn btn-success" id="btn-add" type="button">
                            <i class="fa fa-plus"></i>
                          </button>
                          <button class="btn btn-danger invisible" id="btn-remove" type="button">
                            <i class="fa fa-minus"></i>
                          </button>
                        </th>
                        <th>
                          Total
                        </th>
                        <td>
                          <span class="total">
                            0.00
                          </span>
                          <input type="hidden" name="amount" id="amount" class="form-control form-control-sm" placeholder="Enter the amount">
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>


                <div class="col-md-12">
                  <div class="form-group text-right">
                    <button class="btn btn-primary" id="saveInvoice" name="saveInvoice">
                      Save invoice <i class="white-500 wb wb-file"></i>
                    </button>
                  </div>
                </div>
              </div>
            </form>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Page -->
<!-- Footer -->
<footer class="site-footer">
  <div class="site-footer-legal">© 2017 <a href="http://themeforest.net/item/remark-responsive-bootstrap-admin-template/11989202">Remark</a></div>
  <div class="site-footer-right">
    Crafted with <i class="red-600 wb wb-heart"></i> by <a href="http://themeforest.net/user/amazingSurge">amazingSurge</a>
  </div>
</footer>
<script src="template/global/vendor/babel-external-helpers/babel-external-helpers.js"></script>
<script src="template/global/vendor/jquery/jquery.js"></script>
<script src="template/global/vendor/tether/tether.js"></script>
<script src="template/global/vendor/bootstrap/bootstrap.js"></script>
<script src="template/global/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="template/global/vendor/animsition/animsition.js"></script>
<script src="template/global/vendor/mousewheel/jquery.mousewheel.js"></script>
<script src="template/global/vendor/asscrollbar/jquery-asScrollbar.js"></script>
<script src="template/global/vendor/asscrollable/jquery-asScrollable.js"></script>
<script src="template/global/vendor/ashoverscroll/jquery-asHoverScroll.js"></script>
<!-- Plugins -->
<script src="template/global/vendor/switchery/switchery.min.js"></script>
<script src="template/global/vendor/intro-js/intro.js"></script>
<script src="template/global/vendor/screenfull/screenfull.js"></script>
<script src="template/global/vendor/slidepanel/jquery-slidePanel.js"></script>
<script src="template/global/vendor/summernote/summernote.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<!-- Scripts -->
<script src="template/global/js/State.js"></script>
<script src="template/global/js/Component.js"></script>
<script src="template/global/js/Plugin.js"></script>
<script src="template/global/js/Base.js"></script>
<script src="template/global/js/Config.js"></script>
<script src="template/base/assets/js/Section/Menubar.js"></script>
<script src="template/base/assets/js/Section/GridMenu.js"></script>
<script src="template/base/assets/js/Section/Sidebar.js"></script>
<script src="template/base/assets/js/Section/PageAside.js"></script>
<script src="template/base/assets/js/Plugin/menu.js"></script>
<script src="template/global/js/config/colors.js"></script>
<script src="template/base/assets/js/config/tour.js"></script>
<script>
Config.set('assets', 'template/base/assets');
</script>
<!-- Page -->
<script src="template/base/assets/js/Site.js"></script>
<script src="template/global/js/Plugin/asscrollable.js"></script>
<script src="template/global/js/Plugin/slidepanel.js"></script>
<script src="template/global/js/Plugin/switchery.js"></script>
<script src="template/global/js/Plugin/summernote.js"></script>
<script src="template/base/assets/examples/js/forms/editor-summernote.js"></script>
<script type="text/javascript" src="ajax/crud-invoice.js"></script>


<script type="text/javascript">


$(document).ready(function(){

  $('#newFormInvoice').on('submit',function(e){
    e.preventDefault();

    if($('#amount').val() === "")
    {
      alert('You have to add at least one article');
      return false;
    }
    saveInvoice($(this), "<?php echo($_SERVER['PHP_SELF']); if(isset($_GET['id'])){echo('?id='.$_GET['id']);} ?>");
  });


  $('#btn-add').on('click', function(){
    var size = ($('#invoiceRows').children('tr').size());
    var select = '<select class="form-control form-control-sm js-data-articles" data-id="'+(size + 1)+'" id="js-data-articles'+(size + 1)+'" name="js-data-articles[]"></select>';
    var tr ='<tr id="invoiceRow'+(size + 1)+'">'+
    '<td>'+(size + 1)+'</td>'+
    '<td>'+
    '<input type="hidden" name="label[]" id="label'+(size + 1)+'" value="">'+
    select +
    '</td>'+
    '<td>'+
    '<input type="number" min="1" name="quantity[]" data-id="'+(size + 1)+'" id="quantity'+(size + 1)+'" class="form-control form-control-sm">'+
    '</td>'+
    '<td>'+
    '<select class="form-control form-control-sm js-data-taxes" data-id="'+(size + 1)+'" id="js-data-taxes'+(size + 1)+'" name="tax[]"></select>'+
    '</td>'+
    '<td>'+
    '<input type="text" name="unityPrice[]" id="unityPrice'+(size + 1)+'" data-id="'+(size + 1)+'" class="form-control form-control-sm">'+
    '</td>'+
    '<td>'+
    '<input type="text" name="note[]" class="form-control form-control-sm">'+
    '</td>'+
    '<td>'+
    '<input type="text" name="subtotal[]" data-id="'+(size + 1)+'" id="subtotal'+(size + 1)+'" class="form-control form-control-sm" readonly>'+
    '</td>'+
    '</tr>';
    $('#invoiceRows').append(tr);

    $(document).find('.js-data-articles').select2({
      placeholder: 'Select an article',
      minimumInputLength: 1,
      width: '100%',
      allowClear: true,
      ajax: {
        url: './ajax/articles.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
              return {
                  q: params.term // search term
              };
        },
        processResults: function (data) {
          return {
            results: data
          };
        }
      }
    });

    $(document).find('.js-data-taxes').select2({
      placeholder: 'Select a tax',
      minimumInputLength: 1,
      width: '100%',
      allowClear: true,
      ajax: {
        url: './ajax/taxes.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
              return {
                  q: params.term // search term
              };
          },
        processResults: function (data) {
          return {
            results: data
          };
        }
      }
    });

    if($('#btn-remove').hasClass('invisible'))
    {
      $('#btn-remove').removeClass('invisible');
    }
  });

  $('#btn-remove').on('click', function(){
    var size = $('#invoiceRows').children('tr').size();
    if( size > 5 )
    {
      $('#invoiceRows').children('#invoiceRow'+size).remove();
      if(size - 1 === 5)
      {
        if(!$('#btn-remove').hasClass('invisible'))
        {
          $('#btn-remove').addClass('invisible');
        }
      }
    }
  });


  $(document).on('focus','[name^="subtotal"]', function(){
    var subtotal = 0, total = 0, quantity, tax, unityPrice;
    var id = $(this).data('id');
    quantity = parseInt($('#quantity'+id).val());
    tax = parseFloat($('#js-data-taxes'+id).children('option:selected').text());
    unityPrice = parseFloat($('#unityPrice'+id).val());
    subtotal = ((unityPrice * quantity) + (tax * (unityPrice * quantity))/100);
    if(subtotal)
    {
      $(this).val(subtotal);
    }

    $('[name^="subtotal"]').each(function(){
      if($(this).val() !== "")
      {
        total += parseFloat($(this).val());
      }
    });

    $('.total').text( total > 0 ? total : '0.00' );
    $('#amount').val( total > 0 ? total : '0.00' );
  });

  $(document).on('change','[name^="quantity"]', function(){
    var subtotal = 0, total = 0, quantity, tax, unityPrice;
    var id = $(this).data('id');
    quantity = parseInt($(this).val());
    tax = parseFloat($('#js-data-taxes'+id).children('option:selected').text());
    unityPrice = parseFloat($('#unityPrice'+id).val());
    subtotal = ((unityPrice * quantity) + (tax * (unityPrice * quantity))/100);
    if(subtotal)
    {
      $('#subtotal'+id).val(subtotal);
    }

    $('[name^="subtotal"]').each(function(){
      if($(this).val() !== "")
      {
        total += parseFloat($(this).val());
      }
    });

    $('.total').text( total > 0 ? total : '0.00' );
    $('#amount').val( total > 0 ? total : '0.00' );

  });

  $(document).on('blur','[name^="quantity"]', function(){
    var subtotal = 0, total = 0, quantity, tax, unityPrice;
    var id = $(this).data('id');
    quantity = parseInt($(this).val());
    tax = parseFloat($('#js-data-taxes'+id).children('option:selected').text());
    unityPrice = parseFloat($('#unityPrice'+id).val());
    subtotal = ((unityPrice * quantity) + (tax * (unityPrice * quantity))/100);
    if(subtotal)
    {
      $('#subtotal'+id).val(subtotal);
    }

    $('[name^="subtotal"]').each(function(){
      if($(this).val() !== "")
      {
        total += parseFloat($(this).val());
      }
    });

    $('.total').text( total > 0 ? total : '0.00' );
    $('#amount').val( total > 0 ? total : '0.00' );

  });

  $(document).on('change','[name^="tax"]', function(){
    var subtotal = 0, total = 0, quantity, tax, unityPrice;
    var id = $(this).data('id');
    quantity = parseInt($('#quantity'+id).val());
    tax = parseFloat($(this).children('option:selected').text());
    unityPrice = parseFloat($('#unityPrice'+id).val());
    subtotal = ((unityPrice * quantity) + (tax * (unityPrice * quantity))/100);

    if(subtotal)
    {
      $('#subtotal'+id).val(subtotal);
    }

    $('[name^="subtotal"]').each(function(){
      if($(this).val() !== "")
      {
        total += parseFloat($(this).val());
      }
    });

    $('.total').text( total > 0 ? total : '0.00' );
    $('#amount').val( total > 0 ? total : '0.00' );

  });

  $(document).on('blur','[name^="tax"]', function(){
    var subtotal = 0, total = 0, quantity, tax, unityPrice;
    var id = $(this).data('id');
    quantity = parseInt($('#quantity'+id).val());
    tax = parseFloat($(this).children('option:selected').text());
    unityPrice = parseFloat($('#unityPrice'+id).val());
    subtotal = ((unityPrice * quantity) + (tax * (unityPrice * quantity))/100);

    if(subtotal)
    {
      $('#subtotal'+id).val(subtotal);
    }

    $('[name^="subtotal"]').each(function(){
      if($(this).val() !== "")
      {
        total += parseFloat($(this).val());
      }
    });

    $('.total').text( total > 0 ? total : '0.00' );
    $('#amount').val( total > 0 ? total : '0.00' );

  });

  $(document).on('blur','[name^="unityPrice"]', function(){
    var subtotal = 0, total = 0, quantity, tax, unityPrice;
    var id = $(this).data('id');
    quantity = parseInt($('#quantity'+id).val());
    tax = parseFloat($('#js-data-taxes'+id).children('option:selected').text());
    unityPrice = parseFloat($(this).val());
    subtotal = ((unityPrice * quantity) + (tax * (unityPrice * quantity))/100);
    if(subtotal)
    {
      $('#subtotal'+id).val(subtotal);
    }

    $('[name^="subtotal"]').each(function(){
      if($(this).val() !== "")
      {
        total += parseFloat($(this).val());
      }
    });

    $('.total').text( total > 0 ? total : '0.00' );
    $('#amount').val( total > 0 ? total : '0.00' );

  });


  $('.js-data-repertories').select2({
    placeholder: 'Select a repertory',
    minimumInputLength: 1,
    width: '100%',
    allowClear: true,
    ajax: {
      url: './ajax/repertories.php',
      dataType: 'json',
      delay: 250,
      data: function (params) {
            return {
                q: params.term // search term
            };
      },
      processResults: function (data) {
        return {
          results: data
        };
      }
    }
  });

  $('.js-data-articles').select2({
    placeholder: 'Select an article',
    minimumInputLength: 1,
    width: '100%',
    allowClear: true,
    ajax: {
      url: './ajax/articles.php',
      dataType: 'json',
      delay: 250,
      data: function (params) {
            return {
                q: params.term // search term
            };
      },
      processResults: function (data) {
        return {
          results: data
        };
      }
    }
  });

  $('.js-data-taxes').select2({
    placeholder: 'Select a tax',
    minimumInputLength: 1,
    width: '100%',
    allowClear: true,
    ajax: {
      url: './ajax/taxes.php',
      dataType: 'json',
      delay: 250,
      data: function (params) {
            return {
                q: params.term // search term
            };
        },
      processResults: function (data) {
        return {
          results: data
        };
      }
    }
  });




$(document).on('change', '.js-data-articles', function(e) {
  var unityPrice = $(this).select2('data')[0].sellingPriceDutyFree;
  var label = $(this).select2('data')[0].text;
  var id = $(this).data('id');

  $('#label'+id).val(label);
  $('#unityPrice'+id).val(unityPrice);
});



$('#btn-generate').on('click', function(){
  var number = "0123456789";
  var ref = "";
  for(var i = 0 ; i < 16 ; i++ )
  {
    ref += number[Math.floor(Math.random() * 10)];
  }
  $('#ref').val(ref);
});




<?php if(isset($transformedQuote)){ ?>

var newOption = $("<option></option>").val("<?php echo ($repertory['id']); ?>").text("<?php echo ($repertory['name']); ?>");
$(".js-data-repertories").append(newOption).trigger('change');

<?php
  foreach($transformedRows as $i=>$transformedRow):
  {
?>
  var articleOption = $("<option></option>").val("<?php echo ($transformedRow->getId()); ?>").text("<?php echo ($transformedRow->getLabel()); ?>")
  $("#js-data-articles<?php echo ($i + 1); ?>").append(articleOption).trigger('change');
  $('#label<?php echo ($i + 1); ?>').val("<?php echo ($transformedRow->getLabel()); ?>");

  var taxOption = $("<option></option>").val("<?php echo ($taxes[$i]['id']); ?>").text("<?php echo ($taxes[$i]['amount']); ?>")
  $("#js-data-taxes<?php echo ($i + 1); ?>").append(taxOption).trigger('change');
<?php
  }endforeach;
?>

<?php } ?>

});
</script>
</body>
</html>
