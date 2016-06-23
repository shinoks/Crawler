<?php

use Masterminds\HTML5;
    
class polskieKsiazkiTelefoniczne 
{
    function __construct()
    {
        //$this->view = $_GET['view'];
        $this->twig = new Twig_Environment( new Twig_Loader_Filesystem("./view"),
            array( "cache" => "./view/cache" ) );
    }
    
    
    public function getPreviousDownload()
    {
        if(file_exists('dane/pkt/temp.txt')){
            $fread = fopen('dane/pf/temp.txt', 'r');
            $temp = fread($fread, filesize('dane/pf/temp.txt'));
            fclose($fread);
        }else {
            $temp = '1;1';
        }
        $temp = explode(';',$temp);
        
        return $temp;
    }
    
    public function setPreviousDownload($nrstrony,$nrkategorii)
    {
        $previous = $nrstrony.';'.$nrkategorii;
        $fwrite = fopen('dane/pkt/temp.txt', 'w');
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
        $fwrite = fopen('dane/pkt/' . $categoryName . '.csv', 'a');
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
        if(file_exists('dane/pkt/categories.csv')){
            $fread = fopen('dane/pkt/categories.csv', 'r');
            $category = fread($fread, filesize('dane/pkt/categories.csv'));
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
            return $this->twig->render("pkt/downloadData.html.twig", array(
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
            $url = 'https://www.pkt.pl/szukaj/'.$categories[$this->getCategoryNumber()] . '/'.$page;
            $kat = explode('/',$url);
            $percent = round($categoryNumber/($categoriesCount/100),2); 
            $downloaded = $this->getContents($url);
            $info = ' ';
            $error = ' ';
            $dane = ' ';
            $ht = ' ';
            
            if($downloaded != '0'){
                $dom = $this->loadHTML($downloaded);
                
                $links2 = $dom->getElementsByTagName('div');
                
                //$links = $dom->getElementsByTagName('meta');
                
                $a = 0;
                foreach ($links2 as $link) {
                    
                    $temp = $link->getAttribute('class');
                    
                    if($temp == 'box-content company-row '){
                        $list = $this->saveHTML($link);
                        $dom = $this->loadHTML($list);
                        
                        $links = $dom->getElementsByTagName('meta');
                        $list = $this->saveHTML($links);
                        var_dump($list);
                        
                        foreach ($links as $li){
                            $tempClass = $link->getAttribute("itemprop");
                        
                            if ($tempClass == 'telephone'){
                                $article=$link->getAttribute('content').';';
                                echo $article;
                                $dane .= "\r\n";
                                $ht .= "<br/>";
                                $dane .= $article.';';
                                $ht .= $article.'<br/>';
                                $a++;
                            }
                        }
                    }
                    

                    
                }
                $this->setData($dane,$kat[4]);

                $page2 =$page+1;

                if(count($kat) == 1){
                    $error ='category';
                }else {
                    //header( "refresh:1;url=index.php?site=pfdata&nrstrony=". $page2 ."&nrkategorii=" . $categoryNumber."" ); 
                }
                
            } else {
                $info = "categoryend";
                $page2=1;
                $categoryNumber++;
                if($kat = 0){
                    $error ='category';
                }else {
                 //   header( "refresh:1;url=index.php?site=pfdata&nrstrony=". $page2 ."&nrkategorii=" . $categoryNumber."" ); 
                }
            }
            
            return $this->twig->render("pkt/downloadingDataReady.html.twig", array(
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
        $page = $this->getPageNumber();
        $categorynumber = $this->getCategoryNumber();
        
        if($categorynumber == 0){$categorynumber = 1;}

        $url = 'https://www.pkt.pl/kategorie/budowa-i-remont-'.$categorynumber.'/'.$page;
        $kategorie = '';
        $plik = '';
        $info = ' ';
        $professionNumber = 24;
        $html = " Adres obecny: <b>".$url."</b><br/><br/>";
        
        if($strona = @file_get_contents($url)){
            
            $dom = $this->loadHTML($strona);
            $links = $dom->getElementsByTagName("section");
            
            $article = $this->saveHTML($links);;
            
            $dom = $this->loadHTML($article);
            $links = $dom->getElementsByTagName("a");

            foreach ($links as $link) {
                $kategorie .= $link->getAttribute('href').';';
                $html .= $link->getAttribute('href').'<br/>';
            }
                
            $this->setData($kategorie,'categories');
            $page2= $page+1;
                
            header( "refresh:5;url=index.php?site=pktcategories&nrkategorii=". $categorynumber ."&nrstrony=".$page2 ); 
        } elseif ($categorynumber<25) {
            $categorynumber2= $categorynumber+1;
            $page2 = 1;
            header( "refresh:5;url=index.php?site=pktcategories&nrkategorii=". $categorynumber2 ."&nrstrony=".$page2 ); 
            
                $info = "categoryend";
                
        } else {
            $info = "end";
        }
        $progress = round($categorynumber/($professionNumber/100));
        
        return $this->twig->render("pkt/downloadCategory.html.twig", array(
            'progress' => $progress,
            'professionnumber' => $professionNumber,
            'professionactualnumber' => $categorynumber,
            'url' => $url,
            'info' => $info,
            'html' => $html,
            )
        );
        
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