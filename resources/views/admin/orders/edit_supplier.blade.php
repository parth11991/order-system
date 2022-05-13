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
                    <form action="{{ route('admin.order.update_supplier', ['order' => $order->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')
                        <div>
                            <!-- <div class="form-group">
                                <label>Search Item</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="search"  placeholder="Search item" autocomplete="search" id="search" autofocus maxlength="200">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="funSearchItems()"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div> -->

                            <div id="item_list" style="display:none;">
                                <div class="form-group">
                                    <label>Select Item</label>
                                    <select class="form-control select2" id="item" name="item" required autocomplete="name" >
                                            <option value="{{$order->item_id}}">{{$order->sku}} - {{$order->item_title}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="display:none;">
                                <label>Suppliers &nbsp;</label>
                                <select class="form-control select2" id="supplier_id" name="supplier_id" required autocomplete="supplier_id">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @if($supplier->id==$order->supplier_id) selected @endif>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" style="display:none;">
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
                            <div class="form-group" style="display:none;">
                                <label>Qty</label>
                                <input type="number" name="qty" class="form-control" required autocomplete="qty" value="{{$order->qty}}" autofocus>
                            </div>  
                        

                        <div class="form-group" style="display:none;">
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

    $("#status").select2({
      placeholder: "Select Status",
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

    @if(auth()->user()->hasRole('supplier'))
    function selected_status(){
        var selected_status = $('#selected_status').val();
        $('#status').val(selected_status);
        $('#status').trigger('change');
    }
    selected_status();
    @endif
</script>
@endsection