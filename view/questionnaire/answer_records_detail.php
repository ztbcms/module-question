<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>提交记录</h3>
                <div>
                    <div>
                        <span style="font-weight: bold">用户：</span>{{questionnaire_answer.target}}
                    </div>
                    <div style="display: flex;align-items: center">
                        <span style="font-weight: bold">问卷：</span><el-link type="primary" :href="'{:api_url('/question/questionnaire/answer_records',['questionnaire_id'=>''])}'+questionnaire_answer.questionnaire_id">{{questionnaire_answer.title}}</el-link>
                    </div>
                </div>
                <div style="margin-top: 20px">
                    <el-table
                            :data="lists"
                            border
                            style="width: 100%">
                        <el-table-column
                                prop="number"
                                label="题号"
                                width="80">
                        </el-table-column>
                        <el-table-column
                                prop="content"
                                label="题目"
                                min-width="200"
                        >
                        </el-table-column>
                        <el-table-column
                                align="center"
                                prop="item_type_text"
                                label="题目类型"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                prop="option_values"
                                label="回答"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                label="操作"
                                min-width="200">
                            <template slot-scope="props">
                                <el-popover
                                        placement="bottom-start"
                                        title="统计"
                                        width="200"
                                        trigger="hover">
                                    <div>
                                        <div v-for="(item,index) in props.row.option_values_analysis.list"
                                             style="display: flex;justify-content: space-between;margin-bottom: 10px;">
                                            <div>{{item.option_value}}</div>
                                            <div>
                                                {{item.count}}({{(item.count/props.row.option_values_analysis.total)*100|fix}}%)
                                            </div>
                                        </div>
                                    </div>
                                    <el-link slot="reference" type="primary">查看统计</el-link>
                                </el-popover>
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
                    questionnaire_answer_id: "<?php echo $_GET['questionnaire_answer_id'] ?? 0?>",
                    lists: [],
                    show: false,
                    edit_item: {},
                    questionnaire_answer: {}
                },
                filters: {
                    fix: function (value) {
                        return parseFloat(value).toFixed(2);
                    }
                },
                methods: {
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/questionnaire/answer_records_detail')}",
                            data: {
                                questionnaire_answer_id: this.questionnaire_answer_id,
                            },
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                _this.questionnaire_answer = res.data.questionnaire_answer
                                _this.lists = res.data.questionnaire_items
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