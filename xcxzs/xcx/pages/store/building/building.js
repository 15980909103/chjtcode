let City = require('../../../dist/wx-index-list/allcity.js');
let Region = require('../../../dist/wx-index-list/region.js');
const app = getApp();
var id=0;
var page=1;
var parameter={};
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    is_showdetail:false,
    sortList: [  //排序栏目数据
      { text: '城市', is_show: false,className:"my-font2" },
      { text: '区域', is_show: false },
      { text: '价格', is_show: false },
      { text: '户型', is_show: false },
      { text: '更多', is_show: false },
      { text: '', is_show: false },
    ],
    sortData:[{},{},{},{},{},{}],  //排序栏数据
    sort2Index:0, //下标为2选中的索引
    lpList: [],
    config: {
      mycity:false,   //是否显示我的城市
      horizontal: true, // 第一个选项是否横排显示
      animation: true, // 过渡动画是否开启
      search: false, // 是否开启搜索
      searchHeight: 45, // 搜索条高度
      suctionTop: true // 是否开启标题吸顶
    },

    queryBar: []
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    id = options.id;
    page=1;
    parameter={};
    //获取楼盘数据
    app.ajax("buildingAjax/getBuildingHome", { agent_id: id}, function (res) {
      var data = res.data;
      wx.setNavigationBarTitle({ title: data.agentName })
      //获取组织排序栏数据
      var sortData = _this.data.sortList;
      sortData[0].data = City;
      sortData[1].data = Region;
      sortData[2].data = data.sortData['2']
      sortData[3].data = data.sortData['3']
      sortData[4].data = data.sortData['4']
      sortData[5].data = data.sortData['5']
      _this.setData({ sortData: sortData })
    })
    _this.onGetDataInfo();
  },
  onSearch(event){
    var searchText = event.detail ? event.detail:'';
    parameter = { ...parameter, searchText: searchText};
    page=1;
    this.onGetDataInfo();
  },
  //ajax获取数据
  onGetDataInfo() {
    var _this = this;
    const params = {
      ...parameter,
      moreData: JSON.stringify(parameter.moreData),
      agent_id: id,
      page
    };
    if (!params.moreData) {
      delete(params.moreData);
    }
    params._header = {
      "Accept": "application/json,*/*;",
       "Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"
    }
    app.ajax("buildingAjax/getBuildingData", params,function (res) {
      var data = res.data;
      if (data.success) {
        if (data.page == 1){
          _this.setData({ lpList: data.data});
        }else{
          _this.setData({ lpList: _this.data.lpList.concat(data.data) });
        }
        page++;
      }
    })
  },
  //排序选择
  onSortBtn(event){
    var _index = event.currentTarget.dataset.index;
    var sortList = this.data.sortList;
    if (sortList[_index].is_show){
      sortList[_index].is_show = !sortList[_index].is_show;
    }else{
      this.onPackUpAll();
      sortList[_index].is_show = !sortList[_index].is_show;
    }
    this.setData({ sortList: sortList })
    this.checkIsDetail();
  },
  //收起所有排序选择
  onPackUpAll(){
    var sortList = this.data.sortList;
    for (var i in sortList){
      sortList[i].is_show=false;
    }
    this.setData({ sortList: sortList })
    this.checkIsDetail();
  },
  //检测is_showdetail是否显示
  checkIsDetail(){
    var sortList = this.data.sortList;
    for (var i in sortList) {
      if (sortList[i].is_show){
        this.setData({ is_showdetail:true});
        return false;
      }
    }
    this.setData({ is_showdetail: false });
  },
  //收起搜索条件
  closeSelect(){
    var sortList = this.data.sortList;
    for (var i in sortList) {
      sortList[i].is_show=false;
    }
    this.setData({ sortList:sortList,is_showdetail:false });
  },
  //价格排序
  onSort2(event){
    var index = event.currentTarget.dataset.index;
    this.setData({ sort2Index:index})
  },
  //更多筛选
  onSort4(event){
    var index = event.currentTarget.dataset.index;
    var idx = event.currentTarget.dataset.idx;
    var sortData = this.data.sortData;
    sortData[4].data[index].data[idx].is_checked = !sortData[4].data[index].data[idx].is_checked
    this.setData({ sortData: sortData })
  },
  //更多重置筛选
  onSort4Reset(){
    var sortData = this.data.sortData;
    for (var i in sortData[4].data){
      for (var j in sortData[4].data[i].data){
        sortData[4].data[i].data[j].is_checked=false
      }
    }
    this.setData({ sortData: sortData })
  },
  //更多确定按钮
  onSubmit(event){
    var btnidx = event.currentTarget.dataset.btnidx;
    var name = btnidx == 0? event.detail.name: event.currentTarget.dataset.name;
    var query = null;
    switch (btnidx){
      case '0':     //城市选择
        var sortList = this.data.sortList;
        sortList[0].text = name;
        sortList[0].className = "my-font" + name.length>4? 4: name.length;
        this.setData({ sortList: sortList })
        query = {city: name };
        break;
      case '1':   //区域选择
        query = {area: name };
        break;
      case '2':   //价格选择
        query = {fold: name };
        break;
      case '3':   //户型选择
        query = {house_type: name };
        break;
      case '4':   //更多选择
        var moreData = this.data.sortList[4].data.map((item, index) => {
          return {
            title: item.title,
            data: item.data.filter(data => data.is_checked)
          }
        });
        query = {moreData};
        break;
      case '5':   //排序选择
        query = {my_sort: name };
        break;
    }
    page = 1;
    this.setQueryData(query);
    this.closeSelect();
  },
  //楼盘详情
  onHouseDetail(event) {
    var building_id = event.currentTarget.dataset.id;
    wx.navigateTo({ url: '/pages/store/house_detail/house_detail?id=' + building_id + "&agent_id=" + id })
  },
  setQueryData(query) {
    const keys = Object.keys(query);
    const queryKey = keys[0];

    parameter = {
      ...parameter,
      [queryKey]: query[queryKey]
    };
    this.setQueryBar();
  },
  setQueryBar() {
    const result = [];
    Object.keys(parameter).forEach((key, index) => {
      if (!Array.isArray(parameter[key])) {
        if (parameter[key]) {
          result.push({
            key,
            label: key == 'fold' ? this.getFoldLabel(parameter[key]): parameter[key]
          });
        }
      } else {
        parameter[key].forEach((type, typeIndex) => {
          type.data.forEach((item, index) => {
            if (item.is_checked) {
              result.push({
                key: `${key}-${typeIndex}-${index}`,
                label: item.title
              });
            }
          });
        });
      }
    });

    this.setData({
      queryBar: result
    })
    this.onGetDataInfo();
  },
  getFoldLabel(value) {
    var label = '';
    this.data.sortList[2].data.forEach(list => {
      list.data.forEach(item => {
        if (item.val == value) {
          label = item.name;
          return item.name;
        }
      })
    });
    return label;
  },
  removeQueryItem(event) {
    const key = event.currentTarget.dataset.key;
    if (key.match('moreData')) {
      const params = key.split('-');
      const listIndex = params[1];
      const itemIndex = params[2];
      const data = this.data.sortData[4].data;
      data[listIndex].data[itemIndex].is_checked = false;
      this.setData({
        moreData: data
      }) ;
      parameter.moreData[listIndex].data.splice(itemIndex, 1);
    } else {
      parameter = {
        ...parameter,
        [key]: ''
      }
    }
    this.setQueryBar();
  }
})
