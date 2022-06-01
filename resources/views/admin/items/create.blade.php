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
                                <select class="form-control select2" id="user_id" name="user_id[]" required autocomplete="user_id" multiple>
                                    <option value=""></option>
                                </select>
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