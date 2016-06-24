<?php

use Masterminds\HTML5;
    
class panoramaFirm 
{
    function __construct()
    {
        //$this->view = $_GET['view'];
        $this->twig = new Twig_Environment( new Twig_Loader_Filesystem("./view"),
            array( "cache" => "./view/cache" ) );
            
        if(!file_exists('dane/pf/')){
            mkdir('dane/pf/');
        }
    }
    
    
    public function getPreviousDownload()
    {
        if(file_exists('dane/pf/temp.txt')){
            $fread = fopen('dane/pf/temp.txt', 'r');
            $temp = fread($fread, filesize('dane/pf/temp.txt'));
            fclose($fread);
        }else {
            $temp = '1;0';
        }
        $temp = explode(';',$temp);
        
        return $temp;
    }
    
    
    public function setPreviousDownload($nrstrony,$nrkategorii)
    {
        $previous = $nrstrony.';'.$nrkategorii;
        $fwrite = fopen('dane/pf/temp.txt', 'w');
        if(fwrite($fwrite, $previous)){
            $temp = 'ok';
        } else {
            throw new Exception('Błąd przy zapisywaniu danych poprzedniego pobierania');
            $temp = 'error';
        }
        fclose($fwrite);
        
        return $temp;
    }
    
    public function setData($data,$categoryName)
    {
        $fwrite = fopen('dane/pf/' . $categoryName . '.csv', 'a');
        if(fwrite($fwrite, $data)){
            $temp = 'ok';
        } else {
            throw new Exception('Błąd przy zapisywaniu danych');
            $temp = 'error';
        }
        fclose($fwrite);
        
        
        return $temp;
    }
    
    public function getCategories()
    {
        if(file_exists('dane/pf/categories.csv')){
            $fread = fopen('dane/pf/categories.csv', 'r');
            $category = fread($fread, filesize('dane/pf/categories.csv'));
            $category = explode(';',$category);
            fclose($fread);
        } else {
            throw new Exception('Brak pobranych kategorii');
        }
        
        return $category;
    }
    
    public function getContents($url)
    {
        if($strona = @file_get_contents($url)){
        } else {
           $strona = '0';
        }

        return $strona;
    }
    
    public function loadHTML($html)
    {
        $html5 = new HTML5();
        libxml_use_internal_errors(true);
        $dom = $html5->loadHTML($html);
        
        return $dom;
        
    }
    
    public function saveHTML($html)
    {
        $html5 = new HTML5();
        libxml_use_internal_errors(true);
        $dom = $html5->saveHTML($html);
        
        return $dom;
        
    }
    
    public function getCategoryNumber()
    {
        if(isset($_GET['nrkategorii'])){
            $categoryNumber = $_GET['nrkategorii'];
        } else {
            $categoryNumber = 0;
        }
        
        return $categoryNumber;
    }
    
    public function getPageNumber()
    {
        if(isset($_GET['nrstrony'])){
            $pagenumber = $_GET['nrstrony'];
        }else {
            $pagenumber = 1;
        }
        
        return $pagenumber;
    }
    
    public function getStartSite()
    {
        return $this->twig->render("index.html.twig", array());
    }
    
    private function getDownloadedData($catalog)
    {
        $files = '';
        $preg = $catalog.'/*{*.csv}';
        foreach(glob($preg, GLOB_BRACE) as $file)
          if($file != '.' && $file != '..') 
            $files .= $file.';';
        
        $files = str_replace(''.$catalog.'/','',$files);
        $ready = explode(';',$files);
        
        return $ready;
    }
    
