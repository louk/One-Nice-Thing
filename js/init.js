

$.fn.api.settings.api = {
 	'login user': 'login.php',
 	'register user': 'user.php',
  	'edit user' : 'edit.php?id={$id}'
};


var login_validate = {
    fields: {
        name: {
            identifier: 'username',
            rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter your name'
                }
            ]
        },
        password: {
            identifier: 'password',
            rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter a password'
                }
            ]
        }
    },
    inline : true,
    on     : 'blur'
}

var register_validate = {
    fields: {
        first: {
            identifier: 'first',
            rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter your first name'
                }
            ]
        },
        last: {
            identifier: 'last',
            rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter your last name'
                }
            ]
        },
        password: {
            identifier: 'password',
            rules: [
				{
					type   : 'empty',
					prompt : 'Please enter a password'
				},
				{
					type   : 'minLength[6]',
					prompt : 'Your password must be at least {ruleValue} characters'
				}
			]
        },
        email: {
            identifier: 'email',
            rules: [
                {
                    type   : 'empty',
                    prompt : 'Please enter a email'
                },
                {
                	type   : 'email',
                	prompt : 'Please enter a valid e-mail' 
                }
            ]
        }
    },
    inline : true,
    on     : 'blur'
}

          /*  $("#login").submit(function( event ) {
                event.preventDefault();
                var i = $('.ui.checkbox').checkbox('is checked');
                $.post("login.php", {
                    email: $("#email").val(),
                    pass: $("#password").val(),
                    check: i
                }, function (result) {
                    if(result!=0){
                        var url = "index.php?dashboard";
                        window.location.replace(url);  
                    }
                });
            });

            $("#register").submit(function( event ) {
                event.preventDefault();
                var pass1 = $("#pass_new").val();
                var pass2 = $("#pass_re").val();
                if(pass1===pass2){
                    $.post("user.php", {
                        first: $("#first").val(),
                        last: $("#last").val(),
                        email: $("#email_new").val(),
                        pass: $("#pass_new").val(),
                        check: 'register'
                    }, function (result) {
                        if(result==11){
                            var url = "index.php?dashboard";
                            window.location.replace(url);  
                        }else{
                            alert(result);
                        }
                    }); 
                }else{
                    alert("Password mismatch!");
                    return;
                }
            });*/
