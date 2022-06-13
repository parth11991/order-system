@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="error_message">
            @include('message.alert')
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <!-- <button onclick="exportTableToExcel('table')">Export Table Data To Excel File</button> -->
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr> 
                                <th>SKU</th>
                                <th>Item Title</th>
                                <th>Image</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr> 
                                    <td>{{$itemData['ItemNumber']}}</td>
                                    <td>{{$itemData['ItemTitle']}}</td>
                                    <td><img src="{{$itemImage[0]['FullSource']}}" height="100px"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive list-table-wrapper">
                        <!-- <button onclick="exportTableToExcel('table')">Export Table Data To Excel File</button> -->
                        
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr> 
                                <th>Supplier</th>
                                <th>Supplier Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($item->users as $user)
                                <tr> 
                                    <td><img src="{{$user->getImageUrlAttribute($user->id)}}" height="50" /></td>
                                    <td>{{$user->name}}</td>
                                    <td><a href="{{ route('admin.edit_item_supplier', ['item' => $item->id,'user'=>$user->id]) }}" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a></td>
                                </tr>
                                @endforeach
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
</script>
@endsection