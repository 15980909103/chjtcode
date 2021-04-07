import store from '@/store'

const { body } = document

let _resetDataCallFun = null 
//一些基础的代码混入
export default {
  data(){
    return {
      _$origin_data :{}
    }
  },
  methods: {
    /**
     * //设置重置时要获取的原始数据
     * //示例
     * this.resetData({
     *   form: this.form,
     * })
     * @param {*} obj 
     */
    resetData(obj=null,callfun = null){
      if(obj){
        this.setDataArr({
          _$origin_data: this.$DeepCopy(obj)
        })
        _resetDataCallFun = callfun
      }else{
        let obj2 = this.$DeepCopy(this.$data._$origin_data)
        this.setDataArr({
            ...obj2
        })
        if(_resetDataCallFun){
          _resetDataCallFun()
        }
      }
    },
  }
}
