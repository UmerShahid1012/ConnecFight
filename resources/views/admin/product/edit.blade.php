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
                        <h3 class="box-title">Edit Product</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" method="post" action="{{ route('admin.product.update') }}"
                          enctype="multipart/form-data">
                        <input type="hidden" value="{{ $product->id }}" name="product_id">
                        @csrf
                        <div class="box-body">
                            <div class="col-md-12">
                                <div class="form-group has-feedback">
                                    <label for="exampleInputEmail1">Title</label>
                                    <input type="text" name="title"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{$product->name}}" id="exampleInputEmail1" placeholder="Title" required
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
                                    <label for="inputDesc">SKU</label>
                                    <input class="form-control form-control-lg" type="text" value="{{$product->sku}}" name="sku" required placeholder="SKU">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="inputDesc">Year</label>
                                    <input class="form-control form-control-lg" name="year" type="number" value="{{$product->year}}" placeholder="Year">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Category</label>
                                    <select id="cat_select" name="category_id" class="form-control cat_select">
                                        <option value="0" disabled selected>Select Categories</option>
                                        @foreach($categories as $category)
                                            @if($category->name == "All")
                                                @continue
                                            @endif
                                            <option id="{{$category->id}}" value="{{$category->id}}" {{($category->id == $product->category_id)? 'selected':''}}>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12" id="sub_cat_div" style="display: none">
                                <div class="form-group">
                                    <label>Select Sub Category</label>
                                    <select id="sub_category_select" name="sub_category_id" class="form-control">

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="inputDesc">Description</label>
                                    <textarea class="form-control form-control-lg" name="description" id="inputDesc" rows="3" placeholder="">{{$product->description}}</textarea>
                                    <span id="pdescription" class="error<?= 3; ?>" style="display:none; color:red">Product
                                    Description Required</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="">Product Weight (lbs)</label>
                                    <input class="form-control form-control-lg" type="number" name="weight" value="{{$product->weight}}"  placeholder="Enter Product Weight e.g 0.25 (lbs)">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="">Meta Title</label>
                                    <input class="form-control form-control-lg" value="{{$product->meta_title}}" name="meta_title"  placeholder="Meta title">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="inputDesc">Meta Description</label>
                                    <textarea class="form-control form-control-lg" name="meta_description"  rows="3" placeholder="Meta Description">{{$product->meta_description}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="inputDesc">Meta Keywords</label>
                                    <textarea class="form-control form-control-lg" name="meta_keywords"  rows="3" placeholder="Meta Keywords">{{$product->meta_keywords}}</textarea>
                                </div>
                            </div>

{{--                            <div class="col-md-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="">Type</label>--}}
{{--                                    <div class="radio">--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="type" id="optionsRadios1" value="1" {{($product->is_new==1)? 'checked=""' : ''}} >--}}
{{--                                            New--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                    <div class="radio">--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="type" id="optionsRadios2" value="0" {{($product->is_new==0)? 'checked=""' : ''}}>--}}
{{--                                            Used--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="col-md-4">
                                <div class="col-md-8">
                                    <img id="output_image" @if($product->image) src="{{ Illuminate\Support\Facades\Storage::url($product->image)}}" width="200" height="200"  @endif>
                                    <div class="form-group">
                                        {{--                                        <img id="edit_user_profile" src="" alt=""><br>--}}
                                        <label for="exampleInputFile">Upload Image</label>
                                        <input type="file" id="edit_user_input" name="image"
                                               onchange="preview_image(event)">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="col-md-8">
                                    <img id="output_image2" @if($product->image2) src="{{ Illuminate\Support\Facades\Storage::url($product->image2)}}" width="200" height="200"  @endif>
                                    <div class="form-group">
                                        {{--                                        <img id="edit_user_profile" src="" alt=""><br>--}}
                                        <label for="exampleInputFile">Upload Image</label>
                                        <input type="file" id="edit_user_input2" name="image2"
                                               onchange="preview_image2(event)">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="col-md-8">
                                    <img id="output_image3" @if($product->image3) src="{{ Illuminate\Support\Facades\Storage::url($product->image3)}}" width="200" height="200"  @endif>
                                    <div class="form-group">
                                        {{--                                        <img id="edit_user_profile" src="" alt=""><br>--}}
                                        <label for="exampleInputFile">Upload Image</label>
                                        <input type="file" id="edit_user_input3" name="image3"
                                               onchange="preview_image3(event)">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Related Products</label>
                                    <select name="related_products[]" class="form-control related_products" id="edit-product-select2" multiple>
                                        @foreach($products as $r_product)
                                            <option  value="{{$r_product->id}}" {{in_array($r_product->id, $related_ids)===1 ?'selected':''}}>{{$r_product->name}}</option>
                                        @endforeach
                                    </select>
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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.15/dist/summernote.min.js"></script>
    <script type='text/javascript'>
        $(document).ready(function () {
            // $('#inputDesc').summernote({height: 250});
            // $related_ids
            $('#edit-product-select2').select2();
            $('#edit-product-select2').val(<?=json_encode($related_ids)?>);
            $('#edit-product-select2').trigger('change');
        });

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                output.style.width = "200px";
                output.style.height = "200px";
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function preview_image2(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('output_image2');
                output.src = reader.result;
                output.style.width = "200px";
                output.style.height = "200px";
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function preview_image3(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('output_image3');
                output.src = reader.result;
                output.style.width = "200px";
                output.style.height = "200px";
            }
            reader.readAsDataURL(event.target.files[0]);
        }
        var selectedCategory = $( "#cat_select option:selected" ).val();
        $.ajax({
            url: "{{url('admin/category/get_sub_categories')}}"+"/"+selectedCategory,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $("#sub_category_select").empty();
                if(res.length > 0){
                    $("#sub_cat_div").css('display', 'block');
                    $("#sub_category_select").append(new Option('Select Sub Category', '0'));
                    $.each(res, function (key, value) {
                        if( value.id == '{{$product->sub_category_id}}' ){
                            $("#sub_category_select").append(new Option(value.name, value.id, true));
                        }else{
                            $("#sub_category_select").append(new Option(value.name, value.id));
                        }
                    });

                }else{
                    $("#sub_cat_div").css('display', 'none');
                }
            }
        });

        $("select.cat_select").change(function(){
            var selectedCategory = $(this).children("option:selected").val();
            $.ajax({
                url: "{{url('admin/category/get_sub_categories')}}"+"/"+selectedCategory,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $("#sub_category_select").empty();
                    if(res.length > 0){
                        $("#sub_cat_div").css('display', 'block');
                        $("#sub_category_select").append(new Option('Select Sub Category', '0', true));
                        $.each(res, function (key, value) {
                            $("#sub_category_select").append(new Option(value.name, value.id));
                        });
                    }else{
                        $("#sub_cat_div").css('display', 'none');
                    }
                }
            });
        });
    </script>
@endsection
