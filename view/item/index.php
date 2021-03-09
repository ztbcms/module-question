<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>题目列表</h3>
                <div style="display: flex;justify-content: space-between">
                    <div>
                        <el-form :inline="true" :model="search_where" class="demo-form-inline">
                            <el-form-item>
                                <el-input v-model="search_where.keyword" placeholder="请问题目关键字"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-select v-model="search_where.item_kind" placeholder="题目种类">
                                    <el-option label="全部" value=""></el-option>
                                    <el-option label="问卷" value="0"></el-option>
                                    <el-option label="试题" value="1"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item>
                                <el-select v-model="search_where.item_type" placeholder="题目类型">
                                    <el-option label="全部" value=""></el-option>
                                    <el-option label="单选" value="0"></el-option>
                                    <el-option label="多选" value="1"></el-option>
                                    <el-option label="填空" value="2"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="searchSubmit">查询</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                    <div>
                        <el-button @click="show=true;edit_item={}" type="primary">添加题目</el-button>
                    </div>
                </div>
                <div>
                    <el-table
                            :data="lists"
                            border
                            style="width: 100%">
                        <el-table-column
                                prop="item_id"
                                label="编号"
                                width="180">
                        </el-table-column>
                        <el-table-column
                                prop="content"
                                label="问题">
                        </el-table-column>
                        <el-table-column
                                prop="item_kind_text"
                                label="种类"
                                width="180">
                        </el-table-column>
                        <el-table-column
                                prop="item_type_text"
                                label="类型"
                                width="180">
                        </el-table-column>
                        <el-table-column
                                label="操作"
                                width="200">
                            <template slot-scope="props">
                                <el-button @click="editItemEvent(props.row)" type="primary">编辑</el-button>
                                <el-button @click="deleteItemEvent(props.row)" type="danger">删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div style="text-align: center;margin-top: 20px">
                        <el-pagination
                                background
                                @current-change="currentPageChange"
                                layout="prev, pager, next"
                                :current-page="currentPage"
                                :page-count="totalCount"
                                :page-size="pageSize"
                                :total="totalCount">
                        </el-pagination>
                    </div>
                </div>
            </el-card>
            <div>
                <question-edit-item @success="getList" :item="edit_item" @close="show=false"
                                    :show.sync="show"></question-edit-item>
            </div>
        </div>
    </div>
    {include file="/components/edit-item"}
    <!--    如果公共方法没有定义 window.__vueList 打开这个注释 -->
    {include file="/components/vue-list"}
    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                mixins: [window.__vueList],
                data: {
                    lists: [],
                    show: false,
                    edit_item: {},
                    search_where: {}
                },
                methods: {
                    searchSubmit: function () {
                        this.currentPage = 1
                        this.getList()
                    },
                    deleteItemEvent: function (item) {
                        var _this = this
                        this.$confirm("是否确认删除 " + item.content + ' ?').then(() => {
                            this.httpPost("{:api_url('question/item/delete')}", {
                                item_id: item.item_id
                            }, function (res) {
                                if (res.status) {
                                    _this.$message.success('删除成功')
                                    _this.getList()
                                } else {
                                    _this.$message.error(res.msg)
                                }
                            })
                        }).catch(err => {
                        })
                    },
                    editItemEvent: function (item) {
                        this.show = true
                        this.edit_item = item
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/item/index')}",
                            data: Object.assign({
                                page: this.currentPage,
                            }, this.search_where),
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                _this.handListData(res)
                            }
                        })
                    },
                },
                mounted: function () {
                    this.getList()
                },
            })
        })
    </script>
</div>