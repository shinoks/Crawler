<?php
if(!isset($_GET['site'])){
    $start = new PanoramaFirm();
    echo $start->getStartSite();
} elseif ($_GET['site']=='pfdata'){
    //include('sites/panoramadane.php');
    $panoramadane = new PanoramaFirm();
    echo $panoramadane->getDataSite();
} elseif ($_GET['site']=='pfcategories'){
    $panoramakategorie = new PanoramaFirm();
    echo $panoramakategorie->getCategorySite();
} elseif ($_GET['site']=='pfinstruction'){
    $instruction = new PanoramaFirm();
    echo $instruction->getInstructionSite();
} elseif ($_GET['site']=='pfdownloadeddata'){
    $panoramadata = new PanoramaFirm();
    echo $panoramadata->getDownloadedDataSite();
} elseif ($_GET['site']=='contact'){
    $contact = new DefaultController();
    echo $contact->getContactSite();
}  elseif ($_GET['site']=='pktcategories'){
    $pktcategories = new PolskieKsiazkiTelefoniczne();
    echo $pktcategories->getCategorySite();
}  elseif ($_GET['site']=='pktdata'){
    $pktdata = new PolskieKsiazkiTelefoniczne();
    echo $pktdata->getDataSite();
} else {
    include('sites/start.php');
}