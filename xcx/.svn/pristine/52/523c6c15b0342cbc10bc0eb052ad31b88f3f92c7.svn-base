<?php

/**
 * 控制器基类
 *
 * @author admin
 */
class Controller {

    private $cache;
    private $html = "html.tpl.php";

    function permission() {
        return TRUE;
    }

    function setCache() {
        $this->cache = array();
    }

    function setBaseHtml($html) {
        $this->html = $html . '.tpl.php';
    }

    public function render($name, &$data = array()) {
        return $this->renderContent($name,$data);
    }

    public function renderPage($name, &$data = array()) {
        $content = $this->renderContent($name,$data);
        $data['content'] = &$content;
        return $this->renderHtml($data);
    }

    public function renderJson(&$data) {
        header('Content-type: text/json');
        return json_encode($data);
    }

    public function _exec() {
        $iscache = ACTIONCACHE && isset($this->cache[Context::$action]);
        if ($iscache) {
            $cache = ActionCache::get($this->cache[Context::$action]);
            if ($cache !== null) {
                echo $cache;
                exit();
            }
        }
        $action = Context::$action;
        $str = $this->$action();
        if ($iscache) {
            ActionCache::set($str);
        }
        echo $str;
    }

    private function renderHtml(&$data) {
        $path = Module . DS . Context::$module . DS . 'template' . DS;

        if (is_array($data)) {
            extract($data);
        }
        ob_start();
        include $path . $this->html;
        $ret = ob_get_contents();
        ob_end_clean();

        return $ret;
    }

    private function renderContent($filename, &$data) {
        $path = Module . DS . Context::$module . DS . 'template' . DS . Context::$controller . DS;
        $tmpArray = Context::$para;
        array_unshift($tmpArray, Context::$action);

        $filename.='.tpl.php';

        if (is_array($data)) {
            extract($data);
        }
        ob_start();
        include $path . $filename;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
