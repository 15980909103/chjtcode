

// 调用时，字段名以字符串的形式传参，如Tree(source, 'id', 'parentId', 'children' ,start_idval)

/**
 * 一维数组转树形结构
 * @param {*} source 要转换的数组
 * @param {*} id_key 
 * @param {*} parentId_key 
 * @param {*} children_key 
 * @param {*} start_idval 第一层从parentId = start_idval 开始
 */
const Tree= {
	arrayTransTree:function  (source, id_key='id', parentId_key='parentId' , children_key='children' ,start_idval='0'){   
		let cloneData = JSON.parse(JSON.stringify(source))
		return cloneData.filter(father=>{
				let branchArr = cloneData.filter(child => father[id_key] == child[parentId_key]);
				branchArr.length>0 ? father[children_key] = branchArr : ''
				return father[parentId_key] == start_idval     // 如果第一层不是从parentId=0开始，请自行修改
		})
	},

	
	// tree 为当前树的数据源  
	treeTransArray: function(tree) {
		let cloneData = JSON.parse(JSON.stringify(tree))
		var r=[];
		if (Array.isArray(cloneData)) {
		  for (var i=0, l=cloneData.length; i<l; i++) {
			r.push(cloneData[i]); // 取每项数据放入一个新数组
			if (Array.isArray(cloneData[i]["children"])&&cloneData[i]["children"].length>0)
			 // 若存在children则递归调用，把数据拼接到新数组中，并且删除该children
			  r = r.concat(Tree.treeTransArray(cloneData[i]["children"]));
				delete cloneData[i]["children"]
		  }
		} 
		return r;
	},
}




export default Tree