<template>
	<u-dropdown class="u-dropdown" active-color="rgba(254, 130, 30, 1)" @close="setCloseMethod" ref="uDropdown">
		<template v-for="(sezer, k) in list">
			<u-dropdown-item 
				:title="sezer.title"
				:key="k"
			>
				<view class="slot-content">
					<template v-if=" k == 0 ">
						<common-area 
							:height="716" 
							:list="sezer.list" 
							@sure="siteSure"
							@close="siteClose" 
							:default_data='value'
							ref="area"
						>
						</common-area>
					</template>
					<template v-else-if=" k == 1 ">
						<view class="drop-price">
							<view class="drop-price-box">
								<view class="drop-price-left">
									<view 
										class="drop-price-item" 
										:class="[ priceActive.left == index ? 'drop-price-item-active' : '' ]"
										v-for="(item,index) in sezer.list" 
										:key="index" 
										@click="priceChoose('left',index)"
									>
										{{ item.title }}
									</view>
								</view>
								<view class="drop-price-right">
									<template v-for="(item,index) in sezer.list">
										<span :key="index" v-if="priceActive.left == index">
											<view
												class="drop-price-item" 
												:class="[ priceActive.right.indexOf(key) != -1 ? 'drop-price-item-active' : '' ]"
												v-for="(money,key) in item.list" 
												:key="key"
												@click="priceChoose('right',key)"
											>
												<p>{{ money.name }}</p>
												<i class="iconfont " :class="[ priceActive.right.indexOf(key) != -1 ? 'iconnewxuanzhongduoxuan' : 'iconweixuanzhong' ]"></i>
											</view>
										</span>
									</template>
								</view>
							</view>
							<view class="drop-price-input">
								<view class="drop-price-input-price"> 
									<span class="drop-price-input-title">自定义(万元)</span>
									<view class="drop-price-input-box">
										<u-input 
											v-model="priceMin" 
											placeholder="最低价" 
											type="digit" 
											input-align="center" 
											:clearable="false" 
											:custom-style="{
												width: '158rpx',
												height: '64rpx',
												padding: 0,
												border: '1px solid #e0e0e0'
											}"
											@input="priceInput"
										/>
										<span class="drop-price-input-interval"></span>
										<u-input 
											v-model="priceMax" 
											placeholder="最高价" 
											type="digit" 
											input-align="center" 
											:clearable="false"
											:custom-style="{
												width: '158rpx',
												height: '64rpx',
												padding: 0,
												border: '1px solid #e0e0e0'
											}"
											@input="priceInput"
										/>
									</view>
								</view>
								<view class="drop-price-input-wrap">
									<u-button
										class="drop-del drop-btn-item" 
										:custom-style="{
											borderColor: 'rgba(134, 186, 122, 1)',
											color: 'rgba(134, 186, 122, 1)',
											height: '88rpx',
											lineHeight: '88rpx'
										}"
										plain 
										@click="priceDel"
									>
										重置
									</u-button>
									<u-button
										class="drop-btn-item" 
										:custom-style="{
											color: '#fff',
											backgroundColor: 'rgba(254, 130, 30, 1)',
											height: '88rpx',
											lineHeight: '88rpx'
										}"
									
										@click="priceSure"
									>
										确认
									</u-button>
								</view>
							</view>
						</view>
					</template>
					
					<template v-else-if=" k == 2 ">
						<view class="drop-type">
							<view class="drop-type-wrap" >
								<view class="drp-type-box" v-for="(item,index) in sezer.list" :key="index" @click="typeChange(index)">
									<span :class="[ typeChoose.indexOf(index) != -1 ? 'drop-price-item-active' : '' ]">{{ item.name }}</span>
									<i class="iconfont " :class="[ typeChoose.indexOf(index) != -1 ? 'iconnewxuanzhongduoxuan' : 'iconweixuanzhong' ]"></i>
								</view>
							</view>
							<view class="drop-btn">
								<u-button
									class="drop-del drop-btn-item" 
									:custom-style="{
										borderColor: 'rgba(134, 186, 122, 1)',
										color: 'rgba(134, 186, 122, 1)',
										height: '88rpx',
										lineHeight: '88rpx'
									}"
									plain 
									@click="typeDel"
								>
									重置
								</u-button>
								<u-button
									class="drop-btn-item" 
									:custom-style="{
										color: '#fff',
										backgroundColor: 'rgba(254, 130, 30, 1)',
										height: '88rpx',
										lineHeight: '88rpx'
									}"
									@click="typeSure"
								>
									确认
								</u-button>
							</view>
						</view>
					</template>
					
					<template v-else>
						<view class="drop-more" v-if=" Object.keys(moreChooseSure).length > 0 ">
							<view class="drop-more-wrap">
								<view class="drop-more-item" v-for="(item,index) in sezer.list" :key="index">
									<h4 class="drop-more-title">{{ item.title }}</h4>
									<view class="drop-more-box">
										<span 
											class="drop-more-tip van-ellipsis" 
											:class="[ (moreClass.indexOf(item.type+tip.id) != -1) ? 'drop-more-tip-active' : '' ]"
											v-for="(tip,key) in item.list"
											:key="key"
											@click="chooseMore(index,tip.id)"
										>
											{{ tip.name }}
										</span>
									</view>
								</view>
							</view>
							<view class="drop-btn">
								<u-button
									class="drop-del drop-btn-item" 
									:custom-style="{
										borderColor: 'rgba(134, 186, 122, 1)',
										color: 'rgba(134, 186, 122, 1)',
										height: '88rpx',
										lineHeight: '88rpx'
									}"
									plain 
									@click="moreDel"
								>
									重置
								</u-button>
								<u-button
									class="drop-btn-item" 
									:custom-style="{
										color: '#fff',
										backgroundColor: 'rgba(254, 130, 30, 1)',
										height: '88rpx',
										lineHeight: '88rpx'
									}"
									@click="moreSure(k)"
								>
									确认
								</u-button>
							</view>
						</view>
					</template>
				</view>
			</u-dropdown-item>
		</template>
	</u-dropdown>
