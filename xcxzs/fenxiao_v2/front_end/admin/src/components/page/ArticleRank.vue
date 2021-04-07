
<template>
    <div class="_container">
        <div class="tb-top" style="float:right;margin-bottom: 30px;">
            <el-form :inline="true" :model="searchData" class="form-serch" >
                <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
                <el-button type="info" icon="el-icon-circle-close" @click="openPage({url: '/admin/rank_city'})">返回</el-button>
            </el-form>
        </div>
        <!-- 表格 -->
        <el-table class="tb-title" :data="tableData" style="width: 100%">
        <el-table-column prop="rank" label="排名" width="70" align="center"></el-table-column>
        <el-table-column prop="name" label="文章名称" width="600" align="center"></el-table-column>
        <el-table-column prop="id" label="文章id" width="200" align="center"></el-table-column>
        <el-table-column prop="opt" label="操作" align="center">
            <template slot-scope="scope">
                <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
                <el-button type="danger" size="mini" @click="del(scope.row.id,scope.row.rank,scope.$index)">删除</el-button>
            </template>
        </el-table-column>
        </el-table>

        <!-- 新增弹窗部分 -->
        <el-dialog :title="'更改'" :visible.sync="dialogVisibleEdit" width="860px" :close-on-click-modal="false" @close="doEditCancel('formData')">
        <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
            <el-row>
                <el-col :span="12">
                    <el-form-item label="排名" prop="rank" :label-width="formLabelWidth">
                        <el-input-number label="排名" v-model="formData.rank"></el-input-number>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="12">
                  <el-row>
                    <el-col :span="24" >
                     <span @click="changeInnerShow">
                         <el-form-item label="请选择文章" prop="name" :label-width="formLabelWidth">
                           <el-input style="width:100% ;display: none" :disabled="true"  v-model="formData.id"  placeholder="请输入内容" ></el-input>
                           <el-input style="width:100%"   v-model="formData.name"  placeholder="请输入内容" ></el-input>
                         </el-form-item>
                   </span>
                    </el-col>
                  </el-row>
                  <Article :show.sync='innerVisible' :city="region_id" @innerFormData="innerFormData"> </Article>
                </el-col>
            </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
            <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
        </div>
        </el-dialog>
    </div>
</template>

<script>
var util = require("@/utils/util.js");
import baseMixin from  '@/mixin/baseMixin';
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import Article from '@/components/page/Article.vue';

export default {
    name: 'estates-rank',
    props: {
        type:{
            default () {
                return '1';
            }
        },
    },
    watch:{

    },
    components: {
        'estates-new': EstatesNew,
        'Article'   : Article
    },
    mixins: [baseMixin],
    computed: {

    },
    data () {
        return {
            region_id: 0,
            code_type:'',
            dialogVisibleEdit:false,
            innerVisible: false,
            formLabelWidth: "123px",
            tableData: [],
            formData: {
                rank: '0',
                deal: '',
                id:'',
                name:'',
            },
            rules:{},
            searchData:{

            },
        };
    },
    created(){
        if (this.$urlData) {
            if(this.$urlData.id) {
                this.region_id = this.$urlData.id;
            }
            if(this.$urlData.type) {
                this.code_type = this.$urlData.type;// 编码类型 city-城市 area-区域
            }
        }
        this.getList();
        this.clearInner()
    },
  innerFormData(e){
    this.formData.id = e.id;
    this.formData.name= e.name
  },
    methods: {
        getList(searchdata={}){   //获取所有数据，或按条件查找数据
            var that = this
            searchdata.type = that.type;
            searchdata.region_id = that.region_id;
            util.requests("post",{
                url:"news/getRank",
                data: searchdata
            }).then(res=>{
                if(res.code==1){
                    that.tableData = res.data
                }else{
                    util.Message.error(res.msg);
                }
            })
        },
        changeInnerShow(){
          this.innerVisible = true
        },
        doEdit(e={}){
            this.formData.deal = 'add';
            if(Object.keys(e).length>0){
                this.formData = Object.assign({},e);
                this.formData.deal = 'edit';
            } else {
                this.clearInner()
            }
            this.dialogVisibleEdit = true;
        },
        doEditCancel(formName){
            var that=this
            that.$refs[formName].resetFields()
            that.resetData()
            if(that.dialogVisibleEdit == true){
                that.dialogVisibleEdit = false
            }
        },
        doSubmit(formName){
            var that = this
            that.$refs[formName].validate((valid) => {
                if (valid) {
                    if(this.page_loading){
                        return;
                    }
                    that.page_loading = true;

                    that.formData.type = that.type;
                    that.formData.region_id = that.region_id;
                    util.requests("post",{
                        url:"news/editRank",
                        data:that.formData
                    }).then(res=>{
                        that.page_loading = false
                        if(res.code==1){
                            that.$message({ type: 'success', message: '操作成功!' });
                            that.dialogVisibleEdit = false;
                            that.getList()
                        }else{
                            that.$message({
                                type: 'error',
                                message: res.msg
                            });
                        }
                    });
                }else{
                    console.log('error submit!!');
                    return false;
                }
            })
        },
        del(id,rank,val){
            this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                util.requests("post",{
                    url: "news/editRank",
                    data: {id:id, region_id:this.region_id,rank:rank, deal:'del'}
                }).then(res => {
                    if(res.code==0){ alert("删除失败："+res.msg);return; }
                    this.tableData.splice(val,1)
                    this.$message({
                        type: 'success',
                        message: '删除成功!'
                    });
                })
            })
        },
        changeInnerShow(){
            this.innerVisible = true
        },
      innerFormData(e){
        this.formData.id = e.id;
        this.formData.name= e.name
      },
        clearInner(){
            this.formData.forname = '';
            this.formData.forid = 0;
        },
        openPage: util.openPage,
    },
};
</script>
