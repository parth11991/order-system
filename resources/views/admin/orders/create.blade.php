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

                        <div class="form-group">
                            <label>Suppliers &nbsp;</label>
                            <select class="form-control select2" id="supplier_id" name="supplier_id" required autocomplete="supplier_id">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text">GBP</span>
                              </div>
                              <input type="number" min="0.00" step="0.05" value="" required id="price" name="price" class="form-control" placeholder="Price">
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

</script>

@endsection