<h1>Pobieranie danych z pf.pl</h1>
<?php
    require "vendor/autoload.php";
    use Masterminds\HTML5;
    
    if(file_exists('dane/pf/temp.txt')){
        $fread = fopen('dane/pf/temp.txt', 'r');
        $temp = fread($fread, filesize('dane/pf/temp.txt'));
        fclose($fread);
    }
    $temp = explode(';',$temp);
    
    if(file_exists('dane/pf/kategorie.csv')){
        $fread = fopen('dane/pf/kategorie.csv', 'r');
        $kategorie = fread($fread, filesize('dane/pf/kategorie.csv'));
        $kategorie = explode(';',$kategorie);
        fclose($fread);
    } else {
        $kat = 0;
    }
    $liczbakategorii = count($kategorie);
    
if(isset($_GET['nrkategorii'])){
    $nrkategorii = $_GET['nrkategorii'];
} elseif($_GET['restart']=='true') {
    $nrkategorii = $temp[0];
} else {
    $nrkategorii = 0;
}

echo 'Kategoria: '. $nrkategorii .' z '. $liczbakategorii . '<br/><br/>';

$nrstrony = 1;
$lastpage = '--';
if(isset($_GET['nrstrony'])){
    $nrstrony = $_GET['nrstrony'];
} elseif ($_GET['restart']=='true'){
    $nrstrony = $temp[1];
}
//var_dump($kategorie);
$url = $kategorie[$nrkategorii] . '/firmy,' . $nrstrony . '.html';
$kat = explode('/',$url);
    echo 'Nazwa kategorii: '. $kat[3] . '<br/><br/>';
    echo " Adres obecny: <b>".$url."</b><br/><br/>";
$procent = $nrkategorii/($liczbakategorii/100); 
echo $procent.'% wszystkich kategorii.';
?>
<div class="progress">
  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $procent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $procent; ?>%">
    <span class="sr-only"><?php echo $procent; ?>% Complete (success)</span>
  </div>
</div>

<?php
    
    
if($strona = file_get_contents($url)){
    
    $html5 = new HTML5();
    libxml_use_internal_errors(true);
    $dom = $html5->loadHTML($strona);
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
    
    $plik = '';

    $tempPlik = $dane;
    $fwrite = fopen('dane/pf/' . $kat[3] . '.csv', 'a');
    fwrite($fwrite, $tempPlik);
    fclose($fwrite);
    $nrstrony++;
    if($kat = 0){
        'Brak kategorii. Wygeneruj najpierw kategorie następnie spróbuj ponownie.';
    }else {
        echo '<META http-equiv="refresh" content="3;URL=http://localhost/17062016/index.php?site=pfdane&nrstrony='. $nrstrony .'&nrkategorii=' . $nrkategorii . '">';
    }
    $temp = $nrstrony.';'.$nrkategorii;
    $fwrite = fopen('dane/temp.txt', 'w');
    fwrite($fwrite, $temp);
    fclose($fwrite);
    
    
        
} else {
    echo "GOTOWE - KONIEC<br/><br/>";
    echo "Przechodzę do następnej kategorii";
    $nrstrony=1;
    $nrkategorii++;
    if($kat = 0){
        'Brak kategorii. Wygeneruj najpierw kategorie następnie spróbuj ponownie.';
    }else {
        echo '<META http-equiv="refresh" content="3;URL=http://localhost/17062016/index.php?site=pfdane&nrstrony='. $nrstrony .'&nrkategorii=' . $nrkategorii . '">';
    }
}
?>