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
        $strona = 'null';
        

            $proxy = $this->getProxy();
            $tcp = 'tcp://'.$proxy;
            $pro = array(
                'http' => array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n",
                    'proxy' => $tcp,
                    'timeout' => 25 
                ),
            );
            $proxyAdd = stream_context_create($pro);
            
            if($strona = @file_get_contents($url, False, $proxyAdd)){
                var_dump($strona);
            } else {
               $strona = '0';
               
               if(isset($http_response_header)){
                   var_dump($http_response_header);
                    if($http_response_header[0] == 'HTTP/1.1 403 Forbidden'){
                        $strona = '403';
                    } elseif ($http_response_header[0] == 'HTTP/1.1 403 Forbidden'){
                        $strona = '403';
                    }
                }
                else {
                    echo $proxy;
                    $strona = 'null';
                }
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
    
    public function getProxy()
    {
        $proxyList = ['81.196.2.213:3128','90.85.41.71:3128','92.222.78.13:3128','89.10.219.254:3128','92.46.122.98:3128','5.39.118.0:3128','159.8.114.37:25','13.89.36.103:8799','159.8.114.37:8080','218.205.76.139:80','51.254.218.165:8888','79.136.65.150:80','159.8.114.37:3128','90.63.167.63:3128','111.23.6.159:83','183.91.33.76:8087','111.1.23.179:80','213.136.89.121:80','218.205.76.176:80','183.207.228.121:80','183.91.33.76:8090','183.207.228.122:80','5.135.35.184:3128','62.210.88.59:25','111.1.23.148:80','111.1.23.210:80','111.1.23.180:80','45.32.149.239:8080','111.23.6.159:80','212.120.163.170:80','198.50.177.221:8080','211.142.195.72:80','149.56.134.33:8888','212.1.227.182:80','111.1.23.162:80','5.135.176.41:3123','158.69.204.181:3128','31.173.74.73:8080','213.136.77.246:80','111.1.23.141:80','218.205.76.157:80','89.34.97.132:8080','218.205.76.142:80','218.205.72.34:80','151.80.83.141:80','159.8.114.37:80','183.61.236.54:3128','13.89.36.103:53','211.142.195.70:80','218.205.76.154:80','218.205.80.13:80','85.26.146.169:80','111.1.23.148:8080','45.63.77.205:80','13.89.36.103:3128','111.1.23.172:80','111.1.23.145:80','111.1.23.143:80','111.1.23.164:80','111.1.23.140:80','5.135.35.183:3128','52.1.238.137:3128','66.109.24.221:3128','51.255.196.204:3128'];
        $count = count($proxyList)-1;
        
        $proxyNumber = rand(0,$count);
        $proxy = $proxyList[$proxyNumber];
        
        return $proxy;
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
            $url = 'https://www.pkt.pl'.$categories[$this->getCategoryNumber()] . '/'.$page;
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
                
                $a = 0;
                foreach ($links2 as $link) {
                    
                    $temp = $link->getAttribute('class');
                    
                    if($temp == 'box-content company-row '){
                        $list = $this->saveHTML($link);
                        $dom = $this->loadHTML($list);
                        
                        $links = $dom->getElementsByTagName('meta');
                        
                        foreach ($links as $li){
                            $tempClass = $li->getAttribute("itemprop");
                            if ($tempClass == 'telephone'){
                                $telephone=$li->getAttribute('content').';';
                                $dane .= $telephone.';';
                                $ht .= $telephone;
                                $a++;
                            }
                        }
                        $links = $dom->getElementsByTagName('h2');
                        $list = $this->saveHTML($links);
                        $dom = $this->loadHTML($list);
                        $links = $dom->getElementsByTagName('a');
                        foreach($links as $li){
                            $company=$li->nodeValue.';';
                            $dane .= $company;
                            $dane .= "\r\n";
                            $ht .= $company.'<br/>';
                        }
                        
                    }                    
                    
                    if($temp == 'box-content company-row list-sel '){
                        $list = $this->saveHTML($link);
                        $dom = $this->loadHTML($list);
                        
                        $links = $dom->getElementsByTagName('meta');
                        
                        foreach ($links as $li){
                            $tempClass = $li->getAttribute("itemprop");
                            if ($tempClass == 'telephone'){
                                $telephone=$li->getAttribute('content').';';
                                $dane .= $telephone.';';
                                $ht .= $telephone;
                                $a++;
                            }
                        }
                        $links = $dom->getElementsByTagName('h2');
                        $list = $this->saveHTML($links);
                        $dom = $this->loadHTML($list);
                        $links = $dom->getElementsByTagName('a');
                        foreach($links as $li){
                            $company=$li->nodeValue.';';
                            $dane .= $company;
                            $dane .= "\r\n";
                            $ht .= $company.'<br/>';
                        }
                        
                    }            
                    
                    if($temp == 'box-content company-row list-free'){
                        $list = $this->saveHTML($link);
                        $dom = $this->loadHTML($list);
                        
                        $links = $dom->getElementsByTagName('meta');
                        
                        foreach ($links as $li){
                            $tempClass = $li->getAttribute("itemprop");
                            if ($tempClass == 'telephone'){
                                $telephone=$li->getAttribute('content').';';
                                $dane .= $telephone.';';
                                $ht .= $telephone;
                                $a++;
                            }
                        }
                        $links = $dom->getElementsByTagName('h2');
                        $list = $this->saveHTML($links);
                        $dom = $this->loadHTML($list);
                        $links = $dom->getElementsByTagName('a');
                        foreach($links as $li){
                            $company=$li->nodeValue.';';
                            $dane .= $company;
                            $dane .= "\r\n";
                            $ht .= $company.'<br/>';
                        }
                        
                    }
                    

                    
                }

                $this->setData($dane,$kat[4]);

                $page2 =$page+1;

                if(count($kat) == 1){
                    $error ='category';
                }else {
                    header( "refresh:5;url=index.php?site=pktdata&nrstrony=". $page2 ."&nrkategorii=" . $categoryNumber."" ); 
                }
                
            }elseif($downloaded == '403'){
                $error = '403';
                header( "refresh:10;url=index.php?site=pktdata&nrstrony=". $page ."&nrkategorii=" . $categoryNumber."" ); 
                
            } else {
                $info = "categoryend";
                $page2=1;
                $categoryNumber++;
                if($kat = 0){
                    $error ='category';
                }else {
                    header( "refresh:5;url=index.php?site=pktdata&nrstrony=". $page2 ."&nrkategorii=" . $categoryNumber."" ); 
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