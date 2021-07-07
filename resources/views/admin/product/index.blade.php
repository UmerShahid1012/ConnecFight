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
                        <a href="{{ route('admin.product.add') }}" type="button"
                           class="btn btn-primary pull-right identity">Add Product</a>
                    </div>
                    <div class="box-body">
                        <table id="full_feature_datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Sr# </th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>SKU</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @php
                                $sr_num = 1
                            @endphp

                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        {{ $sr_num }}
                                        @php
                                            $sr_num++;
                                        @endphp
                                    </td>

                                    <td>{{ $product->name }}</td>
                                    <td style="vertical-align: middle;">
                                        @if($product->image != '')
                                            <img src="{{  Illuminate\Support\Facades\Storage::url($product->image) }}"
                                                 class="img-circle" width="60px">
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>
                                        <a href="#" onclick="showUserDetail({{ $product->id }})" data-toggle="modal"
                                           data-target="#confirm-delete{{ $product->id }}" class="text-danger delete">
                                            <i class="fa fa-trash-o"></i>
                                        </a>

                                        <a href="{{ route('admin.product.edit', [$product->id]) }}" data-toggle="modal" data-target="#" class="text-primary">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                        <div class="modal fade" id="confirm-delete<?=$product->id?>" tabindex="-1"
                                             role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3>Confirm</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h5>Are you sure you want to delete this product?</h5>
                                                    </div>
                                                    <div class="modal-footer">

                                                        <form action="{{ route('admin.product.delete', [$product->id]) }}"
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

    <script>
        function showUserDetail(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery(document).ready(function () {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: '<?php echo asset('
                admin / blog_detail ') ?>/' + id,
                    method: 'get',
                    success: function (result) {
                        $("#myModal").modal();
                    }
                });
            });
        }

    </script>
@endsection
