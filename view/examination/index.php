<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <div slot="header" class="clearfix">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item>试卷列表</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div style="display: flex;justify-content: space-between">
                    <div>
                        <el-form :inline="true" :model="search_where" class="demo-form-inline">
                            <el-form-item>
                                <el-input v-model="search_where.keyword" placeholder="请输入试卷标题"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="searchSubmit">查询</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                    <div>
                        <el-link href="{:url('question/examination/edit')}">
                            <el-button type="primary">添加试卷</el-button>
                        </el-link>
                    </div>
                </div>
                <div style="margin-top: 20px">
                    <el-table
                            :data="lists"
                            border
                            style="width: 100%">
                        <el-table-column
                                prop="examination_id"
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
                                label="提交数量"
                                width="180">
                            <template slot-scope="props">
                                <el-link type="primary" @click="submitPage(props.row)">{{props.row.submit_count}}
                                </el-link>
                            </template>
                        </el-table-column>
                        <el-table-column
                                prop="create_time"
                                label="创建时间"
                                width="200">
                        </el-table-column>
                        <el-table-column
                                label="操作"
                                width="260">
                            <template slot-scope="props">
                                <el-button @click="editEvent(props.row)" type="primary">编辑</el-button>
                                <el-button @click="analysisEvent(props.row)" type="success">分析</el-button>
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
                    search_where: {}
                },
                methods: {
                    //查询
                    searchSubmit: function () {
                        this.currentPage = 1
                        this.getList()
                    },
                    //删除一列
                    deleteEvent: function (item) {
                        var _this = this
                        this.$confirm("是否确认删除 " + item.title + ' ?').then(() => {
                            this.httpPost("{:api_url('question/examination/delete')}", {
                                examination_id: item.examination_id
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
                    //用户提交的试卷列表页面
                    submitPage: function (item) {
                        location.href = "{:api_url('question/examination/answer_records',['examination_id'=>''])}" + item.examination_id
                    },
                    //试卷分析页
                    analysisEvent: function (item) {
                        location.href = "{:api_url('question/examination/analysis',['examination_id'=>''])}" + item.examination_id
                    },
                    //编辑试卷页
                    editEvent: function (item) {
                        location.href = "{:api_url('question/examination/edit',['examination_id'=>''])}" + item.examination_id
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/examination/index')}",
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