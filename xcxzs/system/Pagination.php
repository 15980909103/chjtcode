<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pagination
 *
 * @author admin
 */
class Pagination {

    private $data;
    private $listno;

    public function __construct($currentIndex, $total) {
        $this->data = array();
        $this->data['totalRow'] = $total;
        $this->data['pageRow'] = 10;
        $this->data['currentIndex'] = $currentIndex;
        $this->listno = 7;
    }

    function setPageRow($row) {
        $this->data['pageRow'] = $row;
    }

    function setListNo($no) {
        $this->listno = $no;
    }


    public function Render($url = '', $template = 'pagination') {
        if (empty($url)) {
            $url = $_SERVER["REQUEST_URI"];
        }
        $total = $this->data['totalRow'];
        $currentIndex = $this->data['currentIndex'];
        $pageRow = $this->data['pageRow'];

        $totalPage = ceil($total / $pageRow);
        $totalPage = $totalPage <= 0 ? 1 : $totalPage;
        if ($totalPage == 1)
            return '';

        switch (true) {
            case $currentIndex < 1:
                $currentIndex = 1;
                break;
            case $currentIndex > $totalPage:
                $currentIndex = $totalPage;
                break;
        }

        $leftlist = floor($this->listno / 2);
        $startIndex = $currentIndex - $leftlist;
        $startIndex = $startIndex <= 0 ? 1 : $startIndex;

        $targetIndex = $startIndex + $this->listno - 1;
        if ($targetIndex > $totalPage) {
            $targetIndex = $totalPage;
            $startIndex = $targetIndex - $this->listno + 1;
            $startIndex = $startIndex <= 0 ? 1 : $startIndex;
        }
        if ($totalPage == 1) {
            $totalPage = -1;
        }

        $prePage = $currentIndex - 1;
        $prePage = $prePage <= 0 ? -1 : $prePage;
        $nextPage = $currentIndex + 1;
        $nextPage = $nextPage > $totalPage ? -1 : $nextPage;

        $this->data['firstIndex'] = 1;
        $this->data['currentIndex'] = $currentIndex;
        $this->data['totalPage'] = $totalPage;
        $this->data['lastIndex'] = $totalPage;
        $this->data['preIndex'] = $prePage;
        $this->data['nextIndex'] = $nextPage;
        $this->data['startIndex'] = $startIndex;
        $this->data['targetIndex'] = $targetIndex;


        $url = preg_replace('/[&\?]page=\d+/i', '', $url);
        if (strpos($url, '?') > 0) {
            $url.='&page=';
        } else {
            $url.='?page=';
        }
        $this->data['url'] = $url;
        $file = Module . DS . Context::$module . DS . 'template' . DS . $template . '.tpl.php';
        extract($this->data);
        ob_start();
        include $file;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

}

?>
