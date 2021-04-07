const indexActivity = (function() {
	const html = `<div class="activity">
					<span class="activity-title">活动专栏</span>
					<div class="activity-vr">
						<div 
							class="activity-vr-item" 
							v-for="(item,index) in vrList" 
							:key="index" 
							:style="{ background: item.bg }"
							@click="goPage"
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
						<div class="activity-item" v-for="(item,index) in activityList" :key="index">
							<img :src="item.img">
						</div>
					</div>
				</div>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				vrList: [
					{
						name: 'VR看房',
						text: '真实场景带看',
						tip: '中骏首府',
						img: 'static/logo.png',
						bg: 'rgba(166, 206, 217, 0.52)'
					},
					{
						name: '直播看房',
						text: '龙湖公寓小复式',
						tip: '已预约2098人',
						img: 'static/logo.png',
						bg: 'rgba(166, 206, 217, 0.52)'
					}
				],
				activityList: [
					{ img: 'static/logo.png' },{ img: 'static/logo.png' },{ img: 'static/logo.png' },{ img: 'static/logo.png' }
				],
			}
		},
		created() {
		
		},
		mounted() {
			
		},
		methods: {
			goPage() {
				$api.goPage('pages/index/vr_visit_house.html');
			}
		}
	}
}());