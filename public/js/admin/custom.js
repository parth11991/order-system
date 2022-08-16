$(document).ready(function () {
    $('body').on('click', '#popup-modal-button', function(event) {
        $('#popup-modal-body').html('Loading..');
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(response) {
                $('#popup-modal-body').html(response);
            },
            error: function (data){
                    console.log(data);
            }
        });
        $('#sticky').removeClass('stick');
        $('#popup-modal').modal('show');
    });
});

function alert_message(message) {
    if(typeof(message.success) != "undefined" && message.success !== null) {
        var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        //$('#error_message').html(messageHtml);
        //$('#message').html(messageHtml);
        if(typeof(message.reload) != "undefined" && message.reload == 1){
            Swal.fire({icon: 'Success', title: 'Success!', text: message.success }).then((result) => {location.reload();});
        }else{
            Swal.fire({icon: 'Success', title: 'Success!', text: message.success })
        }

    }else if(typeof(message.delete) != "undefined" && message.delete !== null) {
        var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Delete: </strong> '+ message.delete +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        //$('#error_message').html(messageHtml);
        //$('#message').html(messageHtml);
        Swal.fire({ icon: 'delete', title: 'Delete!', text: message.delete })

    } else if(typeof(message.error) != "undefined" && message.error !== null){
        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        //$('#message').html(messageHtml);
        if(typeof(message.link) != "undefined" && message.link !== null){
            window.open(message.link, '_blank');
        }
        Swal.fire({ icon: 'error',  title: 'Oops...', text: message.error})
    }
    
}

$(document).ready(function () {
    $(document).on('submit','#popup-form',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $("#pageloader").fadeIn();

        var form = $(this).serializeArray();
        
        var data = new FormData(); // Creating object of FormData class
        for (var i = 0; i < form.length; i++) {
            data.append(form[i].name, form[i].value);
        }
        
        let TotalFiles = $('#files')[0].files.length; //Total files
        let files = $('#files')[0];
        for (let i = 0; i < TotalFiles; i++) {
            data.append('files' + i, files.files[i]);
        }
        
        data.append('TotalFiles', TotalFiles);
        
        $.ajax({
            method: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: data,
            cache:false,
            contentType: false,
            processData: false,
            success: function(message){
                $("#popup-modal").modal('hide');
                alert_message(message);
                setTimeout(function() {   //calls click event after a certain time
                    datatables();
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

$(document).ready(function () {
    $(document).on('submit','.delete-form',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        swal({
            title: "Delete?",
            text: "Are you sure want to delete it?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (r) {
            if (r.value === true) {
                $("#pageloader").fadeIn();
                $.ajax({
                    method: "POST",
                    url: url,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    success: function(message){
                        setTimeout(function() {   //calls click event after a certain time
                            datatables();
                            $("#pageloader").hide();
                            alert_message(message);
                        }, 1000);
                    },
                });
            } else {
                r.dismiss;
            }
        }, function (dismiss) {
            return false;
        })
    }); 

    $(document).on('click','#packingwavesCompletedNotification',function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var data = $(this).serialize();
        swal({
            title: "Send Notification?",
            text: "Are you sure you want to send Packing Waves Completed Notification to admin?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, Send it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (r) {
            if (r.value === true) {
                $("#pageloader").fadeIn();
                $.ajax({
                      method: "POST",
                      url: url,
                      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                      success: function(message){
                          alert_message(message);
                          setTimeout(function() {   //calls click event after a certain time
                              $("#pageloader").hide();
                          }, 1000);
                      }
                  }); 
            } else {
                r.dismiss;
            }
        }, function (dismiss) {
            return false;
        })
    }); 
});


$(document).ready(function () {
    $('body').on('click', '#popup-modal-form', function(event) {
        $('#popup-modal-body').html('Loading..');
        event.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            dataType: 'html',
            data:$(this).serialize(),
            type: "POST",
            success: function(response) {
                $('#popup-modal-body').html(response);
            },
            error: function (data){
                    console.log(data);
            }
        });
        $('#sticky').removeClass('stick');
        $('#popup-modal').modal('show');
    });
});
