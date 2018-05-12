<?php
use InvoiceGenerator\Entity\InvoicePayment;
require './classes/RepertoryCRUD.php';
$repertoryCRUD = new RepertoryCRUD();

if(isset($_GET['format']))
{
  require './vendor/autoload.php';
  require './classes/InvoiceCRUD.php';
  require './classes/InvoiceRowCRUD.php';
  require './classes/PaymentCRUD.php';
  require './classes/TaxCRUD.php';

  $paymentCRUD = new PaymentCRUD();
  $invoiceCRUD = new InvoiceCRUD();
  $taxCRUD = new TaxCRUD();
  $invoiceRowCRUD = new InvoiceRowCRUD();

  $col =array(
      0   =>  'id',
      1   =>  'repertory',
      2   =>  'quote',
      3   =>  'ref',
      4   =>  'quote',
      5   =>  'startDate',
      6   =>  'endDate',
      7   =>  'status',
      8   =>  'publicnote',
      9   =>  'privatenote'
  );  //create column like table in database

  $invoices = $invoiceCRUD->getInvoices();
  $recordsTotal = is_array($invoices)?count($invoices):0;

  $invoices = $invoiceCRUD->getOrderedInvoices($col[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['start'], $_POST['length']);

  $taxes = [];
  $data = [];

  if(is_array($invoices))
  {
    foreach($invoices as $key=>$invoice):
      {
        $invoiceRows = $invoiceRowCRUD->getInvoiceRowsByInvoiceAsArray($invoice['id']);
        $paiedAmount = $paymentCRUD->getPaiedAmountByInvoice($invoice['id']);
        foreach($invoiceRows as $invoiceRow):
        {
          $tax = $taxCRUD->getTaxById($invoiceRow['tax'])[0];
          array_push($taxes, $tax);
        }endforeach;

        $data[$key] = ['invoice'=>$invoice, 'paiedAmount'=>$paiedAmount, 'invoiceRows'=>$invoiceRows, 'taxes'=>$taxes];
      }endforeach;
  }

  //Search
  if(!empty($_POST['search']['value'])){
      $invoices = $invoiceCRUD->getInvoicesByCriteria(
        $_POST['search']['value'],
        $col[$_POST['order'][0]['column']], 
        $_POST['order'][0]['dir'], 
        $_POST['start'], 
        $_POST['length']
      );
      $taxes = [];
      $data = [];
      if(is_array($invoices))
      {
        foreach($invoices as $key=>$invoice):
          {
            $invoiceRows = $invoiceRowCRUD->getInvoiceRowsByInvoiceAsArray($invoice['id']);
            $paiedAmount = $paymentCRUD->getPaiedAmountByInvoice($invoice['id']);
              foreach($invoiceRows as $invoiceRow):
                {
                  $tax = $taxCRUD->getTaxById($invoiceRow['tax'])[0];
                  array_push($taxes, $tax);
                }endforeach;
        
              $data[$key] = ['invoice'=>$invoice, 'paiedAmount'=>$paiedAmount, 'invoiceRows'=>$invoiceRows, 'taxes'=>$taxes];
          }endforeach;
          $recordsTotal = count($invoices);
      }
  }
 
  $invoices = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $recordsTotal ?? 0,
    "recordsFiltered" => $recordsTotal ?? 0,
    "data" => $data ?? []
  ];
  exit(json_encode($invoices));
}

if (isset($_GET['paymentInvoiceId']))
{
  require './classes/PaymentCRUD.php';
  $paymentCRUD = new PaymentCRUD();
  $listPayments = $paymentCRUD->getPaymentsByInvoice($_GET['paymentInvoiceId']);
  $data = json_encode($listPayments);
  echo $data;
  exit;
}

