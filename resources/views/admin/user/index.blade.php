@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="message">
            @include('message.alert')
        </div>

        <div class="row col-sm-12 page-titles">
            <div class="col-lg-5 p-b-9 align-self-center text-left  " id="list-page-actions-container">
                <div id="list-page-actions">
                    <!--ADD NEW ITEM-->
                    @can('create user')
                    <a href="{{ route('admin.user.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-buttonUserRole">
                        <span tooltip="Create new team member." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Team Members</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Team Members</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Team Members List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body ">
                    <div class="table-responsive list-table-wrapper">
                        <table class="table table-hover" id="datatableUser">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    @canany(['edit user', 'delete user'])
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    <td><img src="{{ $user->getImageUrlAttribute($user->id) }}" alt="Admin" class="profile-user-img-small img-circle"> {{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->getRoleNames()->first() }}</td>
                                    <td>{{ $user->date }}</td>
                                    <td>
                                        <div class="dropdown action-label">
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o @if($user->status=='1') text-success @else text-danger @endif "></i> @if($user->status=='1') Active @else Inactive @endif </a>
                                            <div class="dropdown-menu dropdown-menu-right" style="">
                                                <a class="dropdown-item" href="#" onclick="funChangeStatus({{$user->id}},1); return false;"><i class="fa fa-dot-circle-o text-success"></i> Active</a>
                                                <a class="dropdown-item" href="#" onclick="funChangeStatus({{$user->id}},0); return false;"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @can('edit user')
                                        <a href="{{ route('admin.user.edit', ['user' => $user->id]) }}" 
                                            class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-buttonUserRole">
                                            <span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span>
                                        </a>
                                        @endcan 
                                        @can('delete user')
                                        <form method="post" class="float-left delete-formUserRole"
                                            action="{{ route('admin.user.destroy', ['user' => $user->id ]) }}">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <span tooltip="Delete" flow="right"><i class="fas fa-trash-alt"></i></span>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">There is no user.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function datatables() {
        var table = $('#datatableUser').DataTable({
            dom: 'Bfrtip',
            buttons: []
        });
    }
    datatables();

    /*For user status change*/
    function funChangeStatus(id,status) {
        //$("#pageloader").fadeIn();
        $.ajax({
          url : '{{ route('admin.user.ajax.change_status') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id,
            "status": status
            },
          type: 'get',
          dataType: 'json',
          success: function( result )
          {
            $("#datatableUser").load(location.href + " #datatableUser");
            var table = $('#datatableUser').DataTable({
                dom: 'Bfrtip',
                buttons: []
            });
            //$("#pageloader").hide();
          }
        });
    }

</script>
@endsection