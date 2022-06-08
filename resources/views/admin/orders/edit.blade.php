@extends('admin.layouts.popup')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="error_message">
            @include('message.alert')
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="card-title">Edit Order</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.order.update', ['order' => $order->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')
                        <div @if(auth()->user()->hasRole('supplier')) style="display:none;" @endif>
                            <!-- <div class="form-group">
                                <label>Search Item</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="search"  placeholder="Search item" autocomplete="search" id="search" autofocus maxlength="200">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="funSearchItems()"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div> -->

                            <div id="item_list">
                                <div class="form-group">
                                    <label>Select Item</label>
                                    <select class="form-control select2" id="item" name="item" required autocomplete="name" >
                                            <option value="{{$order->item_id}}">{{$order->sku}} - {{$order->item_title}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Suppliers &nbsp;</label>
                                <select class="form-control select2" id="supplier_id" name="supplier_id" required autocomplete="supplier_id" onchange="funGetSupplierItemDimensions(this.value)">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @if($supplier->id==$order->supplier_id) selected @endif>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Company &nbsp;</label>
                                <select class="form-control select2" id="company_id" name="company_id" required autocomplete="company_id">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @if($company->id==$order->company_id) selected @endif>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Price</label>
                                <div class="input-group">
                                    <div class="col-2">
                                        <select class="form-control select2" id="currency" name="currency" required autocomplete="currency">
                                            <option value="GBP" @if($order->currency=='GBP') selected @endif>GBP</option>
                                            <option value="USD" @if($order->currency=='USD') selected @endif>USD</option>
                                            <option value="EURO" @if($order->currency=='EURO') selected @endif>EURO</option>
                                            <option value="RMB" @if($order->currency=='RMB') selected @endif>RMB</option>
                                        </select>
                                    </div>
                                    <div class="col-10">
                                        <input type="number" min="0.00" value="{{$order->price}}" required id="price" name="price" class="form-control" placeholder="Price">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Old Price</label>
                                <div class="input-group">
                                    <div class="col-2">
                                        <select class="form-control select2" id="old_price_currency" name="old_price_currency" required autocomplete="old_price_currency">
                                            <option value="GBP" @if($order->old_price_currency=='GBP') selected @endif>GBP</option>
                                            <option value="USD" @if($order->old_price_currency=='USD') selected @endif>USD</option>
                                            <option value="EURO" @if($order->old_price_currency=='EURO') selected @endif>EURO</option>
                                            <option value="RMB" @if($order->old_price_currency=='RMB') selected @endif>RMB</option>
                                        </select>
                                    </div>
                                    <div class="col-10">
                                        <input type="number" min="0.00" value="{{$order->old_price}}"  required id="old_price" name="old_price" class="form-control" placeholder="Old Price">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>New Price</label>
                                <div class="input-group">
                                    <div class="col-2">
                                        <select class="form-control select2" id="new_price_currency" name="new_price_currency" required autocomplete="new_price_currency">
                                            <option value="GBP" @if($order->new_price_currency=='GBP') selected @endif>GBP</option>
                                            <option value="USD" @if($order->new_price_currency=='USD') selected @endif>USD</option>
                                            <option value="EURO" @if($order->new_price_currency=='EURO') selected @endif>EURO</option>
                                            <option value="RMB" @if($order->new_price_currency=='RMB') selected @endif>RMB</option>
                                        </select>
                                    </div>
                                    <div class="col-10">
                                        <input type="number" min="0.00" value="{{$order->new_price}}"  required id="new_price" name="new_price" class="form-control" placeholder="New Price">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" name="qty" class="form-control" required autocomplete="qty" value="{{$order->qty}}" autofocus>
                            </div>  
                        

                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="date" name="due_date" class="form-control" autocomplete="due_date" autofocus value="{{$order->due_date}}">
                        </div>

                        <div class="form-group">
                            <label>Status &nbsp;</label>
                            <select class="form-control select2" id="status" name="status" required autocomplete="status">
                                <option value="0" @if($order->status==0) selected @endif>new order</option>
                                <option value="1" @if($order->status==1) selected @endif>confirmed</option>
                                <option value="2" @if($order->status==2) selected @endif>shipped</option>
                                @if(!auth()->user()->hasRole('supplier'))
                                <option value="3" @if($order->status==3) selected @endif>
                                received</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Product Weight</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="product_weight" name="product_weight" class="form-control" placeholder="Product Weight">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Width</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="product_width" name="product_width" class="form-control" placeholder="Product Width">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Length</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="product_length" name="product_length" class="form-control" placeholder="Product Length">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Depth</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="product_depth" name="product_depth" class="form-control" placeholder="Product Depth">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Inner Quantity</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_inner_quantity" name="box_inner_quantity" class="form-control" placeholder="Box Inner Quantity">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Outer Quantity</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_outer_quantity" name="box_outer_quantity" class="form-control" placeholder="Box Outer Quantity">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Weight Net Kg</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_weight_net_kg" name="box_weight_net_kg" class="form-control" placeholder="Box Weight Net Kg">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Weight Gross Kg</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_weight_gross_kg" name="box_weight_gross_kg" class="form-control" placeholder="Box Weight Gross Kg">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Width Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_width_cm" name="box_width_cm" class="form-control" placeholder="Box Width Cm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Length Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_length_cm" name="box_length_cm" class="form-control" placeholder="Box Length Cm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Depth Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_depth_cm" name="box_depth_cm" class="form-control" placeholder="Box Depth Cm">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="" class="btn btn-secondary"  data-dismiss="modal">Close</a>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // jQuery Validation
    $(function(){
        $('#popup-form').validate(
        {
            rules:{
              
            }
        }); //valdate end
    }); //function end

    function funSearchItems() {
        $("#pageloader").fadeIn();
        $("#latest_order").hide();
        $.ajax({
          url : '{{ route('admin.order.ajax.search_items') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "keyword": $('#search').val()
            },
          type: 'get',
          dataType: 'html',
          success: function( result )
          {
            $("#item_list").html(result);
            $("#item").select2({
              placeholder: "Select item",
              allowClear: true
            });
            $("#pageloader").hide();
          }
        });
    }

    $("#supplier_id").select2({
      placeholder: "Select Supplier",
      allowClear: true
    });

    $("#currency").select2({
      placeholder: "Select Currency",
      allowClear: false
    });

    $("#old_price_currency").select2({
      placeholder: "Select Currency",
      allowClear: false
    });

    $("#new_price_currency").select2({
      placeholder: "Select Currency",
      allowClear: false
    });

    function funSearchSuppliers() {
        $("#pageloader").fadeIn();
        $.ajax({
          url : '{{ route('admin.order.ajax.search_suppliers') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "keyword": $('#item').val()
            },
          type: 'get',
          dataType: 'html',
          success: function( result )
          {
            $("#user_list").html(result);
            $("#supplier_id").select2({
              placeholder: "Select Supplier",
              allowClear: true
            });
            $("#pageloader").hide();
            $("#latest_order").fadeIn();
          }
        });


        var url = "{{ url('admin/order/ajax/latest_order') }}";
        var columns = [
                            {data: 'item_img', name: 'item_img'},
                            {data: 'supplier', name: 'supplier'},
                            {data: 'company_name', name: 'company_name'},
                            {data: 'sku', name: 'sku'},
                            {data: 'item_title', name: 'item_title'},
                            {data: 'price', name: 'price'},
                            {data: 'qty', name: 'qty'},
                            {data: 'due_date', name: 'due_date'},
                            {data: 'order_date', name: 'order_date'},
                            {data: 'order_status', name: 'order_status'},
                        ];
        
        var table = $('#table_latest_order').DataTable({
            dom: 'RBfrtip',
            buttons: [],
            select: true,
            iDisplayLength: 5,
            stateSave     : true,
            responsive    : true,
            fixedHeader   : true,
            processing    : false,
            serverSide    : true,
            "bDestroy"    : true,
            pagingType    : "full_numbers",
            ajax          : {
                url     : url,
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "keyword": $('#item').val()
                }
            },
            columns       : columns,
        });
    }

    function funGetSupplierItemDimensions(user_id) {
        $.ajax({
          url : '{{ route('admin.order.ajax.get_supplier_item_dimensions') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "item": $('#item').val(),
            "user_id": user_id
            },
          type: 'get',
          dataType: 'json',
          success: function( result )
          {
            if (typeof result.id !== 'undefined') {
                $("#product_weight").val(result.product_weight);
                $("#product_width").val(result.product_width);
                $("#product_length").val(result.product_length);
                $("#product_depth").val(result.product_depth);
                $("#box_inner_quantity").val(result.box_inner_quantity);
                $("#box_outer_quantity").val(result.box_outer_quantity);
                $("#box_weight_net_kg").val(result.box_weight_net_kg);
                $("#box_weight_gross_kg").val(result.box_weight_gross_kg);
                $("#box_width_cm").val(result.box_width_cm);
                $("#box_length_cm").val(result.box_length_cm);
                $("#box_depth_cm").val(result.box_depth_cm);
            }else{
                $("#product_weight").val('');
                $("#product_width").val('');
                $("#product_length").val('');
                $("#product_depth").val('');
                $("#box_inner_quantity").val('');
                $("#box_outer_quantity").val('');
                $("#box_weight_net_kg").val('');
                $("#box_weight_gross_kg").val('');
                $("#box_width_cm").val('');
                $("#box_length_cm").val('');
                $("#box_depth_cm").val('');
            }
          }
        });
    }

    $("#user_id").select2({
      placeholder: "Select Supplier",
      allowClear: true
    });

    funGetSupplierItemDimensions({{ $order->supplier_id }});
</script>

@endsection