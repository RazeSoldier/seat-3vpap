@extends('web::layouts.grids.12')

@section('title', $title)
@section('page_header', $title)

@push('head')
    <!-- Vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <!-- element-ui -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
@endpush

@section('full')
    <div class="box-body" id="vue">
        <show-my-pap></show-my-pap>
    </div>
@endsection

@push('javascript')
    <script>
        Vue.component('show-my-pap', {
            template: '<div><el-table :data="tableData.slice((currentPage-1)*pageSize,currentPage*pageSize)" stripe>' +
                '<el-table-column\n' +
                '        prop="characterName"\n' +
                '        label="角色名">' +
                '</el-table-column>' +
                '<el-table-column\n' +
                '        prop="fleetNote"\n' +
                '        label="舰队备注">' +
                '</el-table-column>' +
                '<el-table-column\n' +
                '        prop="fleetTime"\n' +
                '        label="统计时间">' +
                '</el-table-column>' +
                '<el-table-column\n' +
                '        prop="PAP"\n' +
                '        label="出勤分">' +
                '</el-table-column>' +
                '<el-table-column\n' +
                '        prop="fleetFC"\n' +
                '        label="带队指挥">' +
                '</el-table-column>' +
                '<el-table-column\n' +
                '        prop="fleetType"\n' +
                '        label="舰队类型">' +
                '</el-table-column>' +
                '</el-table>' +
                '<el-pagination\n' +
                '    layout="total, sizes, prev, pager, next, jumper"\n' +
                '    current-page="currentPage"\n' +
                '    @size-change="handleSizeChange"\n' +
                '    @current-change="handleCurrentChange"\n' +
                '    :page-size="pageSize"\n' +
                '    :total="totalCount">\n' +
                '</el-pagination></div>',
            data() {
                return {
                    tableData: [],
                    currentPage: 1,
                    totalCount: 1,
                    pageSize: 10
                }
            },
            mounted() {
                fetch('{{route('pap.api-group', ['id' => $groupId])}}').then(res => res.json()).then(json => {
                    this.tableData = json;
                    this.totalCount = json.length;
                })
            },
            methods: {
                handleSizeChange(val) {
                    this.pageSize = val;
                    this.currentPage = 1;
                },
                handleCurrentChange(val) {
                    this.currentPage = val;
                }
            }
        });
        new Vue({el: '#vue'});
    </script>
@endpush