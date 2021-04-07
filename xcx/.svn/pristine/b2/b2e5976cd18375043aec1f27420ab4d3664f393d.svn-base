
//一些基础的代码混入
var util = require("@/utils/util.js");

export default {
  data(){
    return {
      const_estates_new_sale_status:[],
      const_house_purpose:[],
      const_orientation:[],
      const_rooms:[],
    }
  },
  methods: {
    // 初始化新房销售状态
    getEstatesNewSaleStatus(){
      var that = this
      util.requests("post",{
        url:"estates/getEstatesNewSaleStatus",
      }).then(res=>{
        that.const_estates_new_sale_status = res.data
      })
    },
    // 初始化几居室列
    getRooms(){
      var that = this
      util.requests("post",{
        url:"estates/getRooms",
      }).then(res=>{
        that.const_rooms = res.data
      })
    },
    // 初始化朝向列
    getOrientation(){
      var that = this
      util.requests("post",{
        url:"estates/getOrientation",
      }).then(res=>{
        that.const_orientation = res.data
      })
    },
    // 初始化建筑用途列
    getHousePurpose(){
      var that = this
      util.requests("post",{
        url:"estates/getHousePurpose",
      }).then(res=>{
        that.const_house_purpose = res.data
      })
    },
  }
}
