const indexSpeedy = (function() {
	const html = `<div class="speedy">
				<van-tabs 
					class="speedy-card"
					v-model="active" 
					animated
				>
				  <van-tab v-for="(item, index) in speedyList" :key="index" :title="item.tip" >
					  <div class="speedy-wrap">
						  <div class="speedy-item">
							  <div class="speedy-title text-omit">{{item.title}}</div>
							  <div class="speedy-text text-omit">{{item.text}}</div>
						  </div>
						  <img :src="item.img" class="speedy-img">
					  </div>
					  <div class="speedy-btn">{{item.btn}}</div>
				  </van-tab>
				</van-tabs>
			</div>`;
	
	return {
		data: function(){
			return {
				active: 0,
				speedyList: [
					{
						tip: '快速找房',
						title: '为您线上快速找房',
						text: '智能AI匹配，定制推荐，动态提醒，专项服务',
						img: 'static/logo.png',
						btn: '立即找房'
					},
					{
						tip: '我的房子',
						title: '为您线上快速找房，为您线上快速找房，为您线上快速找房，为您线上快速找房',
						text: '智能AI匹配，定制推荐，动态提醒，专项服务，智能AI匹配，定制推荐，动态提醒，专项服务',
						img: 'static/logo.png',
						btn: '立即查看'
					}
				]
			}
		},
		template: html,
		methods: {
	
		}
	}
}());