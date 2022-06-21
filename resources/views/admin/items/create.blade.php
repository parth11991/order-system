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
                    <h3 class="card-title">Create Item</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.item.store') }}" method="post" id="popup-form" class="mt-4">
                        @csrf
                        <div class="form-group">
                            <label>Search Item</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="search"  placeholder="Search item" autocomplete="search" id="search" autofocus maxlength="200">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="funSearchItems()"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>

                        <div id="item_list">
                            <div class="form-group">
                                <label>Select Item</label>
                                <select class="form-control select2" id="item" name="item" required autocomplete="name" onchange="funSearchSuppliers()">
                                        <option value=""></option>
                                </select>
                            </div>
                        </div>

                        <div id="user_list">
                            <div class="form-group">
                                <label>Supplier &nbsp;</label>
                                <select class="form-control select2" id="user_id" name="user_id" required autocomplete="user_id">
                                    <option value=""></option>
                                </select>
                            </div>
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
                            <label>Box Outer Qquantity</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_outer_quantity" name="box_outer_quantity" class="form-control" placeholder="Box Outer Qquantity">
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

                        <div class="form-group">
                            <label>Box Depth Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="box_depth_cm" name="box_depth_cm" class="form-control" placeholder="Box Depth Cm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Supplier Code</label>
                            <div class="input-group">
                                <input type="text" value="" required id="supplier_code" name="supplier_code" class="form-control" placeholder="Supplier Code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Supplier Barcode</label>
                            <div class="input-group">
                                <input type="text" value="" required id="supplier_barcode" name="supplier_barcode" class="form-control" placeholder="Supplier Barcode">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Lead Time (Days)</label>
                            <div class="input-group">
                                <input type="number" min="0" value="" required id="lead_time" name="lead_time" class="form-control" placeholder="Lead Time (Days)">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Supplier Currency</label>
                            <div class="input-group">
                                <select class="form-control select2" id="supplier_currency" name="supplier_currency" required autocomplete="supplier_currency">
                                        <option value="GBP" selected>GBP</option>
                                        <option value="USD">USD</option>
                                        <option value="EURO">EURO</option>
                                        <option value="RMB">RMB</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Supplier Price</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="" required id="supplier_price" name="supplier_price" class="form-control" placeholder="Supplier Price">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Min Order Quantity</label>
                            <input type="number" name="min_order_quantity" class="form-control" required autocomplete="min_order_quantity" autofocus>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
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
        $.ajax({
          url : '{{ route('admin.item.ajax.search_items') }}',
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

    function funSearchSuppliers() {
        $("#pageloader").fadeIn();
        $.ajax({
          url : '{{ route('admin.item.ajax.search_suppliers') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "keyword": $('#item').val()
            },
          type: 'get',
          dataType: 'html',
          success: function( result )
          {
            $("#user_list").html(result);
            $("#user_id").select2({
              placeholder: "Select Supplier",
              allowClear: true
            });
            $("#pageloader").hide();
          }
        });
    }

    

    $("#user_id").select2({
      placeholder: "Select Supplier",
      allowClear: true
    });

</script>

@endsection