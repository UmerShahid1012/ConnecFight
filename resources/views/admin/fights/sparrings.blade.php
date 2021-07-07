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
                        <table id="full_feature_datatable" class="table table-bordered table-striped table-responsive">
                            <thead>
                            <tr>
                                <th>Sr# </th>
                                <th>Posted By</th>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Salary/week</th>
                                <th>Number of weeks</th>
                                <th>Session/week</th>
                                <th>Event</th>
                                <th>Gender</th>
                                <th>Description</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @php
                                $sr_num = 1
                            @endphp

                            @foreach($sparrings as $u)
                                <tr>
                                    <td>
                                        {{ $sr_num }}
                                    </td>
                                    <td style="vertical-align: middle;">{{$u->user->first_name.' '.$u->user->last_name}}</td>
                                    <td>{{ $u->title }}</td>
                                    <td>{{ $u->location }}</td>
                                    <td>{{ isset($u->start_date)?$u->start_date:"N/A"}}</td>
                                    <td>{{ isset($u->end_date)?$u->end_date:"N/A"}}</td>
                                    <td>${{ isset($u->budget_per_week)?$u->budget_per_week:"N/A"}}</td>
                                    <td>{{ isset($u->no_of_weeks)?$u->no_of_weeks:"N/A"}}</td>
                                    <td>{{ isset($u->session_per_week)?$u->session_per_week:"N/A"}}</td>
                                    <td>{{ isset($u->event->title)?$u->event->title:"N/A" }}</td>
                                    <td>{{ isset($u->gender)?$u->gender:"N/A"}}</td>
                                    <td>{{ isset($u->description)?$u->description:"N/A" }}</td>
{{--                                    @dd($u->assign)--}}

                                    <td>{{ isset($u->assign->first_name)?$u->assign->first_name:"N/A" }}</td>
                                    <td>{{ isset($u->status_name['name'])?$u->status_name['name']:"N/A" }}</td>
                                   <td>
                                        <a href="#" data-toggle="modal"
                                           data-target="#confirm-delete{{ $u->id }}" class="text-danger delete">
                                            <i class="fa fa-trash-o"></i>
                                        </a>

{{--                                        <a href="{{ route('admin.category.edit', [$u->id]) }}" data-toggle="modal" data-target="#" class="text-primary">--}}
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

                                                        <form action="{{ route('admin.sparring.delete', [$u->id]) }}"
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
