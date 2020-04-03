@extends('web::layouts.grids.12')

@section('title', $title)
@section('page_header', $title)

@section('full')
    <div class="box-body">
        <table id="srps" class="table table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{{__('pap::pap.pap-characterName')}}</th>
                    <th>{{__('pap::pap.pap-fleetNote')}}</th>
                    <th>{{__('pap::pap.pap-fleetTime')}}</th>
                    <th>{{__('pap::pap.pap')}}</th>
                    <th>{{__('pap::pap.pap-fleetFC')}}</th>
                    <th>{{__('pap::pap.pap-fleetType')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($fleets as $fleet)
                <tr>
                    <td><span rel='id-to-name'>{{$fleet->characterName}}</span></td>
                    <td><span rel='id-to-name'>{{$fleet->fleetNote}}</span></td>
                    <td>{{$fleet->fleetTime}}</td>
                    <td>{{$fleet->PAP}}</td>
                    <td>{{$fleet->fleetFC}}</td>
                    @switch($fleet->fleetType)
                        @case('A')
                            <td>VVV&MSN联合作战</td>
                            @break
                        @case('B')
                            <td>VVV/MSN联盟活动</td>
                            @break
                        @case('C')
                            <td>军团活动</td>
                            @break
                    @endswitch
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection