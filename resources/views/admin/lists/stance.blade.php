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
                    <div class="col-md-12">
                        <a href="{{ route('admin.add.stance') }}" type="button"
                           class="btn btn-primary pull-right identity">Add Event</a>
                    </div>
                    <div class="box-body" style="overflow: scroll;">
                        <table id="full_feature_datatable" class="table table-bordered table-striped table-responsive">
                            <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @php
                                $sr_num = 1
                            @endphp

                            @foreach($tags as $u)
                                <tr>
                                    <td>
                                        {{ $sr_num }}
                                    </td>
                                    <td>{{ $u->title }}</td>
                                    <td>

                                        <a href="#" data-toggle="modal"
                                           data-target="#confirm-delete{{ $u->id }}" class="text-danger delete">
                                            <i class="fa fa-trash-o"></i>
                                        </a>

                                        <a href="{{ route('admin.edit.stance', [$u->id]) }}" data-toggle="modal"
                                           data-target="#" class="text-primary">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                        <div class="modal fade" id="confirm-delete<?=$u->id?>" tabindex="-1"
                                             role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3>Confirm</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h5>Are you sure you want to delete this Stance?</h5>
                                                    </div>
                                                    <div class="modal-footer">

                                                        <form action="{{ route('admin.stance.delete', [$u->id]) }}"
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
