@extends('admin.dashboard.layout')

@section('content')



    <section class="content-header">
        <h1>
            CNF
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
                        <a href="{{ route('admin.category.add') }}" type="button"
                           class="btn btn-primary pull-right identity">Add Category</a>
                    </div>
                    <div class="box-body">
                        <table id="full_feature_datatable" class="table table-bordered table-striped table-responsive">
                            <thead>
                            <tr>
                                <th>Sr# </th>
                                <th>Tag</th>
                                <th>Subtag</th>
                                <th>Best Record</th>
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
                                    <td>{{ $u->tag->name }}</td>
                                    <td>{{ isset($u->sub_tag)?$u->sub_tag->name:"N/A" }}</td>
                                   <td>
                                    @if($u->tag_id == 4)
                                   @if($u->best_record)
                                    {{ $u->best_record}} <a href="{{route('user.add.record',['id'=>$u->id,'type'=>'edit'])}}" class=""><i class="fa fa-pencil"></i></a>
                                         @else
                                             <a href="{{route('user.add.record',['id'=>$u->id, 'type'=>'add'])}}" class=""><i class="fa fa-plus"></i></a>

                                            @endif
                                        @else
                                            {{"N/A"}}
                                        @endif
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




@endsection


