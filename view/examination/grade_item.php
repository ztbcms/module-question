<div id="app">
    <el-card>
        <el-form ref="form" :model="form" label-width="80px">
            <el-form-item label="题目">
                {$answer_result->content}
            </el-form-item>
            <el-form-item label="参考答案">
                {$answer_result->true_options}
            </el-form-item>
            <el-form-item label="用户回答">
                {$answer_result->option_values}
            </el-form-item>
            <el-form-item label="是否正确">
                <el-radio-group v-model="form.is_answer_correct">
                    <el-radio :label="1">是</el-radio>
                    <el-radio :label="0">否</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="获得分数">
                <el-input type="number" v-model="form.score"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</div>

<script>
    new Vue({
        el: "#app",
        data: {
            form: {
                examination_answer_id: "{$answer_result->examination_answer_id}",
                item_id: "{$answer_result->item_id}",
                is_answer_correct: 1,
                score: ''
            }
        },
        methods: {
            onSubmit: function () {
                console.log('form', this.form)
                this.httpPost("{:api_url('/question/examination/gradeItem')}", this.form, function (res) {
                    if (res.status) {
                        layer.msg('操作成功')
                        setTimeout(function () {
                            if (window.parent) {
                                window.parent.layer.closeAll()
                            }
                        }, 1000)
                    } else {
                        layer.msg(res.msg)
                    }
                })
            }
        }
    })
</script>