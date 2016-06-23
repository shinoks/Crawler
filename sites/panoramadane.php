<h1>Pobieranie danych z pf.pl</h1>
<?php
    require "vendor/autoload.php";
    use Masterminds\HTML5;
    $pf = new panoramaFirm();
    
if(!isset($_GET['nrstrony']) && !isset($_GET['nrkategorii'])){

    $temp = $pf->getPreviousDownload();

    echo "Ostatnie pobieranie skończone na ".$temp[1]." kategoria, ".$temp[0]." strona<br/><br/>";
    echo '<form action="index.php" method="GET">
      <div class="form-group">
        <label for="nrkategorii">Nr kategorii</label>
        <input type="text" class="form-control" name="nrkategorii" id="nrkategorii" value="' . $temp[1] . '">
      </div>
      <div class="form-group">
        <label for="nrstrony">Nr strony</label>
        <input type="text" class="form-control" name="nrstrony" id="nrstrony" value="' . $temp[0] . '">
      </div>
        
        <input type="hidden" value="pfdane" name="site"/>
        
      <button type="submit" class="btn btn-primary">Zacznij pobieranie</button>
    </form><br/><br/>';
    
} else {

    $category = $pf->getCategories();
    
    $categoryNumber = $pf->getCategoryNumber();

    echo 'Kategoria: '. $categoryNumber .' z '. $categoryNumber . '<br/><br/>';

    $nrstrony = 1;
    $lastpage = '--';
    if(isset($_GET['nrstrony'])){
        $nrstrony = $_GET['nrstrony'];
    }
    //var_dump($kategorie);
    $url = $category[$categoryNumber] . '/firmy,' . $nrstrony . '.html';
    $kat = explode('/',$url);
        echo 'Nazwa kategorii: '. $kat[3] . '<br/><br/>';
        echo " Adres obecny: <b>".$url."</b><br/><br/>";
    $procent = $categoryNumber/(count($category)/100); 
    echo $procent.'% wszystkich kategorii.';
    ?>
    <div class="progress">
      <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $procent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $procent; ?>%">
        <span class="sr-only"><?php echo $procent; ?>% Complete (success)</span>
      </div>
    </div>

    <?php
        
        $strona = $pf->getContents($url);

    if($strona != '0'){
        $dom = $pf->loadHTML($strona);
        
        $links = $dom->getElementsByTagName('a');
        $dane = '';
        $a = 0;
        foreach ($links as $link) {
         
            $tempClass = $link->getAttribute("class");
            $tempTitle = $link->getAttribute("title");
            
            if ($tempClass == 'noLP companyName colorBlue addax addax-cs_hl_hit_company_name_click'){
                $dane .= "\r\n";
                echo "<br/>";
                $dane .= $link->nodeValue.';';
                echo $link->nodeValue;
                $a++;
            }

            if ($tempClass == 'icon-phone addax addax-cs_hl_hit_phone_number_click noLP'){
                $dane .= $link->nodeValue;
                echo  $link->nodeValue;
                $a = 0;
            }
            //echo $tempTitle;
            if ($tempTitle == 'Przejdź do ostatniej strony'){
                $lastpage = $link->nodeValue;
                echo "<br/><br/> Ostatnia strona: <b>".$link->getAttribute("href").'</b><br/><br/>';
            }
        }
        
        $pf->setData($dane,$kat[3]);

        $nrstrony++;
        if($kat = 0){
            'Brak kategorii. Wygeneruj najpierw kategorie następnie spróbuj ponownie.';
        }else {
            echo '<META http-equiv="refresh" content="3;URL=http://localhost/pobieraniedanych/index.php?site=pfdane&nrstrony='. $nrstrony .'&nrkategorii=' . $categoryNumber . '">';
        }
        $pf->setPreviousDownload($nrstrony,$categoryNumber);
            
    } else {
        echo "GOTOWE - KONIEC<br/><br/>";
        echo "Przechodzę do następnej kategorii";
        $nrstrony=1;
        $categoryNumber++;
        if($kat = 0){
            'Brak kategorii. Wygeneruj najpierw kategorie następnie spróbuj ponownie.';
        }else {
            echo '<META http-equiv="refresh" content="3;URL=http://localhost/pobieraniedanych/index.php?site=pfdane&nrstrony='. $nrstrony .'&nrkategorii=' . $categoryNumber . '">';
        }
    }
}
?>