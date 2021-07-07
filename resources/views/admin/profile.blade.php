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
                    <div class="box-body" style="">
    <form method="post" action="{{route('admin.profile.save')}}" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group col-md-6">
                    <label for="name" class="col-form-label">Name</label>
                    <input id="" type="text" name="name" value="{{$admin->name}}" class="form-control" placeholder="Enter name">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group col-md-6">
                    <label for="name" class="col-form-label">Email</label>
                    <input id="" type="text" name="email" value="{{$admin->email}}" class="form-control" placeholder="Enter email">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group col-md-6">
                    <label for="name" class="col-form-label">Profile Photo</label>
                    <input id="" type="file" name="profile" value="" class="form-control">
                </div>
            </div>

            {{--                <div class="col-md-12">--}}
            {{--                    <div class="form-group col-md-6">--}}
            {{--                        <label for="name" class="col-form-label">New Password</label>--}}
            {{--                        <input id="" type="text" name="password" class="form-control" placeholder="Enter your new password">--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--                <div class="col-md-12">--}}
            {{--                    <div class="form-group col-md-6">--}}
            {{--                        <label for="name" class="col-form-label">Confirm Password</label>--}}
            {{--                        <input id="" type="text" name="password_confirmation" class="form-control" placeholder="Enter your new password">--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            <div class="submit_btn form-group col-md-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
