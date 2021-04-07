
// 自定义楼盘标签 - 继承DOMOverlay
function Site(options) {
	TMap.DOMOverlay.call(this, options);
}

Site.prototype = new TMap.DOMOverlay();

// 初始化
Site.prototype.onInit = function(options) {
	this.map = options.map;
	this.position = options.position;
	this.title = options.title || '';
	this.site = options.site || '';
};

// 销毁时需解绑事件监听
Site.prototype.onDestroy = function() {
	if (this.onClick) {
		this.dom.removeEventListener('click',this.onClick);
	}
	this.removeAllListeners();
};

// 创建DOM元素，返回一个DOMElement，使用this.dom可以获取到这个元素
Site.prototype.createDOM = function() {
	let tip = document.createElement("div");
	let title = document.createElement("span");
	let site = document.createElement("span");
	
	tip.className = 'my_Site';
	
	tip.style.cssText = `
		position: absolute;
		background-color: #fff;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: .1rem .14rem;
		border-radius: 5px;
		box-shadow: 0 0 15px rgba(0,0,0,.1);
	`;
	
	title.style.cssText = `
		font-size: .28rem;
		color: rgba(11, 15, 18, 1);
		font-weight: 600;
	`;
	
	site.style.cssText = `
		font-size: .24rem;
		color: rgba(151, 155, 157, 1);
	`;
	
	title.innerHTML = this.title.length <= 8 ? this.title : this.title.slice(0,8)+'...';
	site.innerHTML = this.site.length <= 10 ? this.site : this.site.slice(0,10)+'...';
	
	tip.appendChild(title);
	tip.appendChild(site);
	
	// click事件监听
	this.onClick = (e) => {
		// DOMOverlay继承自EventEmitter，可以使用emit触发事件
		this.emit('click');
		
		e&&e.stopPropagation&&e.stopPropagation();
	};
	
	tip.addEventListener('click', this.onClick);
	
	return tip;
};

// 更新DOM元素，在地图移动/缩放后执行
Site.prototype.updateDOM = function(e) {
	if (!this.map) {
		return;
	}
	
	// 经纬度坐标转容器像素坐标
	let pixel = this.map.projectToContainer(this.position);
	// 使饼图中心点对齐经纬度坐标点
	let left = pixel.getX() - this.dom.clientWidth / 2 - 4 + 'px';
	let top = pixel.getY() - this.dom.clientHeight / 2 - this.dom.offsetHeight - 20 + 'px';
	this.dom.style.transform = `translate(${left}, ${top})`;
};

window.Site = Site;