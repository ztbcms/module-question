<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <div slot="header">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item><a href="{:api_url('/question/examination/index')}">试卷列表</a>
                        </el-breadcrumb-item>
                        <el-breadcrumb-item>{{examination.title}}</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div style="display: flex;justify-content: space-between;margin-top:10px;">
                    <div>
                        <el-form :inline="true" :model="search_where" class="demo-form-inline">
                            <el-form-item>
                                <el-input v-model="search_where.keyword" placeholder="请输入用户姓名"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="searchSubmit">查询</el-button>
                            </el-form-item>
                        </el-form>
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
                                width="80">
                        </el-table-column>
                        <el-table-column
                                prop="target"
                                label="姓名"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                prop="proportion"
                                label="正确/答题"
                                min-width="100">
                        </el-table-column>
                        <el-table-column
                                prop="create_time"
                                label="起始时间"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                prop="confirm_time"
                                label="完成时间"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                label="操作"
                                width="120">
                            <template slot-scope="props">
                                <el-button @click="detailEvent(props.row)" type="primary">详情</el-button>
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
                    examination_id: "<?php echo $_GET['examination_id'] ?? 0?>",
                    lists: [],
                    show: false,
                    edit_item: {},
                    examination: {},
                    search_where: {},
                    answer_correct: '',
                    proportion: ''
                },
                methods: {
                    //打开详情页面
                    detailEvent: function (item) {
                        location.href = "{:api_url('question/examination/answer_records_detail',['examination_answer_id'=>''])}" + item.examination_answer_id
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/examination/answer_records')}",
                            data: Object.assign({
                                examination_id: this.examination_id,
                                page: this.currentPage,
                            }, this.search_where),
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                _this.examination = res.data.examination
                                _this.handListData({data: res.data.lists})
                            }
                        })
                    },
                    searchSubmit: function () {
                        this.getList()
                    }
                },
                mounted: function () {
                    this.getList()
                },
            })
        })
    </script>
</div>