if(isset($_POST['invoiceId']))
{
    require './classes/InvoiceCRUD.php';
    require './classes/InvoiceRowCRUD.php';
    require './classes/PaymentCRUD.php';
    $invoiceCRUD = new InvoiceCRUD();
    $invoiceRowCRUD = new InvoiceRowCRUD();
    $paymentCRUD = new PaymentCRUD();
    $paymentCRUD->deletePaymentByInvoice($_POST['invoiceId']);
    $invoiceRowCRUD->deleteInvoiceRowByInvoice($_POST['invoiceId']);
    $message = $invoiceCRUD->deleteInvoice($_POST['invoiceId']);
    $data = json_encode(array('message'=>$message));
    echo $data;
    exit;
}

if(isset($_POST['paymentProcessType']))
{
  require './classes/PaymentCRUD.php';
  $paymentCRUD = new PaymentCRUD();
  if($_POST['paymentProcessType'] === 'newPayment')
  {
    $invoicePayment = new InvoicePayment();
    $date = new \DateTime($_POST['date']);
    $invoicePayment->setInvoice($_POST['paymentInvoiceId']);
    $invoicePayment->setDate($date);
    $invoicePayment->setAmount($_POST['amount']);
    $invoicePayment->setMethod(isset($_POST['method'])?$_POST['method']:1);
    $invoicePayment->setPublicNote(isset($_POST['publicNote'])?$_POST['publicNote']:"");
    $invoicePayment->setPrivateNote(isset($_POST['privateNote'])?$_POST['privateNote']:"");
    $message = $paymentCRUD->savePayment($invoicePayment);
  }
  else {
    $invoicePayment = $paymentCRUD->getPaymentById($_POST['paymentId'])[0];
    $date = new \DateTime($_POST['date']);
    $invoicePayment->setDate($date);
    $invoicePayment->setAmount($_POST['amount']);
    $invoicePayment->setMethod(isset($_POST['method'])?$_POST['method']:1);
    $invoicePayment->setPublicNote(isset($_POST['publicNote'])?$_POST['publicNote']:"");
    $invoicePayment->setPrivateNote(isset($_POST['privateNote'])?$_POST['privateNote']:"");
    $message = $paymentCRUD->editPayment($invoicePayment);
  }

  $data = json_encode(array('message'=>$message));
  echo $data;
  exit;
}

