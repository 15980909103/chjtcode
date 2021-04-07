<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Template
 *
 * @author admin
 */
class Template {

    /**
     * Control调用的 用
     * @param type $name
     * @param type $data
     */
    public function renderPage(&$data) {
        $path = Module . DS . Context::$module . DS . 'template' . DS . Context::$controller . DS;
        $tmpArray = Context::$para;
        array_unshift($tmpArray, Context::$action);
        $filename = "";
        while (!empty($tmpArray)) {
            $filename = implode('-', $tmpArray) . '.tpl.php';
            if (!is_file($path . $filename)) {
                array_pop($tmpArray);
                $filename = "";
                continue;
            }

            break;
        }
        if ($filename == '') {
            $filename = 'action.tpl.php';
        }
        if (is_array($data)) {
            extract($data);
        }
        ob_start();
        include $path . $filename;
        $content = ob_get_contents();
        ob_end_clean();

        $path = Module . DS . Context::$module . DS . 'template' . DS;
        $tmpArray = Context::$para;
        array_unshift($tmpArray, Context::$action);
        array_unshift($tmpArray, Context::$controller);
        array_unshift($tmpArray, 'html');
        $html = "";
        while (!empty($tmpArray)) {
            $html = implode('-', $tmpArray) . '.tpl.php';
            if (!is_file($path . $html)) {
                array_pop($tmpArray);
                $html = "";
                continue;
            }

            break;
        }
        ob_start();
        include $path . $html;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
   

    public function render($name, &$data, $block = 'controller') {
        $path = Module . DS . Context::$module . DS . 'template' . DS . $block . DS;
        $tmpArray = Context::$para;
        array_unshift($tmpArray, $name);
        $filename = "";
        while (!empty($tmpArray)) {
            $filename = implode('-', $tmpArray) . '.tpl.php';
            if (!is_file($path . $filename)) {
                array_pop($tmpArray);
                $filename = "";
                continue;
            }
            break;
        }

        if ($filename == '') {
            $filename = $block . '.tpl.php';
        }
        if (is_array($data)) {
            extract($data);
        }
        ob_start();
        include $path . $filename;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function renderJson(&$data) {
        header('Content-type: text/json');
        return json_encode($data);
    }



}
