const indexNav = (function() {
	const html = `<div class="nav">
						<div class="nav-wrap" v-for="(items,key) in newNav" :key="key">
							<div class="nav-box" >
								<div class="nav-item" v-for="(item,index) in items" :key="index" @click="goPage(item.href)">
									<img :src="item.img" class="nav-img">
									<span class="nav-tip">{{item.name}}</span>
								</div>
							</div>
						</div>
					</div>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				nav: [
					{ id: 0, img: 'static/logo.png', name: '资讯', href: '' },
					{ id: 1, img: 'static/logo.png', name: '新房', href: '' },
					{ id: 2, img: 'static/logo.png', name: '查房价', href: '' },
					{ id: 3, img: 'static/logo.png', name: '好房推荐', href: 'pages/new_house/good_house.html' },
					{ id: 4, img: 'static/logo.png', name: '地图找房', href: '' },
					{ id: 5, img: 'static/logo.png', name: '便捷找房', href: 'pages/index/find_house.html' },
					{ id: 6, img: 'static/logo.png', name: '房贷计算', href: '' },
					{ id: 7, img: 'static/logo.png', name: '九房百科', href: '' },
					{ id: 8, img: 'static/logo.png', name: '你的名字9', href: '' },
					{ id: 9, img: 'static/logo.png', name: '你的名字10', href: '' },
					{ id: 10, img: 'static/logo.png', name: '你的名字11', href: '' }
				],
				newNav: []
			}
		},
		created() {
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
				if( href ){
					$api.goPage(href);
				}
			}
		}
	}
}());