<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <div slot="header">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item><a
                                    :href="'{:api_url('/question/examination/answer_records',['examination_id'=>''])}'+examination_answer.examination_id">答题列表</a>
                        </el-breadcrumb-item>
                        <el-breadcrumb-item>提交记录</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div>
                    <div>
                        <span style="font-weight: bold">用户：</span>{{examination_answer.target}}
                    </div>
                    <div style="display: flex;align-items: center">
                        <span style="font-weight: bold">试卷：</span>
                        <el-link type="primary"
                                 :href="'{:api_url('/question/examination/answer_records',['examination_id'=>''])}'+examination_answer.examination_id">
                            {{examination_answer.title}}
                        </el-link>
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
                                prop="result_status"
                                label="回答状态"
                                min-width="100">
                            <template slot-scope="props">
                                <el-tag v-if="props.row.result_status==1" type="primary">
                                    已作答
                                </el-tag>
                                <el-tag v-if="props.row.result_status==0" type="danger">
                                    未作答
                                </el-tag>
                                <el-tag v-if="props.row.result_status==2" type="info">
                                    待判卷
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column
                                prop="option_values"
                                label="回答"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                prop="right_key"
                                label="正确答案"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                prop="is_answer_correct"
                                label="回答结果"
                                min-width="100">
                            <template slot-scope="props">
                                <template v-if="props.row.result_status==2">
                                    <el-button @click="gradeItem(props.row.item_id)" size="small" type="primary">判卷</el-button>
                                </template>
                                <template v-else>
                                    <el-tag v-if="props.row.is_answer_correct==1" type="success">
                                        正确
                                    </el-tag>
                                    <el-tag v-else type="danger">
                                        错误
                                    </el-tag>
                                </template>
                            </template>
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
                    examination_answer_id: "<?php echo $_GET['examination_answer_id'] ?? 0?>",
                    lists: [],
                    show: false,
                    edit_item: {},
                    examination_answer: {},
                },
                filters: {
                    fix: function (value) {
                        return parseFloat(value).toFixed(2);
                    }
                },
                methods: {
                    gradeItem: function (item_id) {
                        var that = this;
                        var url = "{:api_url('/question/examination/gradeItem',['examination_answer_id'=>''])}" + this.examination_answer_id + '&item_id=' + item_id;
                        layer.open({
                            type: 2,
                            title: '判卷',
                            shadeClose: true,
                            area: ['800px', '600px'],
                            content: url,
                            end: function () {
                                that.getList()
                            }
                        });
                    },
                    getList: function () {
                        var _this = this;
                        $.ajax({
                            url: "{:api_url('question/examination/answer_records_detail')}",
                            data: {
                                examination_answer_id: this.examination_answer_id,
                            },
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                _this.examination_answer = res.data.examination_answer
                                _this.lists = res.data.examination_items
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