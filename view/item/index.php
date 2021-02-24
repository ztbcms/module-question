<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>题目列表</h3>
                <div>
                    <el-button @click="show=true;edit_item={}" type="primary">添加题目</el-button>
                </div>
                <div style="margin-top: 20px">
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
                                <el-button type="danger">删除</el-button>
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
                },
                methods: {
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
                            }, this.searchForm),
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