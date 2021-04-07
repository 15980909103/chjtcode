const indexNews = (function() {
	const html = `<div class="info" id="info">
						<van-tabs
							v-model="active"
							color="rgba(254, 130, 30, 1)"
							title-active-color="rgba(254, 130, 30, 1)"
							:sticky="true"
							@change="tabsChange"
						>
							<van-tab v-for="(items,index) in tabList" :title="items.name" :key="index">
								<van-list
								  v-model="loading"
								  :finished="finished"
								  finished-text="没有更多了"
								  @load="onLoad"
								>
									
									<div class="new-house-activity" v-if="items.type == 2">
										<div class="new-house-activity-item" v-for="(item,index) in items.activityList" :key="index">
											<div>{{item.name}}</div>
											<img :src="item.img">
										</div>
									</div>
									
									<common-template :list="activeList"></common-template>
								</van-list>
								
							</van-tab>
						</van-tabs>
					</div>`;
	
	return {
		data: function(){
			return {
				active: 0,
				activeList: [],
				loading: false,
				finished: false,
				/**
				 * 
				 * 广告涉及删除，所有数据需要id
				 * 
				 * type
				 * 	
				 * 0-资讯无图 / 1-资讯有图(1~3)
				 * 2-广告有图(1~3) / 3-广告视频 / 4-广告楼盘有图
				 * 5-单独视频 / 6-精彩小视频 / 7-楼盘视频
				 * 8-新房
				 * 
				 */
				tabList: [
					{ 
						type: 1,
						name: '资讯',
						list: [
							{	
								// 0-资讯无图
								id: 0,
								type: 0,
								hot: 1,
								write: 1,
								title: '如何选择户比，梯户比对居住有什么影响？买房时有 哪些坑是可以避免的',
								author: {
									name: '国际在线',
									head: 'static/logo.png'
								},
								readNum: 136,
								commentNum: 43,
								tip: ['快讯','快讯','快讯','快讯']
							},
							{
								// 1-资讯有图(1~3)
								id: 1,
								type: 1,
								hot: 0,
								write: 1,
								title: '土地使用年限和产权使用年限与 购房者有什么关系！',
								img: ['static/logo.png'],
								author: {
									name: '国际在线',
									head: 'static/logo.png'
								},
								readNum: 136,
								commentNum: 43,
								tip: ['城建','快讯','快讯','快讯']
							},
							{
								id: 2,
								type: 1,
								hot: 0,
								write: 1,
								title: '土地使用年限和产权使用年限与 购房者有什么关系！',
								img: ['static/logo.png','static/logo.png'],
								author: {
									name: '国际在线',
									head: 'static/logo.png'
								},
								readNum: 136,
								commentNum: 43,
								tip: ['城建','快讯','快讯','快讯']
							},
							{
								id: 3,
								type: 1,
								hot: 1,
								write: 0,
								title: '土地使用年限和产权使用年限与 购房者有什么关系！',
								img: ['static/logo.png','static/logo.png','static/logo.png'],
								author: {
									name: '国际在线',
									head: 'static/logo.png'
								},
								readNum: 136,
								commentNum: 43,
								tip: ['城建','快讯','快讯','快讯']
							},
							{
								// 2-广告有图(1~3)
								id: 4,
								type: 2,
								title: '首付76万、南北通透、全明结构、观景落地窗，空间 利用率高，不容错过的神户型',
								img: ['static/logo.png']
							},
							{
								id: 5,
								type: 2,
								title: '首付76万、南北通透、全明结构、观景落地窗，空间 利用率高，不容错过的神户型',
								img: ['static/logo.png','static/logo.png']
							},
							{
								id: 6,
								type: 2,
								title: '首付76万、南北通透、全明结构、观景落地窗，空间 利用率高，不容错过的神户型',
								img: ['static/logo.png','static/logo.png','static/logo.png']
							},
							{
								// 3-广告视频
								id: 7,
								type: 3,
								title: '首付76万、南北通透、全明结构、观景落地窗，空间 利用率高，不容错过的神户型',
								url: 'static/logo.png'
							},
							{
								// 4-广告楼盘有图
								id: 8,
								type: 4,
								info: {
									name: '建发养云',
									tip: ['在售','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
									lab: [
										{ type: 0, name: '入围护理人气楼盘榜', img: 'static/logo.png' },
										{ type: 0, name: '享9.5折', img: 'static/logo.png' },
										{ type: 0, name: '777', img: 'static/logo.png' },
										{ type: 0, name: '888', img: 'static/logo.png' }
									]
								},
								title: '「样板间」看海无遮挡，大面宽，四房三厅二卫',
								img:  ['static/logo.png']
							},
							{
								id: 9,
								type: 4,
								info: {
									name: '建发养云',
									tip: ['待售','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
									lab: [
										{ type: 0, name: '入围护理人气楼盘榜', img: 'static/logo.png' },
										{ type: 0, name: '享9.5折', img: 'static/logo.png' },
										{ type: 0, name: '777', img: 'static/logo.png' },
										{ type: 0, name: '888', img: 'static/logo.png' }
									]
								},
								title: '「样板间」看海无遮挡，大面宽，四房三厅二卫',
								img:  ['static/logo.png','static/logo.png']
							},
							{
								id: 10,
								type: 4,
								info: {
									name: '建发养云',
									tip: ['售完','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
									lab: [
										{ type: 0, name: '入围护理人气楼盘榜', img: 'static/logo.png' },
										{ type: 0, name: '享9.5折', img: 'static/logo.png' },
										{ type: 0, name: '777', img: 'static/logo.png' },
										{ type: 0, name: '888', img: 'static/logo.png' }
									]
								},
								title: '「样板间」看海无遮挡，大面宽，四房三厅二卫',
								img:  ['static/logo.png','static/logo.png','static/logo.png','static/logo.png','static/logo.png']
							},
							{
								// 5-单独视频
								id: 11,
								type: 5,
								title: '首付76万、南北通透、全明结构、观景落地窗，空间 利用率高，不容错过的神户型',
								author: {
									name: '国际在线',
									head: 'static/logo.png'
								},
								readNum: 136,
								commentNum: 43,
								tip: ['快讯','快讯','快讯','快讯'],
								url: 'static/logo.png',
							},
							{
								// 6-精彩小视频
								id: 12,
								type: 6,
								list: [
									{
										title: '89平三房小复式',
										tip: '户型鉴赏',
										view: 235,
										url: 'static/logo.png'
									},
									{
										title: '世贸湖边首府',
										tip: '小区Vlog',
										view: 1098,
										url: 'static/logo.png'
									},
									{
										title: '89平三房小复式',
										tip: '新盘速递',
										view: 235,
										url: 'static/logo.png'
									},
									{
										title: '世贸湖边首府',
										tip: '小区Vlog',
										view: 1098,
										url: 'static/logo.png'
									}
								]
							},
							{
								// 7-楼盘视频
								id: 13,
								type: 7,
								info: {
									name: '东海山庄',
									tip: ['尾盘','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
								},
								title: '「样板间」看海无遮挡，大面宽，四房三厅二卫',
								url: 'static/logo.png'
							}
						]
					},
					{
						type: 2,
						name: '新房',
						activityList: [
							{
								name: 'VR售楼处',
								img: 'static/logo.png'
							},
							{
								name: '直播看房',
								img: 'static/logo.png'
							},
							{
								name: '限时特价',
								img: 'static/logo.png'
							},
							{
								name: 'VR售楼处',
								img: 'static/logo.png'
							},
							{
								name: '直播看房',
								img: 'static/logo.png'
							}
						],
						list: [
							{
								// 8-新房（数据和类型4-广告楼盘有图 雷同）
								id: 14,
								type: 8,
								info: {
									name: '世贸湖边首府',
									tip: ['售完','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
									lab: [
										{ type: 0, name: '入围护理人气楼盘榜', img: 'static/logo.png' },
										{ type: 0, name: '享9.5折', img: 'static/logo.png' },
										{ type: 0, name: '777', img: 'static/logo.png' },
										{ type: 0, name: '888', img: 'static/logo.png' }
									]
								},
								img:  ['static/logo.png','static/logo.png','static/logo.png','static/logo.png','static/logo.png']
							},
							{
								id: 15,
								type: 4,
								info: {
									name: '建发养云',
									tip: ['售完','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
									lab: [
										{ type: 0, name: '入围护理人气楼盘榜', img: 'static/logo.png' },
										{ type: 0, name: '享9.5折', img: 'static/logo.png' },
										{ type: 0, name: '777', img: 'static/logo.png' },
										{ type: 0, name: '888', img: 'static/logo.png' }
									]
								},
								title: '「样板间」看海无遮挡，大面宽，四房三厅二卫',
								img:  ['static/logo.png','static/logo.png','static/logo.png','static/logo.png','static/logo.png']
							},
							{
								id: 10,
								type: 4,
								info: {
									name: '建发养云',
									tip: ['售完','住宅','热楼盘','装修交付'],
									price: 34000,
									site: '湖里 软件园',
									area: 108,
									lab: [
										{ type: 0, name: '入围护理人气楼盘榜', img: 'static/logo.png' },
										{ type: 0, name: '享9.5折', img: 'static/logo.png' },
										{ type: 0, name: '777', img: 'static/logo.png' },
										{ type: 0, name: '888', img: 'static/logo.png' }
									]
								},
								title: '「样板间」看海无遮挡，大面宽，四房三厅二卫',
								img:  ['static/logo.png','static/logo.png','static/logo.png','static/logo.png','static/logo.png']
							},
						]
					}
				],
			}
		},
		template: html,
		created() {
			this.tabsChange();
		},
		methods: {
			// 切换资讯/新房
			tabsChange() {
				this.activeList = this.tabList[this.active].list;
			},
			onLoad() {
				// 异步更新数据
				// setTimeout 仅做示例，真实场景中一般为 ajax 请求
				setTimeout(() => {
					
			
					// 加载状态结束
					// this.loading = false;
					console.log('1')
			
					// this.finished = true;
					console.log('2')
				}, 1000);
			},
		}
	}
}());