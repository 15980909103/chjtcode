<template>
	<view>
		<view v-if="pageShow" class="content">
			<block v-if="lists.length">
				<view class="wrap">
					<view v-for="(nav,k) in lists" :key="k">
						<view class="title">{{ nav.time }}</view>
						<common-new-house :list="nav.list" @del="(e)=>{ $set(list[k], 'list', e) }"></common-new-house>
					</view>
				</view>
			</block>
			<block v-else>
				<u-empty image="error" description="暂无数据"/>
			</block>

			<view id="container-user-site"></view>
		</view>
	</view>
</template>

<script>
	// import commonNewHouse from '@/components/common/template_newHouse'
	import commonNewHouse from '../../components/common/template_newHouse.vue'
	export default {
		data() {
			return {
				loading: false,
				page: 0,
				total_page:1,

				activeId:-1,
				pageShow:false,
				lists:[]
			}
		},
		onLoad() {
			this.getIndexHistory();
		},
		onReachBottom(){
			this.getIndexHistory();
		},
		components:{
			commonNewHouse
		},
		methods:{
			getIndexHistory(){
				let page = this.page+1;
				if(page>this.total_page){
					return
				}
				if(this.loading==true){
					return
				}
				this.loading= true;

				this.$http.post(
					'/user/browseRecords',
					{
						page: page
					}
				).then( res=>{
					//console.log('length',res.data.length)
					//console.log(res.data)
					if(res.data.length == 0){
						res.data = [];
					}
					this.lists = this.lists.concat(res.data) 

					this.pageShow = true;
					this.page = page;
					this.total_page = res.data.last_page? res.data.last_page : 1;
					this.loading= false;
				}).catch(()=>{
					this.loading= false;
					this.pageShow = true;
				})
			},
		},
	}
</script>

<style>
	 @import './my_history.css';
</style>
