var indexNav = (function() {
	const html = `<div class="nav">
						<div class="nav-wrap" v-for="(items,key) in newNav" :key="key">
							<div class="nav-box" >
								<div class="nav-item" v-for="(item,index) in items" :key="index" @click="goPage(item.href)">
									<img v-lazy="$http.imgDirtoUrl(item.cover)" class="nav-img">
									<span class="nav-tip">{{item.name}}</span>
								</div>
							</div>
						</div>
					</div>`;
				
	
	return {
		template: html,
		props: ['nav'],
		data: function(){
			return {
				/* nav: [
					{ id: 0, cover: '/9house/static/logo.png', name: '资讯', href: 'discover/index.html' },
					{ id: 1, cover: '/9house/static/logo.png', name: '新房', href: 'new_house/index.html' },
					{ id: 2, cover: '/9house/static/logo.png', name: '查房价', href: 'discover/index.html?active=研究院' },
					{ id: 3, cover: '/9house/static/logo.png', name: '好房推荐', href: 'new_house/good_house.html' },
					{ id: 4, cover: '/9house/static/logo.png', name: '地图找房', href: 'map/index.html' },
					{ id: 5, cover: '/9house/static/logo.png', name: '便捷找房', href: 'index/find_house.html' },
					{ id: 6, cover: '/9house/static/logo.png', name: '房贷计算', href: 'houses/loan.html' },
					// { id: 7, cover: '/9house/static/logo.png', name: '九房百科', href: '' },
				], */
				newNav: []
			}
		},
		created() {
			//console.log(this.nav,666)
			this.newNav = this.divideNav();
		},
		mounted() {
			
		},
		methods: {
			divideNav() {
				let that = this;
				let newArr = [];
				
				if( that.nav.length > 0 ){
					let cloneNav = $api.deepClone(that.nav);
					_deepAdd(cloneNav);
					
					// console.log(newArr)
					return newArr;
				}
				
				function _deepAdd(arr){
					if( arr.length > 0 ){
						newArr.push(arr.slice(0,8));
						arr.splice(0,8);
						
						if( arr.length > 0 ) {
							_deepAdd(arr);
						}
					}
				}
			},
			goPage(href) {
				if( $api.trim(href) ){
					$api.goPage(href);
				}
			}
		}
	}
}());