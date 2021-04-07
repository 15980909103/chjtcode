const indexHeader = (function() {
	const html = `<header class="header">
					<div class="header-box">
						<div class="header-options">
							<span class="header-location" @click="chooseLocation">{{location}}<span class="iconfont iconjiantou"></span></span>
							<div class="header-skill">
								<span class="header-map"><span class="iconfont iconlocation-2"></span>地图</span>
								<span class="header-scan"><span class="iconfont iconsaoma"></span>扫一扫</span>
							</div>
						</div>
						<div class="header-search" @click="goSearch">
							<span class="iconfont iconsearch"><span class="header-tip">搜索楼盘名称</span></span>
						</div>
					</div>
				</header>`;
	
	return {
		data: function(){
			return {
		
			}
		},
		props: [
			'location'
		],
		template: html,
		methods: {
			chooseLocation() {
				$api.goPage('pages/index/location.html');
			},
			goSearch() {
				$api.goPage('pages/index/search.html');
			}
		}
	}
}());