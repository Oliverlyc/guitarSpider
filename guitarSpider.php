<?php
header('content-type:text/html;charset=utf-8;');
    require '../vendor/autoload.php';
    use QL\QueryList;
    $url = 'http://www.jitaba.cn/pic/list_48_1.html';
    function getLastPage()
    {
        $rules = [
            'page' => ['div.page a:last', 'href']
        ];
        $res = QueryList::get('http://www.jitaba.cn/pic/list_48_1.html')->rules($rules)->query(function($item){ return substr($item['page'],8,3);})->getData()->all();
        return $res[0];
    }

    function getUrlList()
    {
        $allUrl = [];
        $rules = [
            'picPageUrl' => ['li h3 a','href']
        ];
        for($page = 1;$page <= getLastPage(); $page++){
            $arr = QueryList::get('http://www.jitaba.cn/pic/list_48_'.$page.'.html')->rules($rules)->query(function($item){ return $item['picPageUrl'];})->getData()->all();
            foreach($arr as $url){
                $allUrl[] = $url;
            }
            printf("正在爬第 %d 页 \n",$page);
        }
        return $allUrl;
    
    }
    
    function downLoad($url)
    {
        $html = QueryList::get($url);
        $picUrl = $html->rules(['picUrl' => ['div#tabzone a img', 'src']])->query(function($item){return $item['picUrl'];})->getData()->all();
        $title = iconv ( 'GBK', 'utf-8', $html->rules(['title' => ['div.listltitle h1','text']])->query(function($item){return $item['title'];})->getData()->all()[0]);
        mkdir('./img/'.str_replace('/','_',$title));
        for($i = 0 ;$i < count($picUrl);$i++){    
            $data = file_get_contents($picUrl[$i]);
            file_put_contents('./img/'.str_replace('/','_',$title).'/'.($i+1).'.png',$data);
            printf("正在保存%s第 %d 张图 \n",$title,$i+1);
        }
        echo '下载完成';
    }
    function runSpider($urlList)
    {
        foreach($urlList as $url){
            downLoad($url);
        }
    }
    runSpider(getUrlList());
?>