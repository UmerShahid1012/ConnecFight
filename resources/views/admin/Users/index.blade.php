<style>
    .usersOptions select {
        width: 100%;
        padding: 8px 5px;
    }

    .usersOptions {
        width: 215px;
        margin-right: 15px;
    }

    .userFilters {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
</style>

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

                    {{--                <div class="col-md-12">--}}
                    {{--                    <a href="{{ route('admin.user.add') }}" type="button"--}}
                    {{--                        class="btn btn-primary pull-right identity">Add User</a>--}}
                    {{--                </div>--}}

                        <div class="box-body" style="overflow: scroll;">
                            @if($title == 'Matchmakers List' or $title == 'Athletes List')

                            <div class="userFilters">
                                <div class="usersOptions">
                                    <form action="{{route('get_result')}}" method="get">
                                    <select class="tag_search" id="tag" name="id">
                                        @foreach($tags as $t)
                                            <option value="{{$t->id}}">{{$t->name}}</option>
                                        @endforeach
                                    </select>
                                        <input type="hidden" id="title" name="title" value="{{$title}}">
                                        <button type="submit"  id="filter" style="display: none"></button>
                                    </form>
                                </div>
                            </div>
                            @endif

                            <table id="full_feature_datatable"
                                   class="table table-bordered table-striped table-responsive">
                                <thead>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Profile Image</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                    <th>Weight</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Height</th>
                                    <th>Stance</th>
                                    <th>Bio</th>
                                    <th>Tags</th>
                                    @if($title == 'Matchmakers List')
                                        <th>Arranged Fights</th>
                                    @else
                                        <th>Fights</th>
                                    @endif
                                    <th>Posted Sparrings</th>
                                    <th>Applied Sparrings</th>
{{--                                    @if($title == 'Athletes List')--}}
{{--                                        --}}{{--                                    <th>Best Records</th>--}}
{{--                                    @endif--}}
                                    <th>Stripe Status</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php
                                    $sr_num = 1
                                @endphp
                                @foreach($users as $u)
                                    <tr>
                                        <div class="replace">
                                        <td>
                                            {{ $sr_num }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            @if($u->profile_image != '')
                                                <img src="{{$u->profile_image}}"
                                                     class="img-circle" width="60px">
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $u->first_name }}</td>
                                        <td>{{ $u->last_name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ isset($u->gender)?$u->gender:"N/A"}}</td>
                                        <td>{{ isset($u->weight)?$u->weight:"N/A"}}</td>
                                        <td>{{ isset($u->country)?$u->country:"N/A"}}</td>
                                        <td>{{ isset($u->state)?$u->state:"N/A"}}</td>
                                        <td>{{ isset($u->city)?$u->city:"N/A"}}</td>
                                        <td>{{ isset($u->height)?$u->height:"N/A" }}</td>
                                        <td>{{ isset($u->stance->title)?$u->stance->title:"N/A"}}</td>
                                        <td>{{ isset($u->bio)?$u->bio:"N/A" }}</td>

                                        <td><a href="{{route('fetch.tags.by.user',['id'=>$u->id])}}" class=""><i
                                                    class="fa fa-tags"></i></a></td>
                                        @if($title == 'Matchmakers List')
                                            <td><a href="{{route('fetch.user.arranged.fights',['id'=>$u->id])}}"
                                                   class=""><i
                                                        class="">Arranged Fights</i></a></td>

                                        @else
                                            <td><a href="{{route('fetch.user.fights',['id'=>$u->id])}}" class=""><i
                                                        class="">Fights</i></a></td>

                                        @endif
                                        <td><a href="{{route('fetch.user.jobs',['id'=>$u->id,'type'=>'myJobs'])}}" class=""><i class="">Posted Sparrings</i></a>
                                        <td><a href="{{route('fetch.user.jobs',['id'=>$u->id,'type'=>'Applied'])}}" class=""><i class="">Applied Sparrings</i></a>
                                        </td>
                                        {{--                                @dd($u->docs->federal_id)--}}
                                        <td>
                                            @if($u->stripe_payout_account_id)
                                                <a href="#" style="color: green" disabled="">Connected</a>
                                            @else
                                                <a href="#" style="color: red" disabled="">Not connected</a>
                                            @endif
                                        </td>
                                            <td>
                                                @if($u->is_verified)
                                                    <a href="#" style="color: green" disabled="">Accepted</a>
                                                @elseif($u->is_rejected)
                                                    <a href="#" style="color: red" disabled="">Rejected</a>
                                                @else
                                                    <a href="#" style="color: deepskyblue" disabled="">Pending</a>
                                                @endif

                                            </td>
                                        @if(!empty($u->docs))
                                            <td>@if($u->docs['driving_license'])<a
                                                    href="{{$u->docs['driving_license']}}"
                                                    target="_blank" class=""><i
                                                        class="fa fa-file">#1</i></a>@endif @if($u->docs['federal_id'])
                                                    <a
                                                        href="{{$u->docs['federal_id']}}" target="_blank"><i
                                                            class="fa fa-file"
                                                            style="padding-left: 20px">#2</i></a>@endif
                                            </td>
                                        @else
                                            <td><b href="#" style="color: red" disabled="">No Documents Found</b></td>

                                        @endif
                                        <td>
                                            @if(!$u->deleted_at)
                                                @if($u->is_verified)
                                                <a href="{{route('accept_reject.user',['id'=>$u->id,'type'=>'rejected'])}}"><i
                                                        class="fa fa-close"></i></a>
                                                    @elseif($u->is_rejected)
                                                    <a href="{{route('accept_reject.user',['id'=>$u->id, 'type'=>'accepted'])}}"><i
                                                            class="fa fa-check"></i></a>
                                                    @else
                                                    <a href="{{route('accept_reject.user',['id'=>$u->id, 'type'=>'accepted'])}}"><i
                                                            class="fa fa-check"></i></a>
                                                    <a href="{{route('accept_reject.user',['id'=>$u->id,'type'=>'rejected'])}}"><i
                                                            class="fa fa-close"></i></a>
                                                    @endif
                                            @endif
                                            @if(!$u->deleted_at)
                                                <a href="{{route('ban_unban.user',['id'=>$u->id,'type'=>'block'])}}"><i
                                                        class="fa fa-ban"></i></a>
                                            @else
                                                <a href="{{route('ban_unban.user',['id'=>$u->id,'type'=>'unblock'])}}"><i
                                                        class="fa fa-undo"></i></a>
                                            @endif
                                            <a href="#" data-toggle="modal"
                                               data-target="#confirm-delete{{ $u->id }}" class="text-danger delete">
                                                <i class="fa fa-trash-o"></i>
                                            </a>

                                            {{--                                    <a href="{{ route('admin.category.edit', [$u->id]) }}" data-toggle="modal" data-target="#" class="text-primary">--}}
                                            {{--                                        <i class="fa fa-pencil-square-o"></i>--}}
                                            {{--                                    </a>--}}
                                            <div class="modal fade" id="confirm-delete<?=$u->id?>" tabindex="-1"
                                                 role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h3>Confirm</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h5>Are you sure you want to delete this user?</h5>
                                                        </div>
                                                        <div class="modal-footer">

                                                            <form action="{{ route('admin.user.delete', [$u->id]) }}"
                                                                  method="post">
                                                                @csrf
                                                                <button type="button"
                                                                        class="btn btn-default close_modal"
                                                                        data-dismiss="modal">Cancel
                                                                </button>

                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit" class="btn btn-danger">Delete
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{--  Delete Modal End  --}}
                                        </td>
                                        </div>
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
    <script>
        function sub_cat_edit(id) {
            $("#sub_cat_id_input_" + id).removeAttr('readonly');
            $("#sub_cat_id_button_" + id).css('display', 'block');
        }
    </script>
    <script>
        $(document).ready(function () {
            $(document).on('change', '.tag_search', function (evt) {
                $('#filter').trigger('click');
            });
            {{--    $(document).on('change', '.tag_search', function (evt) {--}}
            {{--        var tag = $('#tag :selected').val();--}}
            {{--        var title = $('#title').val();--}}
            {{--        // var data = {tag:tag};--}}
            {{--        $.ajax({--}}
            {{--            type: 'get',--}}
            {{--            url: '{{route('get_result')}}',--}}
            {{--            data: {id:tag,title:title},--}}
            {{--            //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^--}}
            {{--            success: function (result) {--}}
            {{--                console.log(result.data);--}}
            {{--                if (result.status == 'success') {--}}
            {{--                    var html = '';--}}
            {{--                    var num = 1;--}}
            {{--                    $.each(result.data, function (key, value) {--}}
            {{--                            html = '<td style="vertical-align: middle;">';--}}
            {{--                            if (+value.profile_image != '') {--}}
            {{--                                html = '  <img src="' + value.profile_image + '" class="img-circle" width="60px">';--}}
            {{--                            } else {--}}
            {{--                                html = '<span > N / A < /span> </td>';--}}
            {{--                            }--}}


            {{--                            html = ' <td>' + value.first_name + '</td>';--}}
            {{--                            html = ' <td>' + value.last_name + '</td>';--}}
            {{--                            html = ' <td>' + value.email + '</td>';--}}
            {{--                            html = ' <td> isset(' + value.gender + ')?' + value.gender + ':"N/A"</td>';--}}
            {{--                            html = ' <td> isset(' + value.weight + ')?' + value.weight + ':"N/A"</td>';--}}
            {{--                            html = '<td> isset(' + value.weight + ')?' + value.weight + ':"N/A"</td> ';--}}
            {{--                            html = '<td> isset(' + value.country + ')?' + value.country + ':"N/A"</td> ';--}}
            {{--                            html = '<td> isset(' + value.state + ')?' + value.state + ':"N/A"</td> ';--}}
            {{--                            html = '<td> isset(' + value.city + ')?' + value.city + ':"N/A"</td> ';--}}
            {{--                            html = '<td> isset(' + value.height + ')?' + value.height + ':"N/A"</td> ';--}}
            {{--                            html = '<td> isset(' + value.stance_id.title + ')?' + value.stance_id.title + ':"N/A"</td> ';--}}
            {{--                            html = '<td> isset(' + value.bio + ')?' + value.bio + ':"N/A" }}</td>';--}}
            {{--                            html = ' <td><a href="{{route('fetch.tags.by.user',['id'=>'+value.id+'])}}" class=""><iclass="fa fa-tags"></i></a></td>';--}}
            {{--                            if (title == 'Matchmakers List') {--}}
            {{--                                html = ' <td><a href="{{route('fetch.user.arranged.fights',['id'=>'+value.id+'])}}"><i>Arranged Fights</i></a></td> ';--}}

            {{--                            } else {--}}
            {{--                                html = ' <td> <a href = "{{route('fetch.user.fights',['id'=>'+value.id+'])}}"> <i> Fights < /i></a></td>';--}}

            {{--                            }--}}
            {{--                            html = '<td><a href="{{route('fetch.user.jobs',['id'=>'+value.id+'])}}" class=""><i class="">Sparrings</i></a></td>';--}}
            {{--                            html = '<td>';--}}
            {{--                            if (+value.is_verified) {--}}
            {{--                                html = '<a href="#" style="color: green" disabled="">Accepted</a>';--}}
            {{--                            } else {--}}
            {{--                                html = '<a href="#" style="color: deepskyblue" disabled="">Pending</a></td>';--}}
            {{--                            }--}}
            {{--                            if (+value.is_rejected) {--}}
            {{--                                html = '<a href="#" style="color: red" disabled="">Rejected</a>';--}}

            {{--                            } else {--}}
            {{--                                html = '<a href="#" style="color: deepskyblue" disabled="">Pending</a></td>';--}}
            {{--                            }--}}
            {{--                            if (+value.docs) {--}}
            {{--                                html = ' <td>@if('+value.docs.driving_license+')<a href="' + value.docs.driving_license + '" target="_blank" class=""><iclass="fa fa-file">#1</i></a>@endif ';--}}
            {{--                                html = ' @if('+value.docs.federal_id')<a href="' + value.docs.federal_id--}}
            {{--                                '" target="_blank"><i class="fa fa-file" style="padding-left: 20px">#2</i></a>@endif </td> ';--}}
            {{--                            } else {--}}
            {{--                                html = '<td><b href="#" style="color: red" disabled="">No Documents Found</b></td>';--}}
            {{--                            }--}}
            {{--                            html = '<td>';--}}
            {{--                            if (!+value.deleted_at) {--}}
            {{--                                html = '<a href="{{route('accept_reject.user',['id'=>'+value.id+', 'type'=>'accepted'])}}"><i class="fa fa-check"></i></a>';--}}
            {{--                                html = '<a href="{{route('accept_reject.user',['id'=>'+value.id+', 'type'=>'rejected'])}}"><i class="fa fa-close"></i></a>';--}}
            {{--                            }--}}
            {{--                            if (!+value.deleted_at) {--}}
            {{--                                html = ' <a href="{{route('ban_unban.user',['id'=>'+value.id+','type'=>'block'])}}"><i class="fa fa-ban"></i></a>';--}}
            {{--                            }--}}
            {{--                            else{--}}
            {{--                                html = ' <a href="{{route('ban_unban.user',['id'=>'+value.id+','type'=>'unblock'])}}"><i class="fa fa-undo"></i></a>';--}}

            {{--                        }--}}
            {{--                       --}}{{--html = ' <a href="#" data-toggle="modal" data-target="#confirm-delete'+value.id+'" class="text-danger delete"> <i class="fa fa-trash-o"></i> </a> ';--}}


            {{--                       --}}{{-- html = '<div class="modal fade" id="confirm-delete'+value.id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';--}}
            {{--                       --}}{{-- html = ' <div class="modal-dialog"> <div class="modal-content"> <div class="modal-header"> <h3>Confirm</h3> </div> <div class="modal-body"> <h5>Are you sure you want to delete this user?</h5> </div>';--}}
            {{--                       --}}{{-- html = '<div class="modal-footer"> <form action="{{ route('admin.user.delete', [$u->id]) }}" method="post">';--}}
            {{--                       --}}{{--    <?php @csrf ?>--}}
            {{--                       --}}{{--     html = '<button type="button"class="btn btn-default close_modal"data-dismiss="modal">Cancel </button>';--}}

            {{--                       --}}{{--     html = '<input type="hidden" name="_method" value="DELETE"> <button type="submit" class="btn btn-danger">Delete </button> </form> </div> </div> </div> </div> </td>';--}}

            {{--                        num++;--}}
            {{--                    });--}}

            {{--                    $('.replace').html(html);--}}
            {{--                }--}}
            {{--            }--}}
            {{--        });--}}
            {{--});--}}
        });
    </script>
@endsection
