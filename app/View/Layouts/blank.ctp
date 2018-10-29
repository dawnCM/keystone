<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>keyStone v1.0</title>
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
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->fetch('content'); ?>
    </body>
</html>

