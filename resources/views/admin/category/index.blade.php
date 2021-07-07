@extends('admin.dashboard.layout')

@section('content')



<section class="content-header">
    <h1>
        GameGeek
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
                    <table id="full_feature_datatable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Sr# </th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Position</th>
                                <th>Sub Categories</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                            $sr_num = 1
                            @endphp

                            @foreach($categories as $category)
                            <tr>
                                <td>
                                    {{ $sr_num }}
                                </td>

                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description }}</td>
                                <td style="vertical-align: middle;">
                                    @if($category->icon_image != '')
                                        <img src="{{  Illuminate\Support\Facades\Storage::url($category->icon_image) }}"
                                             class="img-circle" width="60px">
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </td>
                                <td>{{ $category->position }}</td>

                                <td><button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal{{$sr_num}}">View List</button></td>

                                <td>
                                    <a href="#" data-toggle="modal"
                                       data-target="#confirm-delete{{ $category->id }}" class="text-danger delete">
                                        <i class="fa fa-trash-o"></i>
                                    </a>

                                    <a href="{{ route('admin.category.edit', [$category->id]) }}" data-toggle="modal" data-target="#" class="text-primary">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                    <div class="modal fade" id="confirm-delete<?=$category->id?>" tabindex="-1"
                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3>Confirm</h3>
                                                </div>
                                                <div class="modal-body">
                                                    <h5>Are you sure you want to delete this category?</h5>
                                                </div>
                                                <div class="modal-footer">

                                                    <form action="{{ route('admin.category.delete', [$category->id]) }}"
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
@foreach($categories as $sr_num => $category)
<!-- Modal -->
<div id="myModal{{$sr_num + 1}}" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Sub Categories</h4>
            </div>
            <div class="modal-body">
                <table id="full_feature_datatable" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Sr# </th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach(\App\SubCategory::where('category_id', $category->id)->get() as $key => $sub_category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <form action="{{ route('admin.sub.category.edit') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="sub_cat" value="{{$sub_category->id}}">
                                    <input id="sub_cat_id_input_{{$key}}" type="text" name="name" value="{{ $sub_category->name }}" required readonly>
                                    <button id="sub_cat_id_button_{{$key}}" type="submit" class="btn btn-danger" style="display: none">Update</button>
                                </form>
                            </td>
                            <td>
                                <button onclick="sub_cat_edit({{$key}})">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                                |
                                <a href="#" data-toggle="modal" data-target="#confirm-delete-sub-cat{{ $sub_category->id }}">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </td>
                            {{--  Delete Sub Category Modal   --}}
                            <div class="modal fade" id="confirm-delete-sub-cat{{$sub_category->id}}" tabindex="-1"
                                 role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3>Confirm</h3>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Are you sure you want to delete this sub category?</h5>
                                        </div>
                                        <div class="modal-footer">

                                            <form action="{{ route('admin.sub.category.delete', [$sub_category->id]) }}"
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
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
@endforeach



@endsection


@section('js')
    <script>
        function sub_cat_edit(id){
            $("#sub_cat_id_input_"+id).removeAttr('readonly');
            $("#sub_cat_id_button_"+id).css('display', 'block');
        }
    </script>
@endsection
