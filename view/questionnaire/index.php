<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>问卷列表</h3>
                <div>
                    <el-link href="{:url('question/questionnaire/edit')}">
                        <el-button type="primary">添加问卷</el-button>
                    </el-link>
                </div>
                <div style="margin-top: 20px">
                    <el-table
                            :data="lists"
                            border
                            style="width: 100%">
                        <el-table-column
                                prop="questionnaire_id"
                                label="编号"
                                width="180">
                        </el-table-column>
                        <el-table-column
                                prop="title"
                                label="问题">
                        </el-table-column>
                        <el-table-column
                                align="center"
                                prop="item_count"
                                label="问题数量"
                                width="180">
                        </el-table-column>
                        <el-table-column
                                align="center"
                                prop="submit_count"
                                label="提交数量"
                                width="180">
                        </el-table-column>
                        <el-table-column
                                prop="create_time"
                                label="创建时间"
                                width="200">
                        </el-table-column>
                        <el-table-column
                                label="操作"
                                width="200">
                            <template slot-scope="props">
                                <el-button @click="editEvent(props.row)" type="primary">编辑</el-button>
                                <el-button @click="deleteEvent(props.row)" type="danger">删除</el-button>
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
        </div>
    </div>
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
                    deleteEvent: function (item) {
                        var _this = this
                        this.$confirm("是否确认删除 " + item.title + ' ?').then(() => {
                            this.httpPost("{:api_url('question/questionnaire/delete')}", {
                                questionnaire_id: item.questionnaire_id
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
                    editEvent: function (item) {
                        location.href = "{:api_url('question/questionnaire/edit',['questionnaire_id'=>''])}" + item.questionnaire_id
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/questionnaire/index')}",
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