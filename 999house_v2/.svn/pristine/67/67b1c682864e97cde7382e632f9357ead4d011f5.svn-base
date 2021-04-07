var indexBanner = (function() {
	const html = `<div class="banner">
					<van-swipe class="my-swipe" :autoplay="5000" indicator-color="white" @change="change">
						<van-swipe-item v-for="(item,index) in list" :key="index" @click="goPage(item)">
							<img :src="$http.imgDirtoUrl(item.img)" class="banner-img">
						</van-swipe-item>
					</van-swipe>
				</div>`;
	
	return {
		props: ['list'], 
		data: function(){
			return {
				/* list: [
					{ img: '/9house/static/logo.png' },
					{ img: '/9house/static/logo.png' },
					{ img: '/9house/static/logo.png' },
					{ img: '/9house/static/logo.png' },
					{ img: '/9house/static/logo.png' }
				] */
				skip: true
			}
		},
		template: html,
		methods: {
			goPage (item){
				if( !this.skip ){
					this.skip = !this.skip;
					return;
				}
				
				if(!$api.trim(item.href)&&item.info){
					item.href = 'houses/index.html?id='+item.info.estate_id+'&cover='+item.cover;
				}

				if(!item.href){
					return
				}
				$api.goPage(item.href)
			},
			change() {
				this.skip = false;
			}
		},
	}
}());