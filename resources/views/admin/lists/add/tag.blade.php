@extends('admin.dashboard.layout')

@section('content')

    <section class="content-header">
        <h1>
            CNF
            <small>{{ $title }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
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
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Tag</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" method="post" action="{{ route('admin.tag.save') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$sub->id}}">
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Title</label>
                                        <input type="text" name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{$sub->name}}" id="exampleInputEmail1"  placeholder="Title" required
                                               autocomplete="title" autofocus>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box -->

                </div>
            </div>
        </section>

        </div>

        <!-- /Interest modal End-->

@endsection
