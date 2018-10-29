<?php
/**
 * keyStone - Application dashboard.
 *
 * Licensed under GNU General Public License v.2
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     TBD
 * @link          TBD
 * @package       app.View.Layouts
 * @since         keyStone v1.0 
 * @license       TBD
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>keyStone v1.0 - Login</title>
        <link href="/css/style.default.css" rel="stylesheet">
        <link href="/css/jquery.gritter.css" rel="stylesheet">
        <?php 
        echo $this->Html->script(array(
		'/js/jquery/jquery-1.11.1.min.js',
		'/js/jquery/jquery-migrate-1.2.1.min.js',
		'/js/jquery/jquery.cookies.js',
		'/js/jquery/jquery.validate.min.js',
		'/js/jquery/jquery.gritter.min.js',
		'/js/bootstrap/bootstrap.min.js',
		'/js/modernizr.min.js',
		'/js/pace.min.js',
		'/js/retina.min.js',
		'/js/custom.js',
		'/js/login/functions.js'
		));
        ?>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>
     <body class="signin">
        <section>     
            <div class="panel panel-signin">
                <div class="panel-body">
                    <div class="logo text-center">
                        <img src="/images/logo-primary.png" alt="keyStone Logo" width="102" height="22">
                    </div>
                    <br />
                    <h4 id="title-text" class="text-center mb5">Sign in to your account</h4>
                    
                    <div id="description-text" class="mb30"></div>
                    
                    <form action="/users/login" id="UserLoginForm" method="post" accept-charset="utf-8" novalidate="novalidate">
                    <div style="display:none;"><input type="hidden" name="_method" value="POST"></div>
                        <div class="input-group mb15">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input type="email" class="form-control" placeholder="Email" name="data[User][email]" id="UserEmail" required>
                        </div><!-- input-group -->
                        <div class="loginfields input-group mb15">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" placeholder="Password" name="data[User][password]" id="UserPassword" required>
                        </div><!-- input-group -->
                        
                        <div class="clearfix">
                            <div class="pull-left">
                                <div class="ckbox ckbox-primary mt10">
                                	<input type="checkbox" id="lostpw" value="1">
                                	<label for="lostpw">Reset Password</label>
                                </div>
                            </div>
                            <div class="pull-right">
                                <button id="loginsubmitbt" type="submit" class="btn btn-success"><span>Sign In </span><i class="fa fa-angle-right ml5"></i></button>
                            </div>
                        </div>                      
                    </form>
                    
                </div><!-- panel-body -->
                <div class="panel-footer">
                </div><!-- panel-footer -->
            </div><!-- panel -->
            
        </section>
    </body>
<?php echo $this->Session->flash(); ?>
<?php //echo $this->element('sql_dump'); ?>
</html>