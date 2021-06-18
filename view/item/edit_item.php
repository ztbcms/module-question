<div id="app" style="padding: 8px;" v-cloak>
    <el-card>
        <el-row>
            <el-col :sm="24" :md="17">
                <div class="grid-content ">
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
                            <el-radio-group v-model="form.item_type" @change="clearOptions">
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
                                <div v-for="(item,index) in pack" style="display: flex;align-items: center">
                                    <div>
                                        <b>填空项{{index+1}}</b> <span style="padding-left: 10px;">{{item.option_value}}</span>
                                        <el-input placeholder="请输入参考答案"
                                                  v-if="form.item_kind==1 && item.option_type==2"
                                                  type="text"
                                                  style="width: 150px; margin-top:5px;"
                                                  v-model="item.reference_answer" ></el-input>
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
                                <!-- 单选 -->
                                <template v-if="form.item_type==0">
                                        <div v-for="(item,index) in radio_data" style="display: flex;align-items: center">
                                            <!--  是否正确答案选项    -->
                                            <el-checkbox-group v-model="item.option_true" v-if="form.item_kind==1 && item.option_type==0" @change="changeSingleOptionEvent(index)">
                                                <el-checkbox label="1" :true-label="1" :false-label="0">&nbsp;</el-checkbox>
                                            </el-checkbox-group>

                                            <div>
                                                <b>选项{{index+1}}</b>
                                                <span style="padding-left: 10px;">{{item.option_value}}</span>
                                            </div>
                                            <div v-if="item.option_img">
                                                <img style="padding-left: 10px;display: flex;width: 30px;height: 30px;line-height: 30px"
                                                     :src="item.option_img"
                                                     alt="">
                                            </div>
                                            <div style="padding-left:10px;display: flex">
                                                <i @click="deleteOptionEvent(index)"
                                                        style="cursor: pointer;color: #f56c6c;font-size: 24px;line-height: 24px"
                                                        class="el-icon-remove"></i>
                                            </div>
                                        </div>
                                </template>

                                <!-- 多选 -->
                                <template v-if="form.item_type==1">
                                    <div v-for="(item,index) in checkbox_data" style="display: flex;align-items: center">
                                        <el-checkbox-group v-model="item.option_true"  v-if="form.item_kind==1 && item.option_type==1">
                                            <el-checkbox label="1" :true-label="1" :false-label="0" >&nbsp;</el-checkbox>
                                        </el-checkbox-group>
                                        <div>
                                            <b>选项{{index+1}}</b>
                                            <span style="padding-left: 10px;">{{item.option_value}}</span>
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
                                </template>

                            </div>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="primary" @click="onSubmit">提交</el-button>
                        </el-form-item>


                    </el-form>
                </div>
            </el-col>

        </el-row>


    </el-card>
</div>

<style>

</style>

