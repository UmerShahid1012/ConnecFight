<style>
    .usersOptions select {
        width: 100%;
        padding: 8px 5px;
    }

    .usersOptions {
        width: 215px;
        margin-right: 15px;
    }
    .userFilters {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
</style>

@extends('admin.dashboard.layout')

@section('content')



    <section class="content-header">
        <h1>
            Connect Fighters
            <small>{{ $title }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><a href="<?= asset('hash_tags') ?>">{{ $title }}</a></li>
        </ol>
        <div class="box-header">
            <div class="alert alert-success  in alert-dismissible ajax-label" style="display: none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <span class="ajax-label-body"></span>
            </div>
            <div id="successMessage">
                @include('includes.messages')
            </div>

        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= $title;?></h3>
                    </div>
{{--                    <div class="col-md-12">--}}
{{--                        <a href="{{ route('admin.category.add') }}" type="button"--}}
{{--                           class="btn btn-primary pull-right identity">Add User</a>--}}
{{--                    </div>--}}
                    <div class="box-body" style="overflow: scroll;">
{{--                        <div class="userFilters">--}}
{{--                            <div class="usersOptions">--}}
{{--                                <select>--}}
{{--                                    <option>one</option>--}}
{{--                                    <option>two</option>--}}
{{--                                    <option>three</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="usersOptions">--}}
{{--                                <select>--}}
{{--                                    <option>one</option>--}}
{{--                                    <option>two</option>--}}
{{--                                    <option>three</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="usersOptions">--}}
{{--                                <select>--}}
{{--                                    <option>one</option>--}}
{{--                                    <option>two</option>--}}
{{--                                    <option>three</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <table id="full_feature_datatable" class="table table-bordered table-striped table-responsive">
                            <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Challenger</th>
                                <th>Defender</th>
                                <th>Posted By</th>
                                <th>Event</th>
                                <th>Event Host</th>
                                <th>Location</th>
                                <th>Match Date</th>
                                <th>Salary</th>
                                <th>No of Rounds</th>
                                <th>description</th>
                                <th>Winner</th>
                                <th>Status</th>
                                <th>K.O</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @php
                                $sr_num = 1
                            @endphp

                            @foreach($fights as $u)
                                <tr>
                                    <td>
                                        {{ $sr_num }}
                                    </td>
                                    <td>{{ $u->challenger_id['first_name'] }} {{ $u->challenger_id['last_name'] }}</td>
                                    <td>{{ $u->defender_id['first_name'] }} {{ $u->defender_id['last_name'] }}</td>
                                    <td>{{ $u->posted_by_id['first_name'] }} {{ $u->posted_by_id['last_name'] }}</td>
                                    <td>{{ $u->event['title']}}</td>
                                    <td>{{ isset($u->event_host)?$u->event_host:"N/A"}}</td>
                                    <td>{{ isset($u->location)?$u->location:"N/A"}}</td>
                                    <td>{{ isset($u->match_date)?$u->match_date:"N/A"}}</td>
                                    <td>${{ isset($u->fund)?$u->fund:"N/A"}}</td>
                                    <td>{{ isset($u->no_of_rounds)?$u->no_of_rounds:"N/A" }}</td>
                                    <td>{{ isset($u->description)?$u->description:"N/A" }}</td>

                                    <td>
                                        @if($u->winner_id)
                                            {{ isset($u->winner_name['first_name'])?$u->winner_name['first_name']:"N/A" }} {{ isset($u->winner_name['last_name'])?$u->winner_name['last_name']:"N/A" }}
                                        @else
                                            @if($u->status == 7 and \Carbon\Carbon::now() >= $u->match_date)
                                                <a href="{{route('decide_winner.user',['id'=>$u->id, 'winner'=>$u->challenger])}}"><i
                                                        class="fa fa-check">Challenger</i></a>
                                                <a href="{{route('decide_winner.user',['id'=>$u->id, 'winner'=>$u->defender])}}"><i
                                                        class="fa fa-check">Defender</i></a>
                                            @else
                                                <a href="#" style="color: darkred" disabled="">Match yet to be start</a>

                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ isset($u->status_id)?$u->status_id['name']:"N/A" }}</td>
                                    <td>
                                        @if($u->is_ko == 1)
                                            <a href="#" style="color: green" disabled="">K.O</a>
                                        @else
                                            <a href="{{route('ko.user',['id'=>$u->id, 'type'=>1])}}"><i
                                                    class="fa fa-check"></i></a>
                                            <a href="{{route('ko.user',['id'=>$u->id,'type'=>0])}}"><i
                                                    class="fa fa-close"></i></a>
                                        @endif
                                    </td>
                                    <td>

                                        <a href="#" data-toggle="modal"
                                           data-target="#confirm-delete{{ $u->id }}" class="text-danger delete">
                                            <i class="fa fa-trash-o"></i>
                                        </a>

{{--                                        <a href="{{ route('admin.category.edit', [$u->id]) }}" data-toggle="modal"--}}
{{--                                           data-target="#" class="text-primary">--}}
{{--                                            <i class="fa fa-pencil-square-o"></i>--}}
{{--                                        </a>--}}
                                        <div class="modal fade" id="confirm-delete<?=$u->id?>" tabindex="-1"
                                             role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3>Confirm</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h5>Are you sure you want to delete this user?</h5>
                                                    </div>
                                                    <div class="modal-footer">

                                                        <form action="{{ route('admin.fight.delete', [$u->id]) }}"
                                                              method="post">
                                                            @csrf
                                                            <button type="button" class="btn btn-default close_modal"
                                                                    data-dismiss="modal">Cancel
                                                            </button>

                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--  Delete Modal End  --}}
                                    </td>
                                </tr>
                                @php
                                    $sr_num++;
                                @endphp
                            @endforeach
                            </tbody>
                        </table>

                        <div class="pull-right">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>

    <!-- /Interest modal End-->



@endsection


@section('js')
@endsection
