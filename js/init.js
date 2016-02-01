
$.fn.api.settings.api = {
 	'login user'       : 'login.php',
 	'register user'    : 'user.php',
    'guest report'     : 'report.php',
    'user report'      : 'report.php',
    'register report'  : 'report.php',
  	'report nicething' : 'add.php'
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
var report_validate = {
    fields:{
        name:{
            identifier: 'username',
            rules: [
            {
                type: 'empty',
                prompt: 'Please enter your name'
            },
            ]
        },
        location:{
            identifier: 'location',
            rules: [
            {
                type: 'empty',
                prompt: 'Please enter your location'
            },
            ]
        },
        content:{
            identifier: 'content',
            rules:[
            {
                type: 'empty',
                prompt: 'Do not blank this field'
            }
            ]
        },
        who:{
            identifier:'who',
            rules:[
            {
                type:'empty',
                prompt:'Do not blank this field',
            }
            ]
        },
        feel:{
            identifier:'feel',
            rules:[
            {
                type:'empty',
                prompt:'Do not blank this field',
            }
            ]
        }
    },
    inline : true,
    on     : 'blur'
}
