<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <div>
                    <h3>问卷分析</h3>
                </div>
                <div>
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item><a href="{:api_url('/question/questionnaire/index')}">问卷列表</a>
                        </el-breadcrumb-item>
                        <el-breadcrumb-item>{{questionnaire.title}}</el-breadcrumb-item>
                    </el-breadcrumb>
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
                                min-width="400"
                        >
                        </el-table-column>
                        <el-table-column
                                align="center"
                                prop="item_type_text"
                                label="题目类型"
                                min-width="200">
                        </el-table-column>
                        <el-table-column
                                label="统计"
                                min-width="400">
                            <template slot-scope="props">
                                <div>
                                    <canvas :id="'mountNode'+props.$index"></canvas>
                                </div>
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
    <script src="https://gw.alipayobjects.com/os/antv/assets/f2/3.4.2/f2.min.js"></script>
    <!-- 在 PC 上模拟 touch 事件 -->
    <script src="https://gw.alipayobjects.com/os/rmsportal/NjNldKHIVQRozfbAOJUW.js"></script>

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
                    questionnaire: {},
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
                            url: "{:api_url('question/questionnaire/analysis')}",
                            data: {
                                questionnaire_id: this.questionnaire_id,
                            },
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                _this.questionnaire = res.data.questionnaire
                                _this.lists = res.data.questionnaire_items
                                setTimeout(function () {
                                    for (var index in _this.lists) {
                                        var item = _this.lists[index]
                                        _this.makeChart(index, item.option_values_analysis.list, item.option_values_analysis.total)
                                    }
                                }, 2000)
                            }
                        })
                    },
                    makeChart: function (index, list, total) {
                        var map = {}
                        var data = [];

                        list.forEach(item => {
                            map[item.option_value] = (parseFloat(item.count / total).toFixed(2) * 100) + '%'
                            data.push({
                                name: item.option_value,
                                percent: parseFloat(item.count / total),
                                a: '1'
                            })
                        })
                        console.log('map', map)
                        console.log('data', data)
                        var chart = new F2.Chart({
                            id: 'mountNode' + index,
                            pixelRatio: window.devicePixelRatio
                        });
                        chart.source(data, {
                            percent: {
                                formatter: function formatter(val) {
                                    return val * 100 + '%';
                                }
                            }
                        });
                        chart.legend({
                            position: 'right',
                            itemFormatter: function itemFormatter(val) {
                                return val + '  ' + map[val];
                            }
                        });
                        chart.tooltip(false);
                        chart.coord('polar', {
                            transposed: true,
                            radius: 0.85
                        });
                        chart.axis(false);
                        chart.interval().position('a*percent').color('name', ['#1890FF', '#13C2C2', '#2FC25B', '#FACC14', '#F04864', '#8543E0']).adjust('stack').style({
                            lineWidth: 1,
                            stroke: '#fff',
                            lineJoin: 'round',
                            lineCap: 'round'
                        }).animate({
                            appear: {
                                duration: 1200,
                                easing: 'bounceOut'
                            }
                        });
                        chart.render();
                    }
                },
                mounted: function () {
                    this.getList()
                },
            })
        })
    </script>
</div>