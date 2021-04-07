var localStore = require('./localStore.js');
var QQMapWX = require('./map/qqmap-wx-jssdk.js');

//小程序微信sdk
class miniQQMap{
	constructor(){
		this.mapSdk =new QQMapWX({
			key: 'WRFBZ-IH6K2-6EIU2-CP3TZ-Q42W3-NNFXQ'
		});
	}
	getSdk(){
		return this.mapSdk;
	}
	//获取城市名称
	getCityName(e){
		return new Promise((resolve)=>{
			this.mapSdk.reverseGeocoder({
				location: {
					latitude: e.lat,
					longitude: e.lng
				},
				success: function (res) {
					if(res.result&&Object.keys(res.result).length){
						resolve(res.result)
					}else{
						resolve('')
					}
				},
				fail: function (res) {
					console.log('error', res)
					resolve('')
				}
			})
		})
	}
}

// 获取用户当前定位
const getUserLocation = function(){
	let key = 'currentUsrLocation';
	return new Promise((resolve, reject) => {
		let user_location = localStore.localGet(key);
		if(user_location){
			resolve({
				lng: user_location.lng,
				lat: user_location.lat
			})
		}else{
			uni.getLocation({
				type: 'gcj02',
				success: function(res){
					user_location = {
						lng: res.longitude,
						lat: res.latitude
					}
					resolve(user_location)
					
					localStore.localSet(key, user_location, 3600*0.5);//缓存半小时用户位置结果
				},fail: function(){
					resolve('')
				}
			})
		}
	})	
}

const getAllCitys = function(that, refresh = 0){
	let key = 'city_list';
	let res = localStore.localGet(key);

	if(!res||refresh==1){
		return new Promise(resolve=>{
			that.$http.post('City/getCityList').then((res)=>{
				res = res.data? res.data : [];
				resolve(res);
				localStore.localSet(key, res, 3600*4);
			})
		});
	}else{
		return new Promise((resolve)=>{
			resolve(res);
		});
	}
}

//根据用户定位获取所在城市
const getUserLocationCity = function(that) {
	let key = 'user_location_city';
	let original_data = {
		city_no: 350200,
		city_name: '厦门'
	}
	let res = localStore.localGet(key);
	
	return new Promise((resolve)=>{
		if(res&&res.city_no){
			resolve(res)
		}else{
			getUserLocation().then((rs)=>{
				if(rs){
					let qqMap = new miniQQMap();
					
					Promise.all([
						getAllCitys(that),
						qqMap.getCityName({
							lng: rs.lng,
							lat: rs.lat
						})
					]).then((result) => {
						let city_name = result[1]&&result[1].ad_info&&result[1].ad_info.city? result[1].ad_info.city.replace('市','') : '';
						let city_no = '';
						for(let i in result[0]){
							let item = result[0][i];
							if(city_name&&item.cname.indexOf(city_name)!='-1'){
								city_no = item.id;
								break;
							}
						}
						if(!city_no){
							res = original_data
							resolve(res) //空数据不缓存保持实时动态更新
						}else{
							res = {
								city_no: city_no,
								city_name: city_name
							}
							
							resolve(res)
							localStore.localSet(key, res, 3600);
						}
					}).catch((error) => {
					  console.log('Promise.all',error)
					  
					  res = original_data
					  resolve(res) //空数据不缓存保持实时动态更新
					})
				}else{
					res = original_data
					resolve(res) //空数据不缓存保持实时动态更新
				}
			})
		}
	})
}


module.exports = {
	getUserLocation,
	getUserLocationCity,
	getAllCitys,//系统中所有的城市列表
	localStore
}