if (isset($_POST['deletePaymentId']))
{
  require './classes/PaymentCRUD.php';
  $paymentCRUD = new PaymentCRUD();
  $message = $paymentCRUD->deletePayment($_POST['deletePaymentId']);
  $data = json_encode(array('message'=>$message));
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
  <title>Invoice Generator</title>
  <link rel="apple-touch-icon" href="template/base/assets/images/apple-touch-icon.png">
  <link rel="shortcut icon" href="template/base/assets/images/favicon.ico">
  <!-- Stylesheets -->
  <link rel="stylesheet" href="./template/global/css/bootstrap.min.css">
  <link rel="stylesheet" href="template/global/vendor/bootstrap-datepicker/bootstrap-datepicker.css">
  <link rel="stylesheet" href="./template/global/css/bootstrap-extend.min.css">
  <link rel="stylesheet" href="./template/base/assets/css/site.min.css">
  <!-- Plugins -->
  <link rel="stylesheet" href="./template/global/vendor/animsition/animsition.css">
  <link rel="stylesheet" href="./template/global/vendor/asscrollable/asScrollable.css">
  <link rel="stylesheet" href="./template/global/vendor/switchery/switchery.css">
  <link rel="stylesheet" href="./template/global/vendor/intro-js/introjs.css">
  <link rel="stylesheet" href="./template/global/vendor/slidepanel/slidePanel.css">
  <link rel="stylesheet" href="./template/global/vendor/flag-icon-css/flag-icon.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-bs4/dataTables.bootstrap4.css">


  <link rel="stylesheet" href="./template/global/vendor/datatables.net-fixedheader-bs4/dataTables.fixedheader.bootstrap4.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-fixedcolumns-bs4/dataTables.fixedcolumns.bootstrap4.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-rowgroup-bs4/dataTables.rowgroup.bootstrap4.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-select-bs4/dataTables.select.bootstrap4.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.css">
  <link rel="stylesheet" href="./template/global/vendor/datatables.net-buttons-bs4/dataTables.buttons.bootstrap4.css">
  <link rel="stylesheet" href="./template/base/assets/examples/css/tables/datatable.css">

  <!-- Fonts -->
  <link rel="stylesheet" href="./template/global/fonts/font-awesome/font-awesome.css">
  <link rel="stylesheet" href="./template/global/fonts/web-icons/web-icons.min.css">
  <link rel="stylesheet" href="./template/global/fonts/brand-icons/brand-icons.min.css">
  <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
  <!--[if lt IE 9]>
  <script src="./template/global/vendor/html5shiv/html5shiv.min.js"></script>
  <![endif]-->
  <!--[if lt IE 10]>
  <script src="./template/global/vendor/media-match/media.match.min.js"></script>
  <script src="./template/global/vendor/respond/respond.min.js"></script>
  <![endif]-->
  <!-- Scripts -->
  <script src="./template/global/vendor/breakpoints/breakpoints.js"></script>
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
                    <img class="w-full" src="template/global/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/global/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/global/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/global/photos/placeholder.png" alt="..." />
                  </a>
                </li>
                <li>
                  <a class="thumbnail m-0" href="javascript:void(0)">
                    <img class="w-full" src="template/global/photos/placeholder.png" alt="..." />
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
<ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
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
</ul>
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
                <a class="animsition-link" href="<?php echo ($_SERVER['PHP_SELF']) ?>">
                  <span class="site-menu-title">List invoices</span>
                </a>
              </li>
              <li class="site-menu-item">
                <a class="animsition-link" href="new-invoice.php">
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
  <div class="page-content container-fluid">
    <div class="row" data-plugin="matchHeight" data-by-row="true">
      <div class="col-lg-12">
        <div class="panel">

          <header class="panel-heading">
            <h3 class="panel-title">Invoice list</h3>
          </header>
          <div class="panel-body">
            <table class="table table-hover dataTable table-striped table-bordered w-full" id="dataTable">
              <thead>
                <tr>
                  <th>Ref</th>
                  <th>Quote</th>
                  <th>Start date</th>
                  <th>End date</th>
                  <th class="sum">Total</th>
                  <th class="sum">Paied</th>
                  <th class="sum">Balance</th>
                  <th>Status</th>
                  <th width="10%">Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>
                    <input type="text" placeholder="Ref" class="form-control form-control" style="border: none">
                  </th>
                  <th>
                    <input type="text" placeholder="Quote" class="form-control form-control" style="border: none">
                  </th>
                  <th>
                    <input type="text" placeholder="Start date" class="form-control form-control" style="border: none">
                  </th>
                  <th>
                    <input type="text" placeholder="End date" class="form-control form-control" style="border: none">
                  </th>
                  <th>Total</th>
                  <th>Paied</th>
                  <th>Balance</th>
                  <th>
                    <input type="text" placeholder="Status" class="form-control form-control" style="border: none">
                  </th>
                  <th>Actions</th>
                </tr>
                <tr>
                  <th colspan="9">
                    <input type="text" placeholder="Type to search the table" id="customGlobalSearch" class="form-control form-control" style="border: none">
                  </th>
                </tr>
              </tfoot>
            </table>
            <div style="padding-right: 30px; margin-bottom: 20px">
              <a href="new-invoice.php" class="btn btn-primary"><i class="white-600 wb wb-plus"></i> Create new invoice</a>
            </div>
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



<div class="modal fade" id="deleteModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Delete</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <i class="icon wb-warning"></i> Warning, this process is irreversible !<br>
        Do you really want to delete this invoice ?
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <form id="formDelete" method="post">
          <input type="hidden" id="invoiceId" name="invoiceId">
          <button type="submit" id="deleteInvoice" name="deleteInvoice" class="btn btn-danger">Yes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deletePaymentModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Delete</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <i class="icon wb-warning"></i> Warning, this process is irreversible !<br>
        Do you really want to delete this payment ?
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <form id="formPaymentDelete" method="post">
          <input type="hidden" id="deletePaymentId" name="deletePaymentId">
          <button type="submit" id="deletePayment" name="deletePayment" class="btn btn-danger">Yes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="mailModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Send quote as an attachment</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form id="formMail" method="post">
      <div class="modal-body">

        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control form-control-sm" name="" value="">
              </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="email">E-mail</label>
              <input type="email" name="email" id="email" class="form-control form-control-sm" name="" value="">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="name">Subject</label>
              <input type="text" name="subject" id="subject" class="form-control form-control-sm" name="" value="">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="body">Message</label>
              <textarea name="body" class="form-control form-control-sm" rows="8" cols="80" placeholder="Write your message here"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <input type="hidden" id="sentInvoiceId" name="sentInvoiceId">
          <button type="submit" id="sendInvoice" name="sendInvoice" class="btn btn-success">Yes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="paymentModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title" id="titlePaymentProcess">Add payment</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form id="formPayment" method="post">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
              <div class="form-group">
                <label for="date">Date</label>
                <input type="text" name="date" data-plugin="datepicker" id="date" class="form-control form-control-sm" placeholder="Pick the date" value="">
              </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="method">Payment Method</label>
              <input type="text" name="method" id="method" class="form-control form-control-sm" value="">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="amount">Amount</label>
              <input type="text" name="amount" id="amount" class="form-control form-control-sm" placeholder="0.00" name="" value="">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="privateNote">Private Note</label>
              <textarea id="privateNote" name="privateNote" class="form-control form-control-sm" rows="8" cols="80" placeholder="Write your private note here"></textarea>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group">
              <label for="publicNote">Public Note</label>
              <textarea id="publicNote" name="publicNote" class="form-control form-control-sm" rows="8" cols="80" placeholder="Write your public note here"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <input type="hidden" id="paymentInvoiceId" name="paymentInvoiceId">
          <input type="hidden" id="paymentId" name="paymentId">
          <input type="hidden" id="paymentProcessType" name="paymentProcessType">
          <button type="submit" id="savePayment" name="savePayment" class="btn btn-success">Yes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="listPaymentsModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">list payments</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table id="listPayments" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount</th>
                  <th width="50%">Public Note</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

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
<script src="template/global/vendor/datatables.net/jquery.dataTables.js"></script>
<script src="template/global/vendor/datatables.net-bs4/dataTables.bootstrap4.js"></script>

<script src="template/global/vendor/datatables.net-fixedheader/dataTables.fixedHeader.js"></script>
<script src="template/global/vendor/datatables.net-fixedcolumns/dataTables.fixedColumns.js"></script>
<script src="template/global/vendor/datatables.net-rowgroup/dataTables.rowGroup.js"></script>
<script src="template/global/vendor/datatables.net-scroller/dataTables.scroller.js"></script>
<script src="template/global/vendor/datatables.net-select-bs4/dataTables.select.js"></script>
<script src="template/global/vendor/datatables.net-responsive/dataTables.responsive.js"></script>
<script src="template/global/vendor/datatables.net-responsive-bs4/responsive.bootstrap4.js"></script>
<script src="template/global/vendor/datatables.net-buttons/dataTables.buttons.js"></script>
<script src="template/global/vendor/datatables.net-buttons/buttons.html5.js"></script>
<script src="template/global/vendor/datatables.net-buttons/buttons.flash.js"></script>
<script src="template/global/vendor/datatables.net-buttons/buttons.print.js"></script>
<script src="template/global/vendor/datatables.net-buttons/buttons.colVis.js"></script>
<script src="template/global/vendor/datatables.net-buttons-bs4/buttons.bootstrap4.js"></script>
<script src="template/global/js/Plugin/pdfmake.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/vfs_fonts.js" integrity="sha256-wvH/UThD/fVD6sz1bAWX7JDW5Nx1TBdhegX8IHX20hA=" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-colvis-1.5.1/b-html5-1.5.1/b-print-1.5.1/r-2.2.1/sl-1.2.5/datatables.min.js"></script>



<script src="template/global/vendor/asrange/jquery-asRange.min.js"></script>
<script src="template/global/vendor/bootbox/bootbox.js"></script>
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
<script src="template/global/js/Plugin/datatables.js"></script>
<script src="template/base/assets/examples/js/tables/datatable.js"></script>



<script type="text/javascript" src="ajax/crud-invoice.js"></script>
<script type="text/javascript" src="ajax/crud-payment.js"></script>
<script type="text/javascript">
$(document).ready(function () {
  function action(data, type, full, meta) {

    
    var id = full.invoice.id;
    
    var actions = '<a href="generate-pdf.php?type=invoice&id=' + id + '" target="_blank" class="dropdown-item action-item"  role="menuitem" style="width: auto; text-decoration: none">' +
    '<i class="fa fa-fw fa-eye"></i> View invoice</a>' +
    '<a href="update-invoice.php?id=' + id + '" class="dropdown-item action-item" role="menuitem" style="width: auto; text-decoration: none">' +
    '<i class="fa fa-fw fa-edit"></i> Update invoice</a>' +
    '<a href="#mailModal" data-id="' + id + '" data-toggle="modal" class="dropdown-item action-item" role="menuitem" style="width: auto; text-decoration: none">' +
    '<i class="fa fa-fw fa-envelope"></i> Send in mail</a>' +
    '<a href="#paymentModal" data-id="' + id + '" data-toggle="modal" class="dropdown-item action-item" role="menuitem" style="width: auto; text-decoration: none">' +
    '<i class="fa fa-fw fa-money"></i> Add payment</a>' +
    '<a href="#listPaymentsModal" data-id="' + id + '" data-toggle="modal" class="dropdown-item action-item" role="menuitem" style="width: auto; text-decoration: none">' +
    '<i class="fa fa-fw fa-money"></i> View payments</a>' +
    '<a href="#deleteModal" data-id="' + id + '" data-toggle="modal" class="dropdown-item action-item" role="menuitem" style="width: auto; text-decoration: none">' +
    '<i class="fa fa-fw fa-trash"></i> Delete invoice</a>';

    var btnOptions = '<div class="dropdown">'+
      '<button type="button" class="btn btn-default btn-sm btn-outline dropdown-toggle" data-toggle="dropdown">Options</button>'+
      '<div class="dropdown-menu">'+
        actions +
      '</div>'+
    '</div>';


    return btnOptions;
  }

  var dataTable = $('#dataTable').DataTable(
    {
      "destroy": true,
      "order": [0, 'asc'],
      "dom": '<"text-center"<"btn-group"B>><"clear"><"row"<"col-md-6"l><"col-md-6 pr0"p>r>t<"row"<"col-md-6"i><"col-md-6"p>><"clear">',
      "buttons": [
          { extend: 'copyHtml5', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
          { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
          { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
          { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true, 
          exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ] } },
          { extend: 'colvis', text: 'Columns'},
      ],
      "pageLength": 10,
      "processing": true, 
      "serverSide": true,
      "ajax": {
        url: "<?php echo $_SERVER['PHP_SELF'] ?>?format=json",
        type: "POST"
      },
      "columnDefs": [
        {"orderable": true, "targets": 0},
        {
          "targets": 4,
          "orderable": false,
          "data": function ( row, type, val, meta ) {
            var total = 0, tax, unityPrice, quantity, subtotal;
            for(var i = 0; i < row.invoiceRows.length; i++)
            {
              tax = row.taxes[i].amount;
              unityPrice =  row.invoiceRows[i].unityprice;
              quantity =  row.invoiceRows[i].quantity;
              subtotal = parseFloat(unityPrice) * parseInt(quantity);
              tax = parseFloat((tax * subtotal)/100);
              total += parseFloat(subtotal + tax);
            }
            return  total;
          }
        },
        {
          "targets": 5,
          "orderable": false,
          "data": function ( row, type, val, meta ) {
            return (row.paiedAmount[0].paiedAmount?row.paiedAmount[0].paiedAmount:"0.00");
          }
        },
        {
          "targets": 6,
          "orderable": false,
          "data": function ( row, type, val, meta ) {
            var total = 0, tax, unityPrice, quantity, subtotal;
            for(var i = 0; i < row.invoiceRows.length; i++)
            {
              tax = row.taxes[i].amount;
              unityPrice =  row.invoiceRows[i].unityprice;
              quantity =  row.invoiceRows[i].quantity;
              subtotal = parseFloat(unityPrice) * parseInt(quantity);
              tax = parseFloat((tax * subtotal)/100);
              total += parseFloat(subtotal + tax);
            }
            return (row.paiedAmount[0].paiedAmount?(total - row.paiedAmount[0].paiedAmount):total);
          }
        }
      ],
      "columns": [
        {
          data: "ref",
          render: function ( data, type, row, meta ) {
            return row.invoice.ref;
          }
        },
        {
          data: "quote",
          render: function ( data, type, row, meta ) {
            return row.invoice.quote;
          }
        },
        {
          data: "startDate",
          render: function ( data, type, row, meta ) {
            var date = new Date(row.invoice.startDate);
            var year = date.getFullYear();
            var day = date.getDate()>9?date.getDate():'0'+date.getDate();
            var month = date.getMonth()>9?date.getMonth():'0'+date.getMonth();
            return year+'-'+month+'-'+day;
          }
        },
        {
          data: "endDate",
          render: function ( data, type, row, meta ) {
            var date = new Date(row.invoice.endDate);
            var year = date.getFullYear();
            var day = date.getDate()>9?date.getDate():'0'+date.getDate();
            var month = date.getMonth()>9?date.getMonth():'0'+date.getMonth();
            return year+'-'+month+'-'+day;
          }
        },
        {
          render: function ( data, type, row, meta ) {
            var total = 0, tax, unityPrice, quantity, subtotal;
            for(var i = 0; i < row.invoiceRows.length; i++)
            {
              tax = row.taxes[i].amount;
              unityPrice =  row.invoiceRows[i].unityprice;
              quantity =  row.invoiceRows[i].quantity;
              subtotal = parseFloat(unityPrice) * parseInt(quantity);
              tax = parseFloat((tax * subtotal)/100);
              total += parseFloat(subtotal + tax);
            }
            return  total.toFixed(2);
          }
        },
        {
          render: function ( data, type, row, meta ) {
            return (row.paiedAmount[0].paiedAmount?row.paiedAmount[0].paiedAmount:"0.00");
          }
        },
        {
          render: function ( data, type, row, meta ) {
            var total = 0, tax, unityPrice, quantity, subtotal;
            for(var i = 0; i < row.invoiceRows.length; i++)
            {
              tax = row.taxes[i].amount;
              unityPrice =  row.invoiceRows[i].unityprice;
              quantity =  row.invoiceRows[i].quantity;
              subtotal = parseFloat(unityPrice) * parseInt(quantity);
              tax = parseFloat((tax * subtotal)/100);
              total += parseFloat(subtotal + tax);
            }
            return (row.paiedAmount[0].paiedAmount?(total - row.paiedAmount[0].paiedAmount).toFixed(2):total.toFixed(2));
          }
        },
        {
          data: "status",
          render: function ( data, type, row, meta ) {
            var status = "", statusColor = "";
            if(row.invoice.status == 1)
            {
              status = "Draft";
              statusColor = "danger";
            }
            else if (row.invoice.status == 2) {
              status = "Sent";
              statusColor = "success";
            }
            else if (row.invoice.status == 3) {
              status = "Paied";
              statusColor = "primary";
            }
            return '<span class="badge badge-'+statusColor+'">'+status+'</span>';
          }
        },
        {
          orderable: false,
          searchable: false,
          data: null,
          render: function (data, type, full, meta) {
            return action(data, type, full, meta);
          }
        }
      ],
      "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
             
            api.columns('.sum', { page: 'current'}).every( function () {
              var sum = this
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
              
              this.footer().innerHTML = sum.toFixed(2);
            } );
        }
    }
  );


  $(document).on('click','[href="#deleteModal"]', function(){
    var _id = $(this).data('id');
    $('#invoiceId').val(_id);
  });

  $('#formDelete').on('submit',function(e){
    e.preventDefault();
    $('#deleteModal').modal('toggle');
    var _action = "<?php echo ($_SERVER['PHP_SELF']) ?>";
    deleteInvoice($(this), _action);
    dataTable.ajax.reload();
  });


  $(document).on('click','[href="#mailModal"]', function(){
    var _id = $(this).data('id');
    $('#sentInvoiceId').val(_id);
  });

  $('#formMail').on('submit',function(e){
    e.preventDefault();
    $('#mailModal').modal('toggle');
    var _action = "send-mail.php";
    sendMail($(this), _action);
  });


  function resetPayment()
  {
    $('#date').val('');
    $('#amount').val('');
    $('#method').val('');
    $('#publicNote').val('');
    $('#privateNote').val('');
  }

  $(document).on('click','[href="#paymentModal"]', function(){
    if($(this).data('id'))
    {
      $('#paymentProcessType').val('newPayment');
      var _id = $(this).data('id');
      $('#paymentInvoiceId').val(_id);
    }
    else {
      $('#listPaymentsModal').modal('toggle');
      $('#paymentProcessType').val('updatePayment');
      $('#paymentId').val($(this).data('paymentid'));
      $('#date').val($(this).data('date'));
      $('#amount').val($(this).data('amount'));
      $('#method').val($(this).data('method'));
      $('#publicNote').val($(this).data('publicnote'));
      $('#privateNote').val($(this).data('privatenote'));
    }
  });

  $('#savePayment').on('click', function(){
    var amount = $('#amount').val();
    var date = $('#date').val();
    var method = $('#date').val();
    if(amount === "" || date === "")
    {
      alert('Please fill all the fields');
      return false;
    }
  });

  $('#formPayment').on('submit',function(e){
    e.preventDefault();
    $('#paymentModal').modal('toggle');
    var _action = "<?php echo ($_SERVER['PHP_SELF']) ?>";
    savePayment($(this), _action);
    resetPayment();
    dataTable.ajax.reload();
  });

  $(document).on('click','[href="#listPaymentsModal"]', function(){
    var id = $(this).data('id')
    var _action = "<?php echo($_SERVER['PHP_SELF']) ?>?paymentInvoiceId="+id;
    getPayments($(this), _action);
  });

  $(document).on('click','[href="#deletePaymentModal"]', function(){
    var _id = $(this).data('id');
    $('#deletePaymentId').val(_id);
    $('#listPaymentsModal').modal('toggle');
  });

  $('#formPaymentDelete').on('submit',function(e){
    e.preventDefault();
    $('#deletePaymentModal').modal('toggle');
    var _action = "<?php echo ($_SERVER['PHP_SELF']) ?>";
    deletePayment($(this), _action);
  });

  $('#customGlobalSearch').on('keyup', function(){
    dataTable.search( this.value ).draw();
  });

  dataTable.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              dataTable
                    .search( this.value )
                    .draw();
            }
            else
            {
              dataTable.search('').draw();
            }
        } );
    } );

});
</script>
</body>
</html>