    public function getDataSite()
    {
        $temp = $this->getPreviousDownload();
        $previousCategory = $temp[1];
        $previousPage = $temp[0];
        
        if($this->getCategoryNumber() == '0' && $this->getPageNumber() == '1' && !isset($_GET['start'])){
            return $this->twig->render("panoramafirm/pobieranieDanych.html.twig", array(
                    'pagenumber' => $this->getPageNumber(),
                    'categorynumber' => $this->getCategoryNumber(),
                    'previouscategory' => $previousCategory,
                    'previouspage' => $previousPage
                )
            );
        } else {
            $categories = $this->getCategories();
            $page = $this->getPageNumber();
            $categoriesCount = count($categories);
            $categoryNumber = $this->getCategoryNumber();
            $url = $categories[$this->getCategoryNumber()] . '/firmy,' . $page . '.html';
            $kat = explode('/',$url);
            $percent = round($categoryNumber/($categoriesCount/100),2); 
            $downloaded = $this->getContents($url);
            $info = ' ';
            $error = ' ';
            $dane = ' ';
            $ht = ' ';
            if($downloaded != '0'){
                $dom = $this->loadHTML($downloaded);
                $links = $dom->getElementsByTagName('a');
                
                $a = 0;
                foreach ($links as $link) {
                    
                    $tempClass = $link->getAttribute("class");
                    $tempTitle = $link->getAttribute("title");
                    
                    if ($tempClass == 'noLP companyName colorBlue addax addax-cs_hl_hit_company_name_click'){
                        $dane .= "\r\n";
                        $ht .= "<br/>";
                        $dane .= $link->nodeValue.';';
                        $ht .= $link->nodeValue;
                        $a++;
                    }
                    
                    if ($tempClass == 'icon-phone addax addax-cs_hl_hit_phone_number_click noLP'){
                        $dane .= $link->nodeValue;
                        $ht .=  $link->nodeValue;
                        $a = 0;
                    }
                    
                    if ($tempTitle == 'Przejdź do ostatniej strony'){
                        $lastpage = $link->nodeValue;
                        $ht .= "<br/><br/> Ostatnia strona: <b>".$link->getAttribute("href").'</b><br/><br/>';
                    }
                }
                $this->setData($dane,$kat[3]);

                $page2 =$page+1;
                
                $this->setPreviousDownload($page2,$categoryNumber);
                
                if(count($kat) == 1){
                    $error ='category';
                }else {
                    header( "refresh:1;url=index.php?site=pfdata&nrstrony=". $page2 ."&nrkategorii=" . $categoryNumber."" ); 
                }
                
            } else {
                $info = "categoryend";
                $page2=1;
                $categoryNumber++;
                if($kat = 0){
                    $error ='category';
                }else {
                    header( "refresh:1;url=index.php?site=pfdata&nrstrony=". $page2 ."&nrkategorii=" . $categoryNumber."" ); 
                    $this->setPreviousDownload($page2,$categoryNumber);
                }
            }
            
            return $this->twig->render("panoramafirm/pobieranieDanychReady.html.twig", array(
                    'pagenumber' => $page,
                    'categorynumber' => $categoryNumber,
                    'categorycount' => $categoriesCount,
                    'previouscategory' => $previousCategory,
                    'previouspage' => $previousPage,
                    'categoryname' => $kat[3],
                    'url' => $url,
                    'progress' => $percent,
                    'html' => $ht,
                    'info' => $info,
                    'error' => $error
                )
            );
        }
        
    }
    
    public function getCategorySite()
    {
        $file = 'dane/pf/categories.csv';

        if(file_exists($file) && !isset($_GET['restart']) && !isset($_GET['go'])){
            $info = 1;
            return $this->twig->render("panoramafirm/pobieranieCategoryStart.html.twig", array(
                'file' => $file
                )
            );
        }else {
            $categorynumber = $this->getCategoryNumber();
            $profession = ['z','b','c','u','r','f','h','i','g','k','j','l','t','m','n','p','s','a'];
            $kategorie = '';
            $plik = '';
            $info = ' ';
            $professionNumber = count($profession);
            @$url = 'http://panoramafirm.pl/biuro,'.$profession[$categorynumber].'/branze.html';

            $html = " Adres obecny: <b>".$url."</b><br/><br/>";
                
            if($strona = @file_get_contents($url)){
                
                $dom = $this->loadHTML($strona);
                $links = $dom->getElementsByTagName("article");
                $article = $this->saveHTML($links);;
                
                $dom = $this->loadHTML($article);
                $links = $dom->getElementsByTagName("a");
                
                foreach ($links as $link) {
                    $kategorie .= $link->getAttribute('href').';';
                    $html .= $link->getAttribute('href').'<br/>';
                }
                
                $this->setData($kategorie,'categories');
                
                $categorynumber = $categorynumber+1;
                
                header( "refresh:0;url=index.php?site=pfcategories&nrkategorii=". $categorynumber ."&go" ); 
                    
            } else {
                $info = "categoryend";
            }
            $progress = round($categorynumber/($professionNumber/100));
            return $this->twig->render("panoramafirm/pobieranieCategory.html.twig", array(
                        'progress' => $progress,
                        'professionnumber' => $professionNumber,
                        'professionactualnumber' => $categorynumber,
                        'url' => $url,
                        'info' => $info,
                        'html' => $html,
                    )
                );
        }
    }
    
    
    public function getInstructionSite()
    {
        return $this->twig->render("panoramafirm/instruction.html.twig", array());
    }
    
    public function getDownloadedDataSite()
    {
        $catalog = 'dane/pf';
        $files = $this->getDownloadedData($catalog);
        return $this->twig->render("panoramafirm/downloadedData.html.twig", array(
            'files' => $files,
            'catalog' => $catalog
        ));
    }
    
}