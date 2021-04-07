var indexHeader = (function() {
	const html = `<header class="header" :style="{ background: backClass }">
					<div class="header-box">
						<div class="header-options">
							<span class="header-location" @click="chooseLocation">{{location}}<span class="iconfont iconjiantou"></span></span>
							<div class="header-skill">
								<!--<span class="header-map" @click="goPage('map/index.html')"><span class="iconfont iconzhoubian"></span>地图</span>
							<span class="header-scan"><span class="iconfont iconsaoma"></span>扫一扫</span>	-->
							</div>
						</div>
						<div class="header-search" @click="goSearch">
							<span class="iconfont icon901"><span class="header-tip">搜索楼盘、资讯</span></span>
						</div>
					</div>
					<div class="map" :style="{ color: mapColor }" @click="goPage('map/index.html')"><i class="iconfont iconzhoubian"></i><span>地图</span></div>
				</header>`;
	
	return {
		data: function(){
			return {
				scrollFlag:false,
				backcolor:false,
				backClass: '',
				mapColor: ''
			}
		},
		props: [
			'location',
			'isfixed'
		],
		template: html,
		watch: {
			isfixed(newV){
				if( newV ){
					this.backcolor = true;
					// this.backClass = 'rgb(245, 245, 245)';
					this.backClass = '#F8F8F8';
					this.mapColor = '#000';
				} else {
					this.backcolor = false;
					this.backClass = this.mapColor = '';
				}
			}
		},
		created() {
			this.$api.localDel('pre-page');
		},
		methods: {
			chooseLocation() {
				this.$api.goPage('index/location.html');
			},
			goSearch() {
				this.$api.localSet('pre-page', window.location.href)
				this.$api.goPage('index/search.html');
			},
			goPage: $api.goPage
		}
	}
}());