$(document).ready(function () {
  loadTenants();
  $("#userName").html(localStorage.getItem('username'));
});

function loadTenants(page=null)
{
  
    if (!localStorage.getItem('jwt_token')) {
      window.location.href = '/login';
    }
    const token = localStorage.getItem('jwt_token');

    var url = '/api/gettenants';
    if (page) {
      url = '/api/gettenants?page=' + page
    }
    $.ajax({
        url: url,
        method: 'GET',
        headers: {
          Authorization: 'Bearer ' + token
        },
        success: function (response) {
          $("#content").html(response.html);
          $('#pagination').html(response.links);
          console.log(response.links);
        },
        error: function (xhr) {
          if (xhr.status == 401) {
            window.location.href = '/login';

          }
        }
      });
}

$(document).on('click', '#addTenant', function(){
  $("#addTenantModal").find('input[type="text"]').val('');
  $("#addTenantModal").find('.text-danger').addClass('d-none');
  $("#addTenantModal").modal('show');
});

$(document).on('click', '#addUser', function(){
  $("#addUserModal").find('input[type="text"]').val('');
  $("#addUserModal").find('input[type="email"]').val('');
  $("#addUserModal").find('.text-danger').addClass('d-none');
 
  $("#tenantId").val($(this).data('id'));
  $("#tenantPrefix").val($(this).data('prefix'));
  
  $("#addUserModal").modal('show');
});

$(document).on('click', '#cancelAddTenant', function(){
  
  $("#addTenantModal").modal('hide');
});

$(document).on('click', '#cancelAddUser', function(){
  
  $("#addUserModal").modal('hide');
});

$('#addUserModal').on('show.bs.modal', function () {
  $(this).find('input[type="text"]').val('');
  $(this).find('.text-danger').addClass('d-none');
});

$(document).on('click', '#loadTenants', function(){
  loadTenants();
});

$(document).on('click', '#registerTenant', function(e){

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
      url: '/api/registertenant',
      method: 'post',
      headers: {
        Authorization: 'Bearer ' + token
      },
      data: {
        name: $("#name").val(),
        prefix: $("#prefix").val(), 
      },
      success: function (data) {
        console.log(data);
        if (data.status == true) {
          loadTenants();
          $("#addTenantModal").modal('hide');


        }  
        
        
      },
      error: function (xhr) {
        console.log('erro');
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors;

      

          // Loop through and display
          $.each(errors, function(field, messages) {
            
            $("#registerTenantError").html(`<div class="text-danger">${messages[0]}</div>`);
          });
        } else if (xhr.status === 401) {
            window.location.href = '/login';  
        }
      }
  });
});

$(document).on('click', '#registerUser', function(e){

  var errorCount = 0;
  var tenantId = $("#tenantId").val();
  var tenantPrefix = $("#tenantPrefix").val();
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
      url: '/api/registeruser',
      method: 'post',
      headers: {
        Authorization: 'Bearer ' + token
      },
      data: {
        name: $("#name").val(),
        email: $("#email").val(), 
        tenantId: tenantId,
        role: $("#role").val()
      },
      async:false,
      success: function (data) {
       
        if (data.status == true) {
         
          $("#addUserModal").modal('hide');

          manageTenant(tenantPrefix);

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

$(document).on('click', '.manageTenant', function(){
  
  var tenantId =  $(this).data('id');
  manageTenant(tenantId);


});

function manageTenant(tenantId, page=null) {
  if (!localStorage.getItem('jwt_token')) {
    window.location.href = '/login';
  }
  const token = localStorage.getItem('jwt_token');

  var url = '/api/manageusers';
  if (page) {
    url = '/api/manageusers?page='+page;
  }
  $.ajax({
    url: url,
    method: 'POST',
    headers: {
      Authorization: 'Bearer ' + token
    },
    data: {
      tenant: tenantId
    },
    async:false,
    success: function (response) {
      
      $("#content").html(response.html);
      $('#pagination').html(response.links);
       
        
      
    },
    error: function (xhr) {
      console.error("Error loading users:", xhr.responseText);
      $('#tenantsTable tbody').html(`<tr><td colspan="3">Failed to load data</td></tr>`);
    }
  });

}

$("#logoutButton").on('click', function(){
    localStorage.clear();
    window.location.href = '/login';
});
 