<?php
    require "vendor/autoload.php";
    use Masterminds\HTML5;
    

$nr = 0;
$branze = ['Biuro','Budownictwo','Dom i ogród','Dzieci','Finanse i ubezpieczenia','Instytucje, urzędy','Motoryzacja i transport','Nauka','Odzież i tekstylia','Przemysł i energetyka',
'Rolnictwo i leśnictwo','Rozrywka i rekreacja','Telekomunikacja i Internet','Turystyka','Usługi dla firm,','Usługi dla każdego','Zdrowie i uroda','Żywność i używki'];
$branzeLink = ['z','b','c','u','r','f','h','i','g','k','j','l','t','m','n','p','s','a'];
$kategorie = '';
$plik = '';
$lastpage = 18;
if(isset($_GET['nr'])){
    $nr = $_GET['nr'];
}
$url = 'http://panoramafirm.pl/biuro,'.$branzeLink[$nr].'/branze.html';

echo " Adres obecny: <b>".$url."</b><br/><br/>";
    
if($strona = file_get_contents($url)){
    
    $html5 = new HTML5();
    libxml_use_internal_errors(true);
    $dom = $html5->loadHTML($strona);
    $links = $dom->getElementsByTagName("article");
    
    $article = $html5->saveHTML($links);
    $dom = $html5->loadHTML($article);
    $links = $dom->getElementsByTagName("a");
    
    foreach ($links as $link) {
        $kategorie .= $link->getAttribute('href').';';
    }
    
    if(file_exists('dane/kategorie.csv')){
        $fread = fopen('dane/kategorie.csv', 'r');
        $plik = fread($fread, filesize('dane/kategorie.csv'));
        fclose($fread);
    }

    $tempPlik = $plik.$kategorie;
    $fwrite = fopen('dane/kategorie.csv', 'w');
    fwrite($fwrite, $tempPlik);
    fclose($fwrite);
    echo $kategorie;
    $nr++;
    echo '<META http-equiv="refresh" content="5;URL=http://localhost/17062016/pobieraniekategorii.php?nr='. $nr .'">';
        
} else {
    echo "GOTOWE - KONIEC";
}