</template>

<script>
	import commonArea from "@/components/common/cArea";
	
	export default {
		data() {
			return {
				list: [],
				// nav筛选所有结果
				sizerResult: {},
				// 价格筛选
				priceActive: {
					left: 0,
					right: []
				},
				priceActiveSure: {
					left: 0,
					right: []
				},
				priceMin: '',
				priceMax: '',
				
				// 户型筛选
				typeChoose: [0],
				typeChooseSure: [0],
				
				// 更多筛选
				moreType: [],
				moreClass: [],
				moreChoose: {},
				moreChooseSure: {},
				value: {}
			}
		},
		props: {
			default_data: {
				type: [Object],
				default () {
					return {}
				}
			},
			moreData: {
				type: [Object,Array],
				default () {
					return {}
				}
			},
		},
		components: {
			commonArea
		},
		created() {
			this.value = this.default_data;
			this.getList();
		},
		mounted() {
			// console.log(this.moreData)
			this.list = this.moreData;
			this.addMoreType(this.list);
			if(this.default_data.site_center){
				this.list[0].title = this.default_data.site_center.district
			}
		},
		methods: {
			setCloseMethod(index) {
				if( index == 99999 ){
					return;
				}
				
				const title = this.list[index].title;
				
				(title != '区域' && title != '价格' && title != '户型' && title != '更多') ? this.$refs.uDropdown.highlight(index,1) : this.$refs.uDropdown.highlight(index,2);
				
				switch (index) {
					case 0:
						this.$refs.area[0].reset();
						break;
					case 1:
						this.priceClosed();
						break;
					case 2:
						this.typeClosed();
						break;
					case 3:
						this.moreClosed();
						break;
				}
			},
			sendResutl(name, arr, type) {
				this.$set(this.sizerResult, name, arr);
			
				if (!type) {
					this.$nextTick(() => {
						this.$emit('result', this.sizerResult);
					});
				}
			},
			siteClose() {
				this.$refs.uDropdown.close();
			},
			siteSure(text, id, center) {
				// text.length = text.length - 1
				if( text.indexOf(',') != -1 ){
					text = text.slice(0,text.length-1);
				}
				
				text = text.split(',');
				
				if( text.length > 1 ){
					text = '多选';
				} else {
					text = text[0] == '不限' ? '区域' : text[0];
				}
				
				this.$set(this.list[0], 'title', text);
			
				if (String(id).indexOf('p_') != -1) {
					id = id.replace('p_', '');
				}
			
				this.sendResutl('siteCenter', center, 1);
				this.sendResutl('site', id);
			},
			chooseMore(index, id, call) {
				// console.log(index)
				const indexOf = this.moreChoose[this.moreType[index]].indexOf(id);
			
				if (indexOf == -1) {
					this.moreChoose[this.moreType[index]].push(id);
					this.moreClass.push(this.moreType[index] + id);
				} else {
					this.moreChoose[this.moreType[index]].splice(indexOf, 1);
			
					this.moreClass.splice(this.moreClass.indexOf(this.moreType[index] + id), 1);
				}
				
				call && call();
			},
			moreDel() {
				this.$set(this.list[3], 'title', '更多');
				this.initMoreState();
				this.$refs.uDropdown.close();
				this.sendResutl('more', this.moreChooseSure);
			},
			moreSure(k,type) {
				let text;
				let len = 0;
			
				for (let i in this.moreChoose) {
					len += this.moreChoose[i].length;
				}
			
				if (len == 0) {
					text = '更多';
				} else {
					if( len > 1 ){
						text = '多选';
					} else {
						for( let i in this.moreChoose ){
							if( this.moreChoose[i] ){
								this.moreChoose[i].map( item=>{
									this.list[k].list.map( list=>{
										list.list.map( key=>{
											if( key.id == item ){
												text = key.name;
											}
										})
									})
								})
							}
						}
						// console.log(this.moreChoose)
						// console.log(this.list[k].list)
					}
					// text = `筛选(${len})`;
				}
			
				this.$set(this.list[3], 'title', text);
				this.moreChooseSure = this.$api.deepClone(this.moreChoose);
			
				this.moreClass = [];
				for (let i in this.moreChoose) {
					this.moreChoose[i].map((item) => {
						this.moreClass.push(i + item);
					})
				}
				
				this.$refs.uDropdown.close();
				
				this.sendResutl('more', this.moreChooseSure);
			},
			moreClosed() {
				this.moreChoose = this.$api.deepClone(this.moreChooseSure);
			
				this.moreClass = [];
				for (let i in this.moreChooseSure) {
					this.moreChooseSure[i].map((item) => {
						this.moreClass.push(i + item);
					})
				}
			},
			// 添加更多类型 
			addMoreType(data) {
				if(data.length>0){
					data.map(item => {
						if (item.title == '更多') {
							item.list.map(child => {
								this.moreType.push(child.type);
							})
						}
					})
				}
				this.initMoreState();
			},
			initMoreState() {
				this.moreType.map((item, index) => {
					this.moreChoose[item] = [];
					this.moreChooseSure[item] = [];
					this.moreClass[index] = [];
				})
			},
			priceInput() {
				if( this.priceActive.right.length > 0 ){
					this.$set(this.priceActive, 'right', []);
				}
			},
			priceChoose(key, index) {
				this.priceMin = this.priceMax = '';
				
				if (key == 'left') {
					if (this.priceActive[key] != index) {
						this.$set(this.priceActive, key, index);
						this.$set(this.priceActive, 'right', []);
					}
				} else {
					const num = this.priceActive[key].indexOf(index);
			
					if (num != -1) {
						this.priceActive[key].splice(num, 1);
					} else {
						if (index != 0) {
			
							const all = this.priceActive[key].indexOf(0);
			
							if (all != -1) {
								this.priceActive[key].splice(all, 1);
							}
			
							this.priceActive[key].push(index);
						} else {
							this.priceActive[key] = [index];
						}
						// this.priceActive[key] = [index];
					}
				}
				// console.log(this.priceActive)
			},
			priceSure() {
				let text = '';
				let id = [];
			
				if (this.priceMin && this.priceMax) {
					if (Number(this.priceMin) > 0 && Number(this.priceMax) > 0 && (Number(this.priceMax) > Number(this.priceMin))) {
						const newName = this.priceActive.left == 1 ? 'total' : 'average';
			
						text = `${this.priceMin}万-${this.priceMax}万`;
			
						id = {
							type: newName,
							val: [`${this.priceMin}-${this.priceMax}`]
						};
			
						this.priceActive.right = this.priceActiveSure.right = [];
						this.priceMin = this.priceMax = '';
					} else {
						this.$toast('请输入正确价格');
			
						return;
					}
				} else {
					if (this.priceActive.right.length > 0) {
			
						this.priceActive.right.map(item => {
							const name = this.list[1].list[this.priceActive.left].list[item].name;
			
							if (name == '不限') {
								text = '价格';
							} else {
								if( text == '' ){
									text += name;
								} else {
									text += ',' + name;
								}
								
								id.push(this.list[1].list[this.priceActive.left].list[item].id);
							}
						})
			
						if (text != '价格') {
							const newName = this.priceActive.left == 1 ? 'total' : 'average';
			
							id = {
								type: newName,
								val: id
							};
						}
			
						this.priceActiveSure.right = this.$api.deepClone(this.priceActive.right);
					} else {
						text = '价格';
						this.priceActive.right = this.priceActiveSure.right = [];
					}
			
				}
				text = text.split(',');
				
				if( text.length == 1 ){
					text = text[0];
				} else {
					text = '多选';
				}
				
				// console.log(id)
			
				this.$set(this.list[1], 'title', text);
			
				this.$refs.uDropdown.close();
			
				this.sendResutl('price', id);
			},
			priceDel() {
				this.priceMin = this.priceMax = '';
				this.$set(this.priceActive, 'right', []);
				this.priceSure();
			},
			priceClosed() {
				this.priceActive.right = this.$api.deepClone(this.priceActiveSure.right);
			},
			typeChange(index) {
				if( index == 0 ){
					// this.typeChoose = [0];
					// console.log(index)
					if( this.typeChoose.indexOf(index) != -1){
						this.typeChoose.splice(this.typeChoose.indexOf(index),1);
					} else {
						this.typeChoose = [0];
					}
				} else {
					if( this.typeChoose.indexOf(0) != -1 ){
						this.typeChoose.splice(this.typeChoose.indexOf(0),1);
					}
					
					if( this.typeChoose.indexOf(index) != -1){
						this.typeChoose.splice(this.typeChoose.indexOf(index),1);
					} else {
						this.typeChoose.push(index);
					}
				}
			},
			typeClosed() {
				this.typeChoose = this.typeChooseSure;
			},
			typeDel() {
				this.typeChoose = this.typeChooseSure = [0];
				
				let id = this.list[2].list[this.typeChoose].id;
				this.$set(this.list[2], 'title', '户型');
				this.$refs.uDropdown.close();
				this.sendResutl('type', id);
			},
			typeSure() {
				let text = [];
				let id = [];
				
				if( this.typeChoose.length > 0 ){
					this.typeChooseSure = this.typeChoose;
				} else {
					this.typeChooseSure = this.typeChoose = [0];
				}
				
				this.typeChoose.map( item=>{
					text.push( this.list[2].list[item].name == '不限' ? '户型' : this.list[2].list[item].name );
					id.push( this.list[2].list[item].id );
				})
				
				if( text.length > 1 ){
					text = '多选';
				} else {
					text = text[0];
				}
				// text = text.join(',');
			
				this.$set(this.list[2], 'title', text);
				this.$refs.uDropdown.close();
				this.sendResutl('type', id);
			},
			getList() {
				let that = this
				console.log(this.city_no)
				// console.log(this.$http)
				this.$http.get('estates/getSeletList',{
					'city_no': this.city_no
				}).then(res=>{
					// console.log(res)
					that.list = res.data;
					this.addMoreType(res.data);
					if(that.default_data.site_center){
						that.list[0].title = that.default_data.site_center.district
					}
					
					this.initMoreChoose();
				})
			},
			// 外部传输更多选项
			initMoreChoose(){
				if( Object.keys(this.moreData).length > 0 ){
					
					this.list.map( (item,k)=>{
						if( item.title == '更多' ){
							item.list.map( (list,index)=>{
								if( list.type == this.moreData.type ){
									this.chooseMore(index,this.moreData.id, ()=>{
										this.moreSure(k,true);
									});
								}
							})
						}
					})
				}
			}
		},
	}
