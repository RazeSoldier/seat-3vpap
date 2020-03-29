@extends('web::layouts.grids.8-4')

@section('title', __('pap::pap.pap'))
@section('page_header', __('pap::pap.pap'))

@if($isAdmin)
@section('left')
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">{{__('pap::pap.corpList')}}</h3>
        </div>
        <div class="box-body">
            <table class="table table table-bordered table-striped">
                <thead>
                <tr>
                    <th>{{__('pap::pap.corporation')}}</th>
                    <th>{{__('pap::pap.alliance')}}</th>
                    <th>{{__('pap::pap.monthPap')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($corpList as $corp)
                    <tr>
                        <td><a href="{{route('pap.corp', ['id' => $corp->corporation_id])}}">{{$corp->name}}</a></td>
                        @if ($corp->alliance()->getResults() !== null)
                            <td>{{$corp->alliance()->getResults()->name}}</td>
                        @else
                            <td></td>
                        @endif
                        <td>{{$corp->point}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@endif

@section('right')
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">{{__('pap::pap.myPap')}}</h3>
            <a class="box-tools" href="{{route('pap.pap', ['id' => $gid])}}">{{__('pap::pap.link-mypap')}}</a>
        </div>
        <div class="box-body">
            <table class="table table-condensed">
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">{{__('pap::pap.linked-totalPap')}}</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$linkedTotalPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">{{__('pap::pap.totalPap')}}</label></th>
                    <th class="bg-white"><label id="totalPap">{{$totalPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">{{__('pap::pap.linked-monthPap')}}</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$linkedMonthPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">{{__('pap::pap.monthPap')}}</label></th>
                    <th class="bg-white"><label id="monthPap">{{$monthPap}}</label></th>
                </tr>
            </table>
        </div>
    </div>

    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">{{__('pap::pap.month-ping')}}</h3>
        </div>
        <div class="box-body">
            <table class="table table-condensed">
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">{{__('pap::pap.month-pingcount')}}</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$pingCount}}</label></th>
                </tr>
            </table>
        </div>
    </div>
@endsection