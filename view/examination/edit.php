<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <div slot="header" class="clearfix">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item><a href="{:url('question/examination/index')}">试卷列表</a></el-breadcrumb-item>
                    <el-breadcrumb-item>{{examination_id>0?'编辑试卷':'新增试卷'}}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
            <div style="margin-top: 20px">
                <el-form ref="form" :model="form" label-width="80px">
                    <el-form-item label="试卷标题">
                        <el-input style="width: 500px" v-model="form.title"></el-input>
                    </el-form-item>
                    <el-form-item label="答题方式">
                        <el-radio-group v-model="form.type">
                            <el-radio :label="0">顺序</el-radio>
                            <el-radio :label="1">
                                随机
                                <el-input style="width: 200px" v-model="form.number" placeholder="请输入随机答题的数量"
                                          v-bind:disabled="form.type==0"></el-input>
                            </el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="答题简介">
                        <el-input style="width: 500px" type="textarea" placeholder="简单描述答题"
                                  v-model="form.description"></el-input>
                    </el-form-item>
                    <el-form-item label="答题题目">
                        <div>
                            <div style="display: flex;align-items: center">
                                <el-select style="width: 500px" v-model="select_item" @change="itemChangeEvent"
                                           clearable
                                           multiple
                                           :remote-method="getItemList"
                                           :loading="loading" @remove-tag="removeItemOption" remote filterable
                                           placeholder="请选择答题题目">
                                    <el-option v-for="(item,index) in items" :key="item.item_id" :label="item.content"
                                               :value="item.item_id"></el-option>
                                </el-select>
                                <i @click="addItem"
                                   style="margin-left: 10px;display: flex;cursor: pointer;color: #409EFF;font-size: 24px;line-height: 24px"
                                   class="el-icon-circle-plus"></i>
                                <small style="color: #F56C6C;margin-left: 10px">选择框只作为选择工具，最终提交数据以下面表格内容为准</small>
                            </div>
                        </div>
                        <div style="margin-top: 20px">
                            <el-table
                                    size="mini"
                                    :data="select_item_list"
                                    border
                                    style="width: 800px">
                                <el-table-column
                                        align="center"
                                        width="80"
                                        label="题号">
                                    <template slot-scope="props">
                                        <div style="cursor: pointer">
                                            <i class="el-icon-s-operation"></i>
                                            {{props.$index+1}}
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                        prop="content"
                                        align="center"
                                        label="题目">
                                </el-table-column>
                                <el-table-column
                                        prop="item_type_text"
                                        align="center"
                                        width="180"
                                        label="类型">
                                </el-table-column>
                                <el-table-column
                                        align="center"
                                        label="操作"
                                        width="180">
                                    <template slot-scope="props">
                                        <el-button @click="deleteItemEvent(props.$index)" size="mini" type="danger">删除
                                        </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="onSubmit">提交</el-button>
                        <el-button @click="backEvent">返回</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </el-card>
    </div>

    <!-- CDNJS :: Sortable (https://cdnjs.com/) -->
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                mixins: [],
                computed: {},
                data: {
                    show_edit_time: false,
                    examination_id: "<?php echo $_GET['examination_id'] ?? 0;?>",
                    form: {
                        type: 0
                    },
                    loading: false,
                    items: [],
                    select_item: "",
                    select_item_list: [],
                },
                methods: {
                    backEvent: function () {
                        history.back()
                    },
                    //获取试卷信息
                    getEditInfo: function () {
                        var _this = this
                        this.httpGet('{:api_url("question/examination/edit")}', {
                            examination_id: this.examination_id
                        }, function (res) {
                            if (res.status) {
                                var detail = res.data.detail
                                _this.form = {
                                    examination_id: detail.examination_id,
                                    title: detail.title,
                                    description: detail.description,
                                    type: parseInt(detail.type),
                                    number: parseInt(detail.number),
                                }
                                _this.select_item_list = detail.item_list
                            }
                        })
                    },
                    onSubmit: function () {
                        var postData = this.form
                        if (this.select_item_list.length === 0) {
                            this.$message.error('请选择答题题目');
                            return
                        }
                        if (postData.title === undefined || postData.title === '') {
                            this.$message.error('请填写答题标题');
                            return
                        }
                        if (postData.description === undefined || postData.description === '') {
                            this.$message.error('请填写答题简介');
                            return
                        }
                        var item_ids = []
                        this.select_item_list.forEach(item => {
                            item_ids.push(item.item_id)
                        })
                        postData['item_ids'] = item_ids
                        var _this = this
                        this.httpPost('{:api_url("question/examination/edit")}', postData, function (res) {
                            if (res.status) {
                                _this.$message.success('操作成功')
                                setTimeout(function () {
                                    location.href = "{:url('question/examination/index')}"
                                }, 1500)
                            } else {
                                _this.$message.error(res.msg)
                            }
                        })
                    },
                    //删除一个选项
                    deleteItemEvent: function (index) {
                        this.select_item_list.splice(index, 1)
                    },
                    //删除一个题目
                    removeItemOption: function (item_id) {
                        var index = this.select_item_list.findIndex(item => {
                            return parseInt(item.item_id) === parseInt(item_id)
                        })
                        if (index >= 0) {
                            this.deleteItemEvent(index)
                        }
                    },
                    //添加答题题目
                    itemChangeEvent: function (item_ids) {
                        var _this = this
                        item_ids.forEach(item_id => {
                            var item = _this.items.find(item => {
                                return parseInt(item.item_id) === parseInt(item_id)
                            })
                            if (item) {
                                var is_exist = _this.select_item_list.findIndex(i => {
                                    return parseInt(i.item_id) === parseInt(item.item_id)
                                })
                                if (is_exist === -1) {
                                    _this.select_item_list.push(item)
                                }
                            }
                        })
                    },
                    //获取答题题目类型
                    getItemList: function (query) {
                        console.log('getItemList', query)
                        var _this = this
                        _this.loading = true
                        this.httpGet('{:api_url("question/examination/getItemList")}', {query: query}, function (res) {
                            _this.loading = false
                            if (res.status) {
                                var items = res.data.items
                                _this.items = items
                            }
                        })
                    },
                    //渲染题目表格
                    initSortable() {
                        const tbody = document.querySelector('.el-table__body-wrapper tbody')
                        const _this = this
                        Sortable.create(tbody, {
                            onEnd({newIndex, oldIndex}) {

                                const currRow = _this.select_item_list.splice(oldIndex, 1)[0]
                                var new_select_item_list = []
                                _this.select_item_list.forEach(item => {
                                    new_select_item_list.push(item)
                                })
                                new_select_item_list.splice(newIndex, 0, currRow)
                                _this.select_item_list = []
                                setTimeout(function () {
                                    _this.select_item_list = new_select_item_list
                                }, 10)
                            }
                        })
                    },
                    //添加题目
                    addItem: function () {
                        var that = this;
                        var url = "{:api_url('/question/item/addQuestion',['item_kind'=>1])}";
                        layer.open({
                            type: 2,
                            title: '新增题目',
                            shadeClose: true,
                            area: ['800px', '600px'],
                            content: url,
                            end: function () {
                                that.getItemList()
                            }
                        });
                    }
                },
                mounted: function () {
                    this.initSortable()
                    window.__GLOBAL_ELEMENT_LOADING_INSTANCE_ENABLE = false
                    this.getItemList()
                    if (parseInt(this.examination_id) > 0) {
                        this.getEditInfo()
                    }
                },
            })
        })
    </script>
</div>