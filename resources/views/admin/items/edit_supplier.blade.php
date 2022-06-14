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
                    <h3 class="card-title">Update Item</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.item.update', ['Supplier_has_item' => $supplier_item_dimensions->id]) }}" method="post" id="popup-form-edit-supplier" class="mt-4">
                        @csrf
                        <div id="item_list">
                            <div class="form-group">
                                <label>Item</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="item" required value="{{$item->title}}" disabled>
                                </div>
                                
                            </div>
                        </div>
                        <div id="user_list">
                            <div class="form-group">
                                <label>Supplier &nbsp;</label>
                                <input class="form-control" type="text" name="user" required value="{{$user->name}}" disabled>
                                <input type="text" name="user_id" required value="{{$user->id}}" style="display: none;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Weight</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->product_weight}}" required id="product_weight" name="product_weight" class="form-control" placeholder="Product Weight">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Width</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->product_width}}" required id="product_width" name="product_width" class="form-control" placeholder="Product Width">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Length</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->product_length}}" required id="product_length" name="product_length" class="form-control" placeholder="Product Length">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Depth</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->product_depth}}" required id="product_depth" name="product_depth" class="form-control" placeholder="Product Depth">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Inner Quantity</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_inner_quantity}}" required id="box_inner_quantity" name="box_inner_quantity" class="form-control" placeholder="Box Inner Quantity">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Outer Qquantity</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_outer_quantity}}" required id="box_outer_quantity" name="box_outer_quantity" class="form-control" placeholder="Box Outer Qquantity">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Weight Net Kg</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_weight_net_kg}}" required id="box_weight_net_kg" name="box_weight_net_kg" class="form-control" placeholder="Box Weight Net Kg">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Weight Gross Kg</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_weight_gross_kg}}" required id="box_weight_gross_kg" name="box_weight_gross_kg" class="form-control" placeholder="Box Weight Gross Kg">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Width Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_width_cm}}" required id="box_width_cm" name="box_width_cm" class="form-control" placeholder="Box Width Cm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Length Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_length_cm}}" required id="box_length_cm" name="box_length_cm" class="form-control" placeholder="Box Length Cm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Box Depth Cm</label>
                            <div class="input-group">
                                <input type="number" min="0.00" value="{{$supplier_item_dimensions->box_depth_cm}}" required id="box_depth_cm" name="box_depth_cm" class="form-control" placeholder="Box Depth Cm">
                            </div>
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

    $("#user_id").select2({
      placeholder: "Select Supplier",
      allowClear: true
    });

    $(document).ready(function () {
        $(document).on('submit','#popup-form-edit-supplier',function(e){
            e.preventDefault();
            var url = $(this).attr('action');
            $("#pageloader").fadeIn();
            $.ajax({
                method: "POST",
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $(this).serialize(),
                success: function(message){
                    $("#popup-modal").modal('hide');
                    alert_message(message);
                    setTimeout(function() {   //calls click event after a certain time
                        $("#pageloader").hide();
                    }, 1000);
                },
                error: function (data){
                        console.log(data);
                        $("#pageloader").hide();
                }
            });
        }); 
    });

</script>

@endsection