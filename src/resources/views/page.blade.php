@extends('web::layouts.grids.8-4')

@section('title', __('pap::pap.pap'))
@section('page_header', __('pap::pap.pap'))

@if($isAdmin)
@section('left')
    <div class="card card-solid">
        <div class="card-header">
            <h3 class="card-title">{{__('pap::pap.corpList')}}</h3>
        </div>
        <div class="card-body">
            <table class="table table table-bordered table-striped">
                <thead>
                <tr>
                    <th>{{__('pap::pap.corporation')}}</th>
                    <th>{{__('pap::pap.alliance')}}</th>
                    <th>{{__('pap::pap.pap')}}</th>
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
    <div class="card card-primary card-solid">
        <div class="card-header">
            <h3 class="card-title">{{__('pap::pap.myPap')}}</h3>
            <a class="card-tools" href="{{route('pap.pap', ['id' => $uid])}}">{{__('pap::pap.link-mypap')}}</a>
        </div>
        <div class="card-body">
            <table class="table table-condensed">
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">PAP</label></th>
                    <th class="bg-white"><label id="linkedTotalPap">{{$point}}</label></th>
                </tr>
            </table>
        </div>
    </div>

    <div class="card card-solid">
        <div class="card-header">
            <h3 class="card-title">{{__('pap::pap.month-ping')}}</h3>
        </div>
        <div class="card-body">
            <table class="table table-condensed">
                <tr>
                    <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Count</label></th>
                    <th class="bg-white"><label id="pingCount">{{$ctaCount}}</label></th>
                </tr>
            </table>
        </div>
    </div>
@endsection