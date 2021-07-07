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
    @if($type == "edit")
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Plan</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" method="post" action="{{ route('admin.plan.save') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$sub->id}}">
                            <input type="hidden" name="type" value="edit">
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Title</label>
                                        <input type="text" name="title"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{$sub->title}}" id="exampleInputEmail1" required
                                               placeholder="Title"
                                               autocomplete="title" autofocus>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Price/Month</label>
                                        <input type="number" name="price"
                                               class="form-control @error('price') is-invalid @enderror"
                                               value="{{$sub->price}}" id="exampleInputEmail1" required
                                               placeholder="Title" required
                                               autocomplete="price" autofocus>
                                        <span>To select unlimited enter -1</span>

                                        @error('price')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Tax</label>
                                        <input type="number" name="tax"
                                               class="form-control @error('tax') is-invalid @enderror"
                                               value="{{$sub->tax}}" id="exampleInputEmail1" required
                                               placeholder="tax"
                                               autocomplete="tax" min="0" autofocus>
                                        <span>Will be count in percentage</span>

                                        @error('tax')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Sparring</label>
                                        <input type="number" name="sparring"
                                               class="form-control @error('sparring') is-invalid @enderror"
                                               value="{{$sub->no_of_sparrings}}" id="exampleInputEmail1" required
                                               placeholder="sparring" required
                                               autocomplete="sparring" autofocus>
                                        <span>To select unlimited enter -1</span>
                                        @error('sparring')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Applications</label>
                                        <input type="number" name="applying"
                                               class="form-control @error('applying') is-invalid @enderror"
                                               value="{{$sub->no_of_applications}}" id="exampleInputEmail1" required
                                               placeholder="Applications" required
                                               autocomplete="applying" autofocus>
                                        <span>To select unlimited enter -1</span>
                                        @error('applying')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Challenges</label>
                                        <input type="number" name="applying"
                                               class="form-control @error('challenges') is-invalid @enderror"
                                               value="{{$sub->no_of_challenges}}" id="exampleInputEmail1" required
                                               placeholder="challenges" required
                                               autocomplete="challenges" autofocus>
                                        <span>To select unlimited enter -1</span>
                                        @error('challenges')
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
    @else
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add New Plan</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" method="post" action="{{ route('admin.plan.save') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Title</label>
                                        <input type="text" name="title"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="" id="exampleInputEmail1" required
                                               placeholder="Title" required
                                               autocomplete="title" autofocus>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Price/Month</label>
                                        <input type="number" name="price"
                                               class="form-control @error('price') is-invalid @enderror"
                                               value="" id="exampleInputEmail1" required
                                               placeholder="price"
                                               autocomplete="price" min="0" autofocus>
                                        @error('price')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Tax</label>
                                        <input type="number" name="tax"
                                               class="form-control @error('tax') is-invalid @enderror"
                                               value="" id="exampleInputEmail1" required
                                               placeholder="tax"
                                               autocomplete="tax" min="0" autofocus>
                                        <span>Will be count in percentage</span>

                                        @error('tax')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Sparring</label>
                                        <input type="number" name="sparring"
                                               class="form-control @error('sparring') is-invalid @enderror"
                                               value="" id="exampleInputEmail1" required
                                               placeholder="sparring" required
                                               autocomplete="sparring" min="-1" autofocus>
                                        <span>To select unlimited enter -1</span>

                                        @error('sparring')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Applications</label>
                                        <input type="number" name="applying"
                                               class="form-control @error('applying') is-invalid @enderror"
                                               value="" id="exampleInputEmail1" required
                                               placeholder="Applications" required
                                               autocomplete="applying" min="-1" autofocus>
                                        <span>To select unlimited enter -1</span>

                                        @error('applying')
                                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="exampleInputEmail1">Challenges</label>
                                        <input type="number" name="challenges"
                                               class="form-control @error('challenges') is-invalid @enderror"
                                               value="" id="exampleInputEmail1" required
                                               placeholder="challenges" required
                                               autocomplete="challenges" min="-1" autofocus>
                                        <span>To select unlimited enter -1</span>

                                        @error('challenges')
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

        @endif
        </div>

        <!-- /Interest modal End-->

@endsection
