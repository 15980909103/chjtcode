const indexBanner = (function() {
	const html = `<div class="banner">
					<van-swipe class="my-swipe" :autoplay="5000" indicator-color="white">
						<van-swipe-item v-for="(item,index) in swiperList" :key="index">
							<img :src="item.img" class="banner-img">
						</van-swipe-item>
					</van-swipe>
				</div>`;
	
	return {
		data: function(){
			return {
				swiperList: [
					{ img: 'static/logo.png' },{ img: 'static/logo.png' },{ img: 'static/logo.png' },{ img: 'static/logo.png' },{ img: 'static/logo.png' }
				]
			}
		},
		template: html,
		methods: {
	
		},
	}
}());