</script>

<style lang="scss" scoped>
	.van-dropdown-menu__bar{
		box-shadow: none;
	}
	
	.van-dropdown-item__content{
		max-height: none;
	}
	
	.van-dropdown-menu__title--active{
		font-size: 28rpx;
		color: rgba(254, 130, 30, 1);
	}
	
	.slot-content{
		background-color: #fff;
	}
	/* 更多 */
	.drop-more{
		width: 100%;
		height: 716rpx;
		position: relative;
	}
	
	.drop-more-item{
		padding: 3rpx 34rpx;
		box-sizing: border-box;
	}
	
	.drop-more-item:last-child{
		padding-bottom: 128rpx;
	}
	
	
	.drop-more-title{
		margin-bottom: 24rpx;
		font-size: 30rpx;
	}
	
	.drop-more-box{
		display: flex;
		flex-wrap: wrap;
	}
	
	.drop-more-tip{
		width: 206rpx;
		height: 64rpx;
		background-color: rgba(250, 250, 250, 1);
		margin-bottom: 24rpx;
		line-height: 64rpx;
		text-align: center;
	}
	
	.drop-more-tip:nth-child(3n-1){
		margin: 0 32rpx;
	}
	
	.drop-more-tip-active{
		background-color: rgba(254, 130, 30, 1);
		color: #fff;
	}
	
	.drop-more-wrap{
		height: 666rpx;
		overflow-y: scroll;
		-webkit-overflow-scrolling: touch;
	}
	
	.drop-btn{
		width: 100%;
		height: 128rpx;
		position: absolute;
		bottom: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		background-color: #fff;
		border-top: 1px solid rgba(240, 240, 240, 1);
	}
	
	.drop-del{
		margin-right: 26rpx;
	}
	
	.drop-btn-item{
		width: 328rpx;
		height: 88rpx;
		font-size: 30rpx;
	}
	
	.drop-price{
		width: 100%;
		height: 716rpx;
		position: relative;
	}
	
	.drop-price-box{
		width: 100%;
		height: 53rpx;
		display: flex;
	}
	
	.drop-price-left{
		width: 343rpx;
		border-right: 1px solid rgba(240, 240, 240, 1);
	}
	
	.drop-price-right{
		width: 406rpx;
	}
	
	.drop-price-left,
	.drop-price-right{
		height: 495rpx;
		overflow-y: scroll;
		-webkit-overflow-scrolling: touch;
	}
	
	.drop-price-right span{
		width: 100%;
		height: 100%;
		text-indent: none;
		overflow-y: scroll;
		-webkit-overflow-scrolling: touch;
		box-sizing: border-box;
		padding-right: 40rpx;
	}
	
	.drop-price-item{
		width: 100%;
		height: 104rpx;
		/* border-bottom: 0.01rpx solid rgba(240, 240, 240, 1); */
		box-sizing: border-box;
		padding-left: 40rpx;
		line-height: 104rpx;
		font-size: 32rpx;
		display: flex;
		justify-content: space-between;
	}
	
	.drop-price-item-active{
		color: rgba(254, 130, 30, 1);
	}
	
	.drop-price-input{
		width: 100%;
		height: 220rpx;
		position: absolute;
		bottom: 0;
		border-top: 1px solid rgba(240, 240, 240, 1);
		box-sizing: border-box;
		padding: 22rpx 32rpx 28rpx;
	}
	
	.drop-price-input-title{
		font-size: 32rpx;
	}
	
	.drop-price-input-price{
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	
	.drop-price-input-wrap{
		display: flex;
		justify-content: center;
		margin-top: 16rpx;
	}
	
	.drop-price-input-box{
		display: flex;
		align-items: center;
	}
	
	.drop-price-input-interval{
		height: 2rpx;
		width: 30rpx;
		background-color: rgba(117, 117, 117, 1);
		margin: 0 12rpx;
	}
	
	.drop-price-btn{
		width: 236rpx;
		height: 88rpx;
	}
	
	.drop-type{
		width: 100%;
		height: 716rpx;
		position: relative;
		box-sizing: border-box;
		padding: 0 32rpx;
	}
	
	.drop-type-wrap{
		height: 588rpx;
		overflow-y: scroll;
		-webkit-overflow-scrolling: touch;
	}
	
	.drp-type-box{
		width: 100%;
		height: 106rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 32rpx;
		border-bottom: 01rpx solid rgba(240, 240, 240, 1);
	}
	
	.drp-type-box .iconnewxuanzhongduoxuan{
		color: rgba(254, 130, 30, 1);
	}
	
	.text-none{
		
	}
</style>
