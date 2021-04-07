// 本地缓存
const localStore = {
	localSet: (key, value, expires=null)=>{
		if (expires) {
			expires = parseInt(expires)
			let params = { value, expires };
			// 记录何时将值存入缓存，秒级
			let nowtime = parseInt(Date.parse(new Date())/1000);
			var data = Object.assign(params, { startTime: nowtime});
			uni.setStorageSync(key, JSON.stringify(data));
		} else {
			if (Object.prototype.toString.call(value) == '[object Object]') {
				value = JSON.stringify(value);
			}
			if (Object.prototype.toString.call(value) == '[object Array]') {
				value = JSON.stringify(value);
			}
			uni.setStorageSync(key, value);
		}
	},
	localGet: (key) => {
		let item = uni.getStorageSync(key);
		// 先将拿到的试着进行json转为对象的形式
		try {
			item = JSON.parse(item);
		} catch (error) {
			// eslint-disable-next-line no-self-assign
			item = null;
		}
		// 如果有startTime的值，说明设置了失效时间
		if (item && item.startTime) {
			let nowtime = parseInt(Date.parse(new Date())/1000);
			// 如果大于就是过期了，如果小于或等于就还没过期
			if (nowtime - parseInt(item.startTime) > parseInt(item.expires)) {
				uni.removeStorageSync(key);
				return false;
			} else {
				if(!item.value){
					return null
				}
				if(typeof(item.value)=='object'&&Object.keys(item.value).length==0){
					return null
				}
				return item.value;
			}
		} else {
			return item;
		}
	},
	localDel: (key) => {
		uni.removeStorageSync(key);
	},
}

module.exports = {
	...localStore
}