<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <div>
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item><a href="{:api_url('/question/questionnaire/index')}">问卷列表</a></el-breadcrumb-item>
                        <el-breadcrumb-item>{{questionnaire.title}}</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div style="margin-top: 20px">
                    <el-table
                            :data="lists"
                            border
                            style="width: 100%">
                        <el-table-column
                                prop="questionnaire_id"
                                label="编号"
                                width="80">
                        </el-table-column>
                        <el-table-column
                                prop="target"
                                label="姓名"
                                min-width="200"
                        >
                        </el-table-column>
                        <el-table-column
                                align="center"
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
                                min-width="200">
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
                    questionnaire_id: "<?php echo $_GET['questionnaire_id'] ?? 0?>",
                    lists: [],
                    show: false,
                    edit_item: {},
                    questionnaire: {}
                },
                methods: {
                    detailEvent: function (item) {
                        location.href = "{:api_url('question/questionnaire/answer_records_detail',['questionnaire_answer_id'=>''])}" + item.questionnaire_answer_id
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/questionnaire/answer_records')}",
                            data: Object.assign({
                                questionnaire_id: this.questionnaire_id,
                                page: this.currentPage,
                            }, this.searchForm),
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                _this.questionnaire = res.data.questionnaire
                                _this.handListData({data: res.data.lists})
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