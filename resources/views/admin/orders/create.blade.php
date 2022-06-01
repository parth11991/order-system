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
                    <h3 class="card-title">Create Order</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.order.store') }}" method="post" id="popup-form" class="mt-4">
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
                                <select class="form-control select2" id="item" name="item" required autocomplete="name">
                                        <option value=""></option>
                                </select>
                            </div>
                        </div>

                        <div id="user_list">
                            <div class="form-group">
                                <label>Supplier &nbsp;</label>
                                <select class="form-control select2" id="supplier_id" name="supplier_id" required autocomplete="supplier_id">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>
                        

                        <div class="form-group">
                            <label>Company &nbsp;</label>
                            <select class="form-control select2" id="company_id" name="company_id" required autocomplete="company_id">
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <div class="input-group">
                                <div class="col-2">
                                    <select class="form-control select2" id="currency" name="currency" required autocomplete="currency">
                                        <option value="GBP" selected>GBP</option>
                                        <option value="USD">USD</option>
                                        <option value="EURO">EURO</option>
                                        <option value="RMB">RMB</option>
                                    </select>
                                </div>
                                <div class="col-10">
                                  <input type="number" min="0.00" value="" required id="price" name="price" class="form-control" placeholder="Price">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Old Price</label>
                            <div class="input-group">
                              
                                <div class="col-2">
                                    <select class="form-control select2" id="old_price_currency" name="old_price_currency" required autocomplete="old_price_currency">
                                        <option value="GBP" selected>GBP</option>
                                        <option value="USD">USD</option>
                                        <option value="EURO">EURO</option>
                                        <option value="RMB">RMB</option>
                                    </select>
                                </div>
                                <div class="col-10">
                                    <input type="number" min="0.00" value="" required id="old_price" name="old_price" class="form-control" placeholder="Old Price">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>New Price</label>
                            <div class="input-group">
                              <div class="col-2">
                                    <select class="form-control select2" id="new_price_currency" name="new_price_currency" required autocomplete="new_price_currency">
                                        <option value="GBP" selected>GBP</option>
                                        <option value="USD">USD</option>
                                        <option value="EURO">EURO</option>
                                        <option value="RMB">RMB</option>
                                    </select>
                                </div>
                                <div class="col-10">
                                    <input type="number" min="0.00" value="" required id="new_price" name="new_price" class="form-control" placeholder="New Price">
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" name="qty" class="form-control" required autocomplete="qty" autofocus>
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
          }
        });
    }

    $("#user_id").select2({
      placeholder: "Select Supplier",
      allowClear: true
    });

</script>

@endsection