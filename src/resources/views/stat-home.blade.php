@extends('web::layouts.grids.4-8')

@section('title', __('pap::stat.fleet-stat'))
@section('page_header', __('pap::stat.fleet-stat'))

@section('left')
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">{{__('pap::stat.stat')}}</h3>
        </div>
        <form role="form" action="{{route('pap.post-stat')}}" method="post">
            {{csrf_field()}}
            <div class="box-body">
                <p>{{__('pap::stat.textinput-notice')}}</p>
                <div class="form-group">
                    <label>
                        <textarea class="form-control" name="members" rows="20" required></textarea>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        集结备注
                        <input type="text" name="fleetNote" style="width: 400px" required>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        集结类型
                        <select name="fleetType" required>
                            <option value="" style="display: none"></option>
                            @switch($fcRight)
                                @case('A')
                                    <option value="A">VVV&MSN联合作战</option>
                                    <option value="B">VVV/MSN联盟活动</option>
                                    <option value="C">军团活动</option>
                                    @break
                                @case('B')
                                    <option value="B">VVV/MSN联盟活动</option>
                                    <option value="C">军团活动</option>
                                    @break
                                @case('C')
                                    <option value="C">军团活动</option>
                            @endswitch
                        </select>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        集结分
                        <select name="PAP" required>
                            <option value="" style="display: none"></option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </label>
                </div>
                <div class="box-footer">
                    <div class="btn-group pull-right" role="group">
                        <input type="submit" class="btn btn-primary" value="{{__('pap::stat.submit')}}"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection