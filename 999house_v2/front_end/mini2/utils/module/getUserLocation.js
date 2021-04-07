// 获取用户当前定位
const getUserLocation = function(){
	uni.getLocation({
		type: 'gcj02'
	},success: function(res){
		console.log(res)
	},fail: function(){
		
	})
}

//获取当前定位所在城市
const getCurrentCity = function(val) {
	let key = 'current_city';
	const that = this;
	let original_data = {
		city_no: 350200,
		city_name: '厦门'
	}
	//const cityList = localStore.localGet('city-list', 3600*2);

	if(val){
		//@todo 根据用户当前经纬获取
		if(!val.city_no||!val.city_name){
			throw('参数格式错误');
		}
		localStore.localSet(key, val, 3600);
		return;
	}

	let res = localStore.localGet(key);
	
	if(res){
		let update_local = 0;
		if(!res.city_no){
			res = original_data
			update_local =1;
		}
		
		//resolve(res)
		
		if(update_local==1){
			$api.localSet(key, res, 3600*8);
		}
	}else{
		uni.getLocation(OBJECT)
	}
})

module.exports = {
	getUserLocation,
	getCurrentCity
}