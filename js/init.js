

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

