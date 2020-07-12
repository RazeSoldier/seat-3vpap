@extends('web::layouts.grids.12')

@section('title', __('pap::stat.fleet-stat'))
@section('page_header', __('pap::stat.fleet-stat'))

@push('head')
    <!-- Vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <!-- element-ui -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
@endpush

@section('full')
    <div class="box-header">
        <h3 class="box-title">{{__('pap::stat.stat')}}</h3>
    </div>
    <div class="box-body" id="vue">
        <post-from></post-from>
    </div>
@endsection

@php
$fleetMemberLabel = __('pap::stat.textinput-notice');
$submitLabel = __('pap::stat.submit');
switch ($fcRight) {
    case 'A':
        $typeOptions = <<<HTML
<el-option label="VVV&MSN联合作战" value="A"></el-option>
<el-option label="VVV/MSN联盟活动" value="B"></el-option>
<el-option label="军团活动" value="C"></el-option>
HTML;
        break;
    case 'B':
        $typeOptions = <<<HTML
<el-option label="VVV/MSN联盟活动" value="B"></el-option>
<el-option label="军团活动" value="C"></el-option>
HTML;
        break;
    case 'C':
        $typeOptions = <<<HTML
<el-option label="军团活动" value="C"></el-option>
HTML;
}
$template = <<<TEXT
<el-form ref="form" :model="form" :rules="rules" label-width="80px">
  <el-form-item label="舰队成员" prop="text">
    <el-input type="textarea" v-model="form.text" placeholder="$fleetMemberLabel" rows="20"></el-input>
  </el-form-item>
  <el-form-item label="集结备注" prop="notice">
    <el-input v-model="form.notice"></el-input>
  </el-form-item>
  <el-form-item label="集结类型" prop="type">
    <el-select v-model="form.type">
      $typeOptions
    </el-select>
  </el-form-item>
    <el-form-item label="集结分" prop="point">
    <el-select v-model="form.point">
      <el-option label="1" value="1"></el-option>
      <el-option label="2" value="2"></el-option>
      <el-option label="3" value="3"></el-option>
      <el-option label="4" value="4"></el-option>
    </el-select>
  </el-form-item>
  <el-form-item>
    <el-button type="primary" @click="submitForm('form')">$submitLabel</el-button>
  </el-form-item>
</el-form>
TEXT;
@endphp

@push('javascript')
    <script>
        Vue.component('post-from', {
            template: `{!! $template !!}`,
            data() {
                return {
                    form: {
                        text: '',
                        notice: '',
                        type: '',
                        point: null,
                    },
                    rules: {
                        text: [{required: true, message: '{{$fleetMemberLabel}}', trigger: 'blur'}],
                        notice: [{required: true, message: '请填写备注', trigger: 'blur'}],
                        type: [{required: true, message: '请选择一个集结类型', trigger: 'change'}],
                        point: [{required: true, message: '请选择一个集结分', trigger: 'change'}],
                    }
                }
            },
            methods: {
                submitForm(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid){
                            fetch('{{route('pap.post-stat')}}', {
                                method: 'POST',
                                body: JSON.stringify(this.form),
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                }
                            }).then(res => res.json()).catch(error => {
                                this.$notify.error({
                                    title: '错误',
                                    message: error,
                                });
                            })
                            .then(json => {
                                if (json.status === 'ok') {
                                    this.$message({
                                        message: '成功上传',
                                        type: 'success',
                                    });
                                    this.$refs[formName].resetFields();
                                } else {
                                    this.$message.error('Failed to submit: ' + json.message);
                                }
                            });
                        } else {
                            return false;
                        }
                    });
                }
            }
        });
        new Vue({el: '#vue'});
    </script>
@endpush