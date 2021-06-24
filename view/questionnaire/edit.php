<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>问卷列表</h3>
            <div style="margin-top: 20px">
                <el-form ref="form" :model="form" label-width="80px">
                    <el-form-item label="问卷标题">
                        <el-input style="width: 500px" v-model="form.title"></el-input>
                    </el-form-item>
                    <el-form-item label="问卷简介">
                        <el-input style="width: 500px" type="textarea" placeholder="简单描述问卷"
                                  v-model="form.description"></el-input>
                    </el-form-item>
                    <el-form-item label="问卷题目">
                        <div>
                            <div style="display: flex;align-items: center">
                                <el-select style="width: 500px" v-model="select_item" @change="itemChangeEvent"
                                           clearable
                                           multiple
                                           :remote-method="getItemList"
                                           :loading="loading" @remove-tag="removeItemOption" remote filterable
                                           placeholder="请选择问卷题目">
                                    <el-option v-for="(item,index) in items" :key="item.item_id" :label="item.content"
                                               :value="item.item_id"></el-option>
                                </el-select>
                                <i @click="show_edit_time=true"
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
        <question-edit-item @success="getItemList" @close="show_edit_time=false"
                            :show.sync="show_edit_time"></question-edit-item>
    </div>

    {include file="/components/edit-item"}
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
                    questionnaire_id: "<?php echo $_GET['questionnaire_id'] ?? 0;?>",
                    form: {},
                    loading: false,
                    items: [],
                    select_item: "",
                    select_item_list: []
                },
                methods: {
                    backEvent: function () {
                        history.back()
                    },
                    getEditInfo: function () {
                        var _this = this
                        this.httpGet('{:api_url("question/questionnaire/edit")}', {
                            questionnaire_id: this.questionnaire_id
                        }, function (res) {
                            if (res.status) {
                                var detail = res.data.detail
                                _this.form = {
                                    questionnaire_id: detail.questionnaire_id,
                                    title: detail.title,
                                    description: detail.description,
                                }
                                _this.select_item_list = detail.item_list
                            }
                        })
                    },
                    onSubmit: function () {
                        var postData = this.form
                        if (this.select_item_list.length === 0) {
                            this.$message.error('请选择问卷题目');
                            return
                        }
                        var item_ids = []
                        this.select_item_list.forEach(item => {
                            item_ids.push(item.item_id)
                        })
                        postData['item_ids'] = item_ids
                        console.log('postData', postData)
                        var _this = this
                        this.httpPost('{:api_url("question/questionnaire/edit")}', postData, function (res) {
                            if (res.status) {
                                _this.$message.success('操作成功')
                                setTimeout(function () {
                                    location.href = "{:url('question/questionnaire/index')}"
                                }, 1500)
                            } else {
                                _this.$message.error(res.msg)
                            }
                        })
                    },
                    //删除问卷的一个题目
                    deleteItemEvent: function (index) {
                        this.select_item_list.splice(index, 1)
                    },
                    //多选模式下移除tag时触发
                    removeItemOption: function (item_id) {
                        console.log('removeItemOption', item_id)
                        var index = this.select_item_list.findIndex(item => {
                            return parseInt(item.item_id) === parseInt(item_id)
                        })
                        if (index >= 0) {
                            this.deleteItemEvent(index)
                        }
                    },
                    //为问卷增加一个题目
                    itemChangeEvent: function (item_ids) {
                        console.log('itemChangeEvent', item_ids)
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
                    //获取问卷题目列表
                    getItemList: function (query) {
                        console.log('getItemList', query)
                        var _this = this
                        _this.loading = true
                        this.httpGet('{:api_url("question/questionnaire/getItemList")}', {query: query}, function (res) {
                            _this.loading = false
                            if (res.status) {
                                var items = res.data.items
                                _this.items = items
                            }
                        })
                    },
                    //渲染问卷题目表格
                    initSortable() {
                        const tbody = document.querySelector('.el-table__body-wrapper tbody')
                        const _this = this
                        Sortable.create(tbody, {
                            onEnd({newIndex, oldIndex}) {
                                console.log({newIndex, oldIndex})
                                // const currRow = _this.select_item_list.splice(oldIndex, 1)[0]
                                // _this.select_item_list.splice(newIndex, 0, currRow)

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
                    }
                },
                mounted: function () {
                    this.initSortable()
                    window.__GLOBAL_ELEMENT_LOADING_INSTANCE_ENABLE = false
                    this.getItemList()
                    if (parseInt(this.questionnaire_id) > 0) {
                        this.getEditInfo()
                    }
                },
            })
        })
    </script>
</div>