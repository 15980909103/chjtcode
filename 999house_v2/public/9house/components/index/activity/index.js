var indexActivity = (function() {
	// <span class="activity-title">活动专栏</span>
	const html = `<div class="activity">
					
					<div class="activity-vr" v-if="vrlist.length > 0">
						<div 
							class="activity-vr-item" 
							v-for="(item,index) in vrlist" 
							:key="index" 
							:style="{ background: item.bg }"
							@click="goPage(item)"
						>
							<div class="vr-left">
								<span class="vr-title">{{item.name}}</span>
								<span class="vr-text">{{item.text}}</span>
								<span class="vr-tip">{{item.tip}} ></span>
							</div>
							<img :src="item.img">
						</div>
					</div>
					<div class="activity-box">
						<div class="swiper-container">
							<div class="swiper-wrapper">
								<div class="swiper-slide activity-item" v-for="(item,index) in activitylist" :key="index" @click="goPage(item)">
									<p class="activity-title">{{item.title}}</p>
									<span>{{item.sub_title}}</span>
									
									<img :src="$http.imgDirtoUrl(item.img)">
								</div>
							</div>
							<div class="swiper-pagination "></div>
						</div>
					</div>
				</div>`;
				// <div class="activity-item" v-for="(item,index) in activitylist" :key="index" @click="goPage(item)">
				// 	<img v-lazy="$http.imgDirtoUrl(item.img)">
				// </div>
				// <van-swipe :loop="false" width="176" :stop-propagation="false">
				//   <van-swipe-item class="activity-item" v-for="(item,index) in activitylist" :key="index" @click="goPage(item)">
				// 	<p class="activity-title">{{item.title}}</p>
				// 	<span v-if="item.sub_title">{{item.sub_title}}</span>
				// 	<img v-lazy="$http.imgDirtoUrl(item.img)">
				//   </van-swipe-item> 
				// </van-swipe>
	
	return {
		template: html,
		props: {
			activitylist:{
				default(){
					return []
				}
			},
			vrlist:{
				default(){
					return []
				}
			},
		}, 
		data: function(){
			return {
				// vrlist: [
				// 	// {
				// 	// 	name: 'VR看房',
				// 	// 	text: '真实场景带看',
				// 	// 	tip: '中骏首府',
				// 	// 	img: '/9house/static/logo.png',
				// 	// 	bg: 'rgba(166, 206, 217, 0.52)'
				// 	// },
				// 	// {
				// 	// 	name: '直播看房',
				// 	// 	text: '龙湖公寓小复式',
				// 	// 	tip: '已预约2098人',
				// 	// 	img: '/9house/static/logo.png',
				// 	// 	bg: 'rgba(166, 206, 217, 0.52)'
				// 	// }
				// ],
				// activitylist: [
				// 	// { img: '/9house/static/logo.png' },
				// 	// { img: '/9house/static/logo.png' },
				// 	// { img: '/9house/static/logo.png' },
				// 	// { img: '/9house/static/logo.png' }
				// ],
			}
		},
		created() {
			this.$nextTick(()=>{
				this.mySwiper = new Swiper('.swiper-container', {
					slidesPerView: 'auto',
					loop: true,
					pagination: {
					  el: '.swiper-pagination',
					  clickable: true,
					},
				})    
			})
		},
		mounted() {
			
		},
		methods: {
			// goPage() {
			// 	$api.goPage('index/vr_visit_house.html');
			// },
			goPage: (item)=>{
				console.log(1)
				if(!$api.trim(item.href)&&item.info){
					item.href = 'houses/index.html?id='+item.info.estate_id+'&cover='+item.cover;
				}
				if(!item.href){
					return
				}
				$api.goPage(item.href)
			},
		}
	}
}());