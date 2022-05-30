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
                    @can('create order')
                    <a href="{{ route('admin.order.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-button">
                        <span tooltip="Create new order." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Orders</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Orders</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Orders List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <!-- <button onclick="exportTableToExcel('table')">Export Table Data To Excel File</button> -->
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr>   
                                <th>Item Image</th> 
                                <th>Company</th> 
                                <th>SKU</th>
                                <th>Item Title</th>
                                <th>Price</th>
                                <th>QTY</th>
                                <th>Due Date</th>
                                <th>Order Date</th>
                                <th class="noExport">Status</th>
                                @if(!auth()->user()->hasRole('supplier'))
                                <th class="noExport" style="width: 100px;">Action</th>
                                @else
                                <th class="noExport" style="width: 100px;">Action</th>
                                @endif

                                <th>Status</th>
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
    if(checkRole=="1"){
        var url = "{{ url('admin/order/ajax/data_supplier') }}";
        var columns = [
                            {data: 'item_img', name: 'item_img'},
                            {data: 'company_name', name: 'company_name'},
                            {data: 'customer_sku', name: 'customer_sku'},
                            {data: 'item_title', name: 'item_title'},
                            {data: 'price', name: 'price'},
                            {data: 'qty', name: 'qty'},
                            {data: 'due_date', name: 'due_date'},
                            {data: 'order_date', name: 'order_date'},
                            {data: 'order_status', name: 'order_status'},
                            {data: 'action', name: 'action', orderable: false, searchable: false,
                                fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                    //  console.log( nTd );
                                    $("a", nTd).tooltip({container: 'body'});
                                }
                            },
                            {data: 'status_field', name: 'status_field',visible: false},
                            
                        ];
    }else{
        var url = "{{ url('admin/order/ajax/data') }}";
        var columns = [
                            {data: 'item_img', name: 'item_img'},
                            {data: 'company_name', name: 'company_name'},
                            {data: 'sku', name: 'sku'},
                            {data: 'item_title', name: 'item_title'},
                            {data: 'price', name: 'price'},
                            {data: 'qty', name: 'qty'},
                            {data: 'due_date', name: 'due_date'},
                            {data: 'order_date', name: 'order_date'},
                            {data: 'order_status', name: 'order_status'},
                            {data: 'action', name: 'action', orderable: false, searchable: false,
                                fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                    //  console.log( nTd );
                                    $("a", nTd).tooltip({container: 'body'});
                                }
                            },
                            {data: 'status_field', name: 'status_field',visible: false},
                        ];
    }
    
    var table = $('#table').DataTable({
        dom: 'RBfrtip',
        buttons: [{
                        extend: 'pdf',
                        title: 'Order Data Export',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3,4,5,6,7,10 ]
                        },
                        customize: function(doc) {
                           //find paths of all images, already in base64 format
                           var arr2 = $('.img-fluid').map(function(){
                                          return this.src;
                                     }).get();
                     
                         for (var i = 0, c = 1; i < arr2.length; i++, c++) {
                            console.log(doc);
                                           doc.content[1].table.body[c][0] = {
                                             image: arr2[i],
                                             width: 100
                                           }
                                             }
                         },
                    },{
                        extend: 'excel',
                        title: 'Order Data Export',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3,4,5,6,7,10 ]
                        },
                        
                    }, {
                        extend: 'csv',
                        title: 'Order Data Export',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3,4,5,6,7,10 ]
                        }
                    }
                ],
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

function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/csv';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = filename?filename+'.xls':'excel_data.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}

function funChangeStatus(id,status) {
    var checkRole = "{{auth()->user()->hasRole('supplier')}}";
    if(checkRole=="1"){
        $('#selected_status').val(status);
        $(".edit_"+id).click()
    }else{
        $("#pageloader").fadeIn();
        $.ajax({
          url : '{{ route('admin.order.ajax.change_status') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id,
            "status": status
            },
          type: 'get',
          dataType: 'json',
          success: function( result )
          {
            datatables();
            $("#pageloader").hide();
          }
        });  
    }
}
</script>

@endsection
