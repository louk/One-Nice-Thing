

$.fn.api.settings.api = {
  'login user': 'login.php',
  'register user': 'user.php',
  'report nicething' : 'add.php',
  'forgot password': 'user.php',
  'settings': 'user.php'
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
var forgot_validate = {
    fields:{
        email:{
            identifier:'email',
            rules:[
            {
                type:'empty',
                prompt:'Please enter your email',
            },
            {
                type:'email',
                prompt:'Please enter a valid e-mail'
            }
            ]
        }
    }
}
var settings_validate = {
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
            identifier: 'old_pass',
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
       password2: {
        identifier: 'new_pass',
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
       password3: {
            identifier: 'new_pass2',
            rules: [
            {
               type   : 'empty',
               prompt : 'Please enter a password'
           },
           {
               type   : 'match[new_pass]',
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