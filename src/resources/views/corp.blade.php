@extends('web::layouts.grids.8-4')

@section('title', $title)
@section('page_header', $title)

@section('left')
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">{{__('pap::pap.member-pap')}}</h3>
        </div>
        <div class="box-body">
            <table class="table table table-bordered table-striped">
                <thead>
                <tr>
                    <th>{{__('pap::pap.pap-characterName')}}</th>
                    <th>{{__('pap::pap.linked-count')}}</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>A + B</th>
                    <th>A + B + C</th>
                </tr>
                </thead>
                <tbody>
                @foreach($memberList as $member)
                    <tr>
                        <td><a href="{{route('pap.pap', ['id' => $member['groupId']])}}">{{$member['name']}}</a></td>
                        <td>{{$member['linkedCount']}}</td>
                        <td>{{$member['aPoint']}}</td>
                        <td>{{$member['bPoint']}}</td>
                        <td>{{$member['cPoint']}}</td>
                        <td>{{$member['aPoint'] + $member['bPoint']}}</td>
                        <td>{{$member['aPoint'] + $member['bPoint'] + $member['cPoint']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection