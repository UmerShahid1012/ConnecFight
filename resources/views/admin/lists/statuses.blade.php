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
                        <a href="{{ route('admin.add.status') }}" type="button"
                           class="btn btn-primary pull-right identity">Add Status</a>
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
                                    <td>{{ $u->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.edit.status', [$u->id]) }}" data-toggle="modal"
                                           data-target="#" class="text-primary">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
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
