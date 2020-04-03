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
                    <th>{{__('pap::pap.aPoint')}}</th>
                    <th>{{__('pap::pap.bPoint')}}</th>
                    <th>A + B</th>
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
                        <td>{{$corp->aPoint}}</td>
                        <td>{{$corp->bPoint}}</td>
                        <td>{{$corp->aPoint + $corp->bPoint}}</td>
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
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">A</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$aPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">B</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$bPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">C</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$cPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">A + B</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$aPap + $bPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">A + B + C</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$aPap + $bPap + $cPap}}</label></th>
                </tr>
                <tr>
                    <th class="bg-white"><small>注：A分指VVV&MSN联合作战，B分指VVV/MSN联盟活动，C分指军团活动</small></th>
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
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">A</label></th>
                    <th class="bg-white"><label id="pingCount">{{$aPing}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">B</label></th>
                    <th class="bg-white"><label id="pingCount">{{$bPing}}</label></th>
                </tr>
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">A + B</label></th>
                    <th class="bg-white"><label id="pingCount">{{$aPing + $bPing}}</label></th>
                </tr>
            </table>
        </div>
    </div>
@endsection