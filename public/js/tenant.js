$(document).ready(function () {
    loadUsers();
    $("#loadUsers").removeClass('d-none');
    $("#userName").html(localStorage.getItem('username'));
});

$(document).on('click', '#loadUsers', function(){
  loadUsers();
});

function loadUsers(page=null)
{
  if (localStorage.getItem('role') != 2) {
    return false;
  }
  
    if (!localStorage.getItem('jwt_token')) {
      window.location.href = '/login';
    }
    const token = localStorage.getItem('jwt_token');
    var url = '/api/getusers';
    if (page) {
      url = '/api/getusers?page='+page;
    }
    $.ajax({
        url: url,
        method: 'GET',
        headers: {
          Authorization: 'Bearer ' + token,
          schema: localStorage.getItem('schema')
        },
        success: function (response) {
          $("#content").html(response.html);
          $('#pagination').html(response.links);

        },
        error: function (xhr) {
          if (xhr.status == 401) {
            window.location.href = '/login';

          }
        }
      });
}

$(document).on('click', '#addUser', function(){
  $("#addUserModal").find('input[type="text"]').val('');
  $("#addUserModal").find('input[type="email"]').val('');
  $("#addUserModal").find('.text-danger').addClass('d-none');
 
  $("#tenantId").val($(this).data('id'));
  $("#tenantPrefix").val($(this).data('prefix'));
  
  $("#addUserModal").modal('show');
});

$(document).on('click', '#cancelAddUser', function(){
  
  $("#addUserModal").modal('hide');
});

$('#addUserModal').on('show.bs.modal', function () {
  $(this).find('input[type="text"]').val('');
  $(this).find('.text-danger').addClass('d-none');
});

$(document).on('click', '#registerUser', function(e){

  var errorCount = 0;
  $('.input-required').each(function(){

    if ($(this).val() == '') {
        $(this).next().removeClass('d-none');
        errorCount++;
      } else {
        $(this).next().addClass('d-none');

      }
    });
    if (errorCount) {
      return false;
    }

    if (!localStorage.getItem('jwt_token')) {
      window.location.href = '/login';
    }
    const token = localStorage.getItem('jwt_token');

    $.ajax({
      url: '/api/adduser',
      method: 'post',
      headers: {
        Authorization: 'Bearer ' + token,
        schema : localStorage.getItem('schema')
      },
      data: {
        name: $("#name").val(),
        email: $("#email").val(),
        role: $("#role").val()
      },
      async:false,
      success: function (data) {
       
        if (data.status == true) {
         
          $("#addUserModal").modal('hide');
          localStorage.setItem('redirected', data.redirected);


          loadUsers();

        }  
        
        
      },
      complete: function() {
         

      },
      error: function (xhr) { 
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors;

      

          // Loop through and display
          $.each(errors, function(field, messages) {
            
            $("#registerUserError").html(`<div class="text-danger">${messages[0]}</div>`);
          });
        } else if (xhr.status === 401) {
            window.location.href = '/login';  
        }
      }
  });
});

$("#logoutButton").on('click', function(){
    localStorage.clear();
    window.location.href = '/login';
});
 