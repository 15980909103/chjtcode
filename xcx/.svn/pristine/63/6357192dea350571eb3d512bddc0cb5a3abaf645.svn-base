
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
        <el-table-column prop="estate_id" label="楼盘ID" width="180" align="center"></el-table-column>
        <el-table-column prop="estate" label="楼盘" width="180" align="center"></el-table-column>
        <el-table-column prop="region" label="地区" width="180" align="center"></el-table-column>
        <el-table-column prop="opt" label="操作" align="center">
            <template slot-scope="scope">
                <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
                <el-button type="danger" size="mini" @click="del(scope.row.estate_id,scope.$index)">删除</el-button>
            </template>
        </el-table-column>
        </el-table>

        <!-- 新增弹窗部分 -->
        <el-dialog :title="'排名'" :visible.sync="dialogVisibleEdit" width="860px" :close-on-click-modal="false" @close="doEditCancel('formData')">
        <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
            <el-row>
                <el-col :span="12">
                    <el-form-item label="排名" prop="rank" :label-width="formLabelWidth">
                        <el-input-number label="排名" v-model="formData.rank" :disabled="formData.deal=='edit' ? true : false"></el-input-number>
                    </el-form-item>
                </el-col>  
            </el-row>
            <el-row>
                <el-col :span="12">
                    <el-form-item label="选择新房" prop="forid" :label-width="formLabelWidth">
                    <el-row>
                        <el-col :span="18">
                        <span @click="changeInnerShow">
                            <el-input style="width:100%;display:none;"  v-model="formData.forid" placeholder="请选择新房"></el-input>
                            <el-input style="width:100%" :disabled='true'  v-model="formData.forname" placeholder="请选择新房"></el-input>
                        </span>
                        </el-col>
                        <el-col :span="4" style="text-align: right;">
                        <el-button  @click="clearInner">清空</el-button>
                        </el-col>
                    </el-row>
                    </el-form-item>

                    <estates-new v-if="code_type=='city'" :region_no='region_id' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>
                    <estates-new v-if="code_type=='area'" :area_no='region_id' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>
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
                estate_id: '',
                deal: '',
                forid:'',
                forname:'',
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
    methods: {
        getList(searchdata={}){   //获取所有数据，或按条件查找数据
            var that = this
            searchdata.type = that.type;
            searchdata.region_id = that.region_id;
            util.requests("post",{
                url:"estates/getRank",
                data: searchdata
            }).then(res=>{
                if(res.code==1){
                    that.tableData = res.data
                }else{
                    util.Message.error(res.msg);
                }
            })
        },
        doEdit(e={}){
            this.formData.deal = 'add';
            if(Object.keys(e).length>0){
                this.formData = Object.assign({},e);
                this.formData.forname = e.estate;
                this.formData.forid = e.estate_id;
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
                        url:"estates/editRank",
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
        del(id,val){
            this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                util.requests("post",{
                    url: "estates/editRank",
                    data: {forid:id, region_id:this.region_id, deal:'del', type:this.type}
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
            this.formData.forid = e.id
            this.formData.forname = e.name
        },
        clearInner(){
            this.formData.forname = '';
            this.formData.forid = 0;
        },
        openPage: util.openPage,
    },
};
</script>
