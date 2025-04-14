$("#loginButton").on('click', function(e){
    e.preventDefault();

        $.ajax({
            url: 'api/login',
            type: 'POST',
            data: {
                email: $('#email').val(),
                password: $('#password').val(),
                _token: '{{ csrf_token() }}' // CSRF protection
            },
            success: function(response) {
                if (response.status== true) {
                    localStorage.setItem('jwt_token', response.token);
                    localStorage.setItem('role', response.role);
                    localStorage.setItem('username', response.name);
                    localStorage.setItem('schema', response.schema);
                    var prefix = (response.prefix == 'admin') ? 'admin' : response.prefix;
                    
                    window.location.href = prefix;
                } else {
                    $("#message").html(response.message);
                }

                
            },
            error: function(xhr) {
                $('#message').text(xhr.responseJSON?.message || 'Login failed');
            }
        });
});
 