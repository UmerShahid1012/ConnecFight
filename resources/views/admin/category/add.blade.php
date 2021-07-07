@extends('admin.dashboard.layout')

@section('content')

    <section class="content-header">
        <h1>
            GameGeek
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
                        <h3 class="box-title">Add New Category</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" method="post" action="{{ route('admin.category.save') }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="box-body">
                            <div class="col-md-12">
                                <div class="form-group has-feedback">
                                    <label for="exampleInputEmail1">Title</label>
                                    <input type="text" name="title"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{old('title')}}" id="exampleInputEmail1" required placeholder="Title" required
                                           autocomplete="title" autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="inputDesc">Description</label>
                                    <textarea class="form-control form-control-lg" name="description" required id="inputDesc"
                                              rows="3" placeholder=""></textarea>
                                    <span id="pdescription" class="error<?= 3; ?>" style="display:none; color:red">Product
                                    Description Required</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="inputDesc">Position</label>
                                    <input class="form-control form-control-lg" name="position" required
                                             placeholder="Position in Menu">
                                    <span id="pposition" class="error<?= 3; ?>" style="display:none; color:red">Product
                                    Position Required</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <img id="output_image"/>
                                    <div class="form-group">
{{--                                        <img id="edit_user_profile" src="" alt=""><br>--}}
                                        <label for="exampleInputFile">Upload Image</label>
                                        <input type="file" id="edit_user_input" name="image" required
                                               onchange="preview_image(event)">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <img id="output_cover"/>
                                    <div class="form-group">
{{--                                        <img id="edit_user_profile" src="" alt=""><br>--}}
                                        <label for="exampleInputFile">Upload Cover Image</label>
                                        <input type="file" name="cover" required onchange="preview_cover(event)">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <button type="button" class="add">Add</button>
                        <button type="button" class="remove">Remove</button>
                        <div id="new_sub_cat">

                        </div>
                        <input type="hidden" value="0" id="total_sub_cat">


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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.15/dist/summernote.min.js"></script>
    <script type='text/javascript'>
        $(document).ready(function () {
            // $('#inputDesc').summernote({height: 250});
        });

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                output.style.width = "250px";
                output.style.height = "250px";
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function preview_cover(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('output_cover');
                output.src = reader.result;
                output.style.width = "250px";
                output.style.height = "250px";
            }
            reader.readAsDataURL(event.target.files[0]);
        }



        $('.add').on('click', add);
        $('.remove').on('click', remove);

        function add() {
            var new_no = parseInt($('#total_sub_cat').val()) + 1;
            var new_input = "<lable>Sub Category</lable><input type='text' name='sub_cat[]'>";

            $('#new_sub_cat').append(new_input);

            $('#total_sub_cat').val(new_no);
        }

        function remove() {
            var last_no = $('#total_sub_cat').val();

            if (last_no > 1) {
                $('#new_' + last_no).remove();
                $('#total_sub_cat').val(last_no - 1);
            }
        }


    </script>
@endsection