<script>
    $(document).ready(function () {
        new Vue({
            // template: '#question-edit-item',
            mixins: [],
            el: '#app',
            data() {
                return {
                    count: 1,
                    item_id: "{$item_id|default=''}",
                    // 填空题选项
                    fill_options: [{
                        option_value: "文本",
                        option_fill_type: '0',
                        option_type:'2',
                        option_true:'1',
                        reference_answer: ''
                    }, {
                        option_value: "数值",
                        option_fill_type: '1',
                        option_type:'2',
                        option_true:'1',
                        reference_answer: ''
                    }],
                    option_value: "",
                    option_type:'1',
                    option_true:'',
                    option_img: "",
                    reference_answer: '',
                    option_data:'',
                    radio_data:[],// 单选 选项数组
                    checkbox_data:[],
                    options: [],
                    pack: [], // 填空题 选项
                    form: {
                        item_id: '',
                        item_kind: '0',
                        item_type: '0',
                        content: ''
                    },
                }
            },
            mounted: function () {
                this.form.item_id = this.getUrlQuery('item_id');
                if(this.form.item_id) {
                    this.getDetail();
                }
                window.addEventListener('ZTBCMS_UPLOAD_IMAGE', this.onUploadedImage.bind(this));
            },
            methods: {
                onSubmit: function () {
                    var form_data = this.form
                    if (form_data.content === '') {
                        this.$message.error('请输入题目内容')
                        return
                    }
                    console.log(form_data)
                    if(form_data.item_type == 0){
                        if (this.radio_data.length === 0) {
                            this.$message.error('请输入题目选项')
                            return
                        }

                        //判断选项是否选择正确答案
                        if(this.radio_data && this.form.item_kind == 1){
                            var ifSetOptionTrue='';
                            for(var i=0;i<this.radio_data.length;i++){
                                ifSetOptionTrue += this.radio_data[i].option_true + ',';
                            }
                            if(ifSetOptionTrue.indexOf('1') == '-1' && ifSetOptionTrue.indexOf('true') == '-1'){
                                this.$message.error('请输入题目正确选项')
                                return
                            }
                        }
                    }else if(form_data.item_type == 1){
                        if (this.checkbox_data.length === 0) {
                            this.$message.error('请输入题目选项')
                            return
                        }

                        //判断选项是否选择正确答案
                        if(this.checkbox_data && this.form.item_kind == 1){
                            var ifSetOptionTrue='';
                            for(var i=0;i<this.checkbox_data.length;i++){
                                ifSetOptionTrue += this.checkbox_data[i].option_true + ',';
                            }
                            if(ifSetOptionTrue.indexOf('1') == '-1' && ifSetOptionTrue.indexOf('true') == '-1'){
                                this.$message.error('请输入题目正确选项')
                                return
                            }
                        }
                    }

                    // TODO 按需拿参数
                    form_data['options'] = [this.pack,this.radio_data,this.checkbox_data]

                    var _this = this
                    this.httpPost('{:url("question/item/edit")}', form_data, function (res) {
                        console.log(res)
                        if (res.status) {
                            _this.$message.success('操作成功')
                            _this.$emit('success')
                            _this.$emit('close')
                            setTimeout(function () {
                                window.parent.layer.closeAll();
                            }, 1000);
                        } else {
                            _this.$message.error(res.msg)
                        }
                    })
                },
                deleteOptionEvent: function (index) {
                    console.log('index', index)
                    var that = this
                    if(that.form.item_type === '0'){
                        this.radio_data.splice(index, 1)
                    }else if(that.form.item_type === '1'){
                        this.checkbox_data.splice(index, 1)
                    }else{
                        this.pack.splice(index, 1)
                    }
                },
                // 添加填空项
                addFillOptionEvent() {
                    var option_fill_type = this.form.option_fill_type
                    var option = null
                    for (var i = 0; i < this.fill_options.length; i++) {
                        if (this.fill_options[i].option_fill_type === option_fill_type) {
                            option = this.fill_options[i]
                        }
                    }
                    if (option) {
                        this.pack.push({
                            option_value: option.option_value,
                            option_fill_type: option.option_fill_type,
                            option_type: option.option_type,
                            option_true: option.option_true,
                            reference_answer: option.reference_answer
                        })
                    }
                },
                // 添加选择项
                addOptionEvent: function () {
                    var that = this
                    if (!this.option_value) {
                        this.$message.error('请输入选项值')
                        return
                    }
                    // console.log(that.form.item_type)
                    // return
                    if(that.form.item_type === '0'){
                        // 单选
                        this.radio_data.push({
                            option_value: this.option_value,
                            option_img: this.option_img,
                            option_type: this.form.item_type,
                            option_true: this.option_true
                        })
                    }
                    if(that.form.item_type === '1'){
                        // 多选
                        this.checkbox_data.push({
                            option_value: this.option_value,
                            option_img: this.option_img,
                            option_type: this.form.item_type,
                            option_true: this.option_true
                        })
                    }
                    this.option_value = ''
                    this.option_img = ''
                    this.option_type = ''
                    this.option_true = ''
                },
                //清空选项
                clearOptions: function (value){
                    if(value == 0){
                        this.checkbox_data = ''
                        this.pack = ''
                    }else if(value == 1){
                        this.radio_data = ''
                        this.pack = ''
                    }else{
                        this.checkbox_data = ''
                        this.radio_data = ''
                    }
                },
                // 单选项 勾选状态变动
                changeSingleOptionEvent: function (index){
                    for (var i = 0; i < this.radio_data.length; i++) {
                        this.radio_data[i].option_true = false
                    }
                    this.radio_data[index].option_true = true;

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
                getDetail: function (){
                    var that = this
                    var url = "{:api_url('/question/item/addQuestion')}"
                    if (!that.form.item_id) {
                        return
                    }
                    var data = {
                        item_id: that.form.item_id,
                        '_action': 'getDetail'
                    }
                    this.httpGet(url, data, function (res) {
                        console.log(res)
                        // vm.options[optionIndex].checked = true;
                        that.form = res.data.form;
                        // that.form.item_kind = res.data.form.item_kind + ''
                        that.pack = res.data.pack;
                        that.radio_data = res.data.radio_data;
                        that.checkbox_data = res.data.checkbox_data;
                        console.log(that.form)
                    })
                }

            }
        })
    })
</script>