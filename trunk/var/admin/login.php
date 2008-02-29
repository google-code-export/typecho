<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<head>
<title><?php _e('登录到%s', $options->title); ?></title>
<link rel="stylesheet" type="text/css" href="var/blueprint/screen.css" />
<!--[if IE]><link rel="stylesheet" href="var/blueprint/lib/ie.css" type="text/css" media="screen, projection"><![endif]-->
<link rel="stylesheet" href="var/blueprint/plugins/buttons/buttons.css" type="text/css" media="screen, projection">
<script src="var/javascript/jquery.js" language="javascript" type="text/javascript"></script>
<style>
    .banner
    {
        background:#292D34;
        height:70px;
    }
</style>
</head>
<body>
<div class="container">
<div class="column span-15">d</div>
<div class="column span-9 last">
    <form method="post">
    <fieldset>
        <legend><?php _e('登录到%s', $options->title); ?></legend>
    
        <p>
            <label for="user_name"><?php _e('用户名'); ?></label><br/>
            <input type="text" class="title" id="user_name" name="user_name" />
        </p>
        
        <p>
            <label for="user_password"><?php _e('密码'); ?></label><br/>
            <input type="password" class="title" id="user_password" name="user_password" />
        </p>
        
        <p>
            <button type="submit" class="button positive">
                <img src="var/blueprint/plugins/buttons/icons/tick.png" alt=""> <?php _e('登录'); ?>
            </button>
        </p>
    </fieldset>
    </form>
</div>
</div>
<script>
    $('input.title').focus(
        function()
        {
            $(this).addClass('focus');
        }
    );
    
    $('input.title').blur(
        function()
        {
            $(this).removeClass('focus');
        }
    );
</script>
</body>
</html>
