<?php
class Block {
	protected $expire = - 1; // 缓存过期时间 -1时不缓存，0永久缓存 大于0时为缓存时间
	protected $name; // 子模块的名称
	protected $module;
	protected $para;
	function setCache($time) {
		$this->expire = $time;
	}
	public function Init() {
		$this->name = strtolower ( get_called_class () );
	}
	function render($name, &$data = array()) {
		$path = Module . DS . Context::$module . DS . 'template' . DS . 'block' . DS;
		$name .= '.tpl.php';
		if (is_array ( $data )) {
			extract ( $data );
		}
		ob_start ();
		include $path . $name;
		$content = ob_get_contents ();
		ob_end_clean ();
		return $content;
	}
	function execute(&$para) {
		$cacheName = $this->name;
		
		if (BLOCKCACHE && $this->expire >= 0) {
			if (! empty ( $para )) {
				$cacheName .= '-' . implode ( '-', $para );
			}
			if (! empty ( Context::$para )) {
				$cacheName .= '-' . implode ( '-', Context::$para );
			}
			$value = BlockCache::get ( $cacheName, $this->expire );
			if ($value != null) {
				return $value;
			}
		}
		$result = call_user_func_array ( array (
				$this,
				'show' 
		), $para );
		
		if (BLOCKCACHE && $this->expire != - 1) {
			
			BlockCache::set ( $cacheName, $result );
		}
		return $result;
	}
}
