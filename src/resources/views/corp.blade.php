@extends('web::layouts.grids.8-4')

@section('title', $title)
@section('page_header', $title)

@push('head')
    <!-- Vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
    <!-- element-ui -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
@endpush

@section('left')
    <div class="box-body" id="vue">
        <list-member></list-member>
    </div>
@endsection

@php
$characterNameLabel = __('pap::pap.pap-characterName');
$characterLinkCountLabel = __('pap::pap.linked-count');
$exportLink = route('pap.corp-execl', ['id' => $corpId]);
$exportLabel = __('pap::pap.export');
$template = <<<TEXT
<div>
    <el-row>
        <el-link href="$exportLink" target="_blank" :underline="false">
            <el-button type="primary" size="small" round>$exportLabel</el-button>
        </el-link>
    </el-row>
    <el-table
        :data="tableData"
        v-loading="loading"
        stripe
        :default-sort = "{prop: 'point', order: 'descending'}">
        <el-table-column
            prop="characterName"
            label="$characterNameLabel"
            width="210"
            sortable>
            <template slot-scope="scope">
                <el-link :href="scope.row.link" type="primary" :disabled="scope.row.noesi">{{ scope.row.characterName }}</el-link>
            </template>
        </el-table-column>
        <el-table-column
            prop="characterLinkCount"
            label="$characterLinkCountLabel"
            width="200"
            sortable>
        </el-table-column>
        <el-table-column
            prop="point"
            label="PAP"
            width="auto"
            sortable>
        </el-table-column>
    </el-table>
</div>
TEXT;
@endphp

@push('javascript')
    <script>
        Vue.component('list-member', {
            template: `{!! $template !!}`,
            data() {
                return {
                    tableData: [],
                    loading: true,
                }
            },
            mounted() {
                fetch('{{route('pap.get-corppap', $corpId)}}').then(res => res.json()).catch(error => {
                    this.$notify.error({
                        title: '错误',
                        message: error,
                    });
                }).then(json => {
                    this.loading = true;
                    for (const jsonKey in json) {
                        if (!json.hasOwnProperty(jsonKey)) {
                            continue;
                        }
                        const pap = json[jsonKey];
                        if (pap.noesi) {
                            pap['link'] = '';
                        } else {
                            pap['link'] = '/pap/group/' + pap.groupId;
                        }
                    }
                    this.tableData = json;
                    this.loading = false;
                });
            }
        });
        new Vue({el: '#vue'});
    </script>
@endpush

@push('head')
    <style>
        .el-row {
            margin-bottom: 10px;
        }
    </style>
@endpush