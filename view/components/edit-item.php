<div id="question-edit-item" type="text/x-template" v-cloak>
    <div>
        <el-dialog
                :title="title"
                :visible.sync="show"
                width="680px"
                :before-close="handleClose">
            <div>
                <el-form ref="form" :model="form" label-width="80px">
                    <el-form-item label="题目">
                        <el-input placeholder="请输入题目内容" type="textarea" v-model="form.content"></el-input>
                    </el-form-item>
                    <el-form-item label="种类">
                        <el-radio-group v-model="form.item_kind">
                            <el-radio label="0">问卷</el-radio>
                            <el-radio label="1">答题</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="类型">
                        <el-radio-group v-model="form.item_type">
                            <el-radio label="0">单选题</el-radio>
                            <el-radio label="1">多选题</el-radio>
                            <el-radio label="2">填空题</el-radio>
                        </el-radio-group>
                    </el-form-item>


                    <!-- 填空题 -->
                    <el-form-item v-if="form.item_type==2" label="填空项">
                        <div style="display: flex;align-items: center;">
                            <div style="display: flex;align-items: center;">
                                <el-select v-model="form.option_fill_type" placeholder="请选择填空类型">
                                    <el-option v-for="(item,index) in fill_options" :key="index"
                                               :label="item.option_value"
                                               :value="item.option_fill_type">
                                    </el-option>
                                </el-select>
                            </div>
                            <div style="padding-left:10px;display: flex">
                                <i @click="addFillOptionEvent"
                                   style="cursor: pointer;color: #409EFF;font-size: 24px;line-height: 24px"
                                   class="el-icon-circle-plus"></i>
                            </div>
                        </div>
                        <div>
                            <div v-for="(item,index) in options" style="display: flex;align-items: center">
                                <div>
                                    <b>填空项{{index+1}}</b> <span style="padding-left: 10px;">{{item.option_value}}</span>
                                </div>
                                <div style="padding-left:10px;display: flex">
                                    <i
                                            @click="deleteOptionEvent(index)"
                                            style="cursor: pointer;color: #f56c6c;font-size: 24px;line-height: 24px"
                                            class="el-icon-remove"></i>
                                </div>
                            </div>
                        </div>
                    </el-form-item>
                    <!-- 单项、多项选择题-->

                    <el-form-item v-if="form.item_type==0 || form.item_type==1" label="选项">
                        <div style="display: flex;align-items: center;">
                            <div>
                                <el-input placeholder="请输入选项值" type="text" style="width: 150px;"
                                          v-model="option_value"></el-input>
                            </div>
                            <div style="padding-left:10px;display: flex">
                                <template v-if="option_img!=''">
                                    <img style="width: 30px;height: 30px;line-height: 30px" :src="option_img" alt="">
                                </template>
                                <template v-else>
                                    <i @click="gotoUploadImage"
                                       style="cursor: pointer;font-size: 24px;line-height: 24px"
                                       class="el-icon-picture"></i>
                                </template>
                            </div>
                            <div style="padding-left:10px;display: flex">
                                <i @click="addOptionEvent"
                                   style="cursor: pointer;color: #409EFF;font-size: 24px;line-height: 24px"
                                   class="el-icon-circle-plus"></i>
                            </div>
                        </div>
                        <div>
                            <div v-for="(item,index) in options" style="display: flex;align-items: center">
                                <div>
                                    <b>选项{{index+1}}</b> <span style="padding-left: 10px;">{{item.option_value}}</span>
                                </div>
                                <div v-if="item.option_img">
                                    <img style="padding-left: 10px;display: flex;width: 30px;height: 30px;line-height: 30px"
                                         :src="item.option_img"
                                         alt="">
                                </div>
                                <div style="padding-left:10px;display: flex">
                                    <i
                                            @click="deleteOptionEvent(index)"
                                            style="cursor: pointer;color: #f56c6c;font-size: 24px;line-height: 24px"
                                            class="el-icon-remove"></i>
                                </div>
                            </div>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button @click="$emit('close')">取 消</el-button>
                <el-button type="primary" @click="onSubmit">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</div>

<script>
    $(function () {
        Vue.component('question-edit-item', {
            template: '#question-edit-item',
            mixins: [],
            props: {
                item: {
                    type: Object,
                    default: function () {
                        return {}
                    }
                },
                show: {
                    type: Boolean,
                    default: false
                },
                title: {
                    type: String,
                    default: "增加题目"
                }
            },
            data() {
                return {
                    fill_options: [{
                        option_value: "文本",
                        option_fill_type: '0',
                    }, {
                        option_value: "数值",
                        option_fill_type: '1',
                    }],
                    option_value: "",
                    option_img: "",
                    options: [],
                    form: {
                        item_kind: "0",
                        item_type: "0",
                    },
                }
            },
            watch: {
                show: function (value) {
                    if (value) {
                        if (this.item.item_id) {
                            this.form = {
                                item_id: this.item.item_id,
                                content: this.item.content,
                                item_kind: this.item.item_kind + '',
                                item_type: this.item.item_type + '',
                            }
                            this.options = this.item.item_options
                        } else {
                            this.form = {
                                item_kind: "0",
                                item_type: "0",
                            }
                            this.options = []
                        }
                    }
                }
            },
            mounted: function () {
                window.addEventListener('ZTBCMS_UPLOAD_IMAGE', this.onUploadedImage.bind(this));
            },
            methods: {
                onSubmit: function () {
                    var form_data = this.form
                    if (form_data.content === '') {
                        this.$message.error('请输入题目内容')
                        return
                    }
                    if (this.options.length === 0) {
                        this.$message.error('请输入题目选项')
                        return
                    }
                    form_data['options'] = this.options
                    var _this = this
                    this.httpPost('{:url("question/item/edit")}', form_data, function (res) {
                        console.log(res)
                        if (res.status) {
                            _this.$message.success('操作成功')
                            _this.$emit('success')
                            _this.$emit('close')
                        } else {
                            _this.$message.error(res.msg)
                        }
                    })
                    console.log('form_data', form_data)
                },
                deleteOptionEvent: function (index) {
                    console.log('index', index)
                    this.options.splice(index, 1)
                },
                addFillOptionEvent() {
                    var option_fill_type = this.form.option_fill_type
                    var option = this.fill_options.find(item => {
                        return item.option_fill_type === option_fill_type
                    })
                    if (option) {
                        this.options.push(option)
                    }
                },
                addOptionEvent: function () {
                    if (!this.option_value) {
                        this.$message.error('请输入选项值')
                        return
                    }
                    this.options.push({
                        option_value: this.option_value,
                        option_img: this.option_img
                    })
                    this.option_value = ''
                    this.option_img = ''
                },
                onUploadedImage: function (event) {
                    var files = event.detail.files;
                    if (files[0]) {
                        this.option_img = files[0].fileurl
                    }
                },
                gotoUploadImage: function (isPrivate) {
                    layer.open({
                        type: 2,
                        title: '',
                        closeBtn: false,
                        content: "{:api_url('common/upload.panel/imageUpload')}?is_private=" + isPrivate,
                        area: ['720px', '550px'],
                    })
                },
                handleClose: function () {

                }
            }
        });
    })
</script>