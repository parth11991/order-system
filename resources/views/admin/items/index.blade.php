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
                    @can('create item')
                    <a href="{{ route('admin.item.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-button">
                        <span tooltip="Create new item." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Items</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Items</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Items List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <!-- <button onclick="exportTableToExcel('table')">Export Table Data To Excel File</button> -->
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr> 
                                <th>Image</th>
                                <th>SKU</th>
                                <th>Item Title</th>
                                <th>Supplier</th>
                                <th class="noExport" style="width: 100px;">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    
                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
</div>

<input type="hidden" name="selected_status" id="selected_status" value="0">
<script>
function datatables() {
    var checkRole = "{{auth()->user()->hasRole('supplier')}}";
    var url = "{{ url('admin/item/ajax/data') }}";
    var columns = [
                        {data: 'item_img', name: 'item_img'},
                        {data: 'sku', name: 'sku'},
                        {data: 'title', name: 'title'},
                        {data: 'users_avatars', name: 'users_avatars'},
                        {data: 'action', name: 'action', orderable: false, searchable: false,
                            fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                //  console.log( nTd );
                                $("a", nTd).tooltip({container: 'body'});
                            }
                        },
                        
                    ];
    
    
    var table = $('#table').DataTable({
        dom: 'RBfrtip',
        buttons: [],
        select: true,
        
        aaSorting     : [[0, 'asc']],
        iDisplayLength: 25,
        stateSave     : true,
        responsive    : true,
        fixedHeader   : true,
        processing    : true,
        serverSide    : true,
        "bDestroy"    : true,
        pagingType    : "full_numbers",
        ajax          : {
            url     : url,
            dataType: 'json'
        },
        columns       : columns,
    });
}

datatables();
</script>

@endsection
