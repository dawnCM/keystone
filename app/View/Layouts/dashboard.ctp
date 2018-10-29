<?php
/**
 * keyStone - CakePHP based application dashboard.
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

        <title>keyStone v1.0 - Dashboard</title>
        <?php 
        echo $this->Html->css('jquery.gritter');
		echo $this->Html->css('style.default');
		?>

        <?php 
        echo $this->Html->script(array(
		'/js/jquery/jquery-1.11.1.min.js',
		'/js/jquery/jquery-migrate-1.2.1.min.js',
		'/js/jquery/jquery.cookies.js',		
        '/js/jquery/jquery.gritter.min.js',
		'/js/bootstrap/bootstrap.min.js',
		'/js/modernizr.min.js',
		'/js/pace.min.js',
		'/js/retina.min.js',
		'/js/custom.js',
		));

		if($loadDashboardJS) {
			echo $this->Html->script(
					array(	'/js/flot/jquery.flot.min.js',
							'/js/flot/jquery.flot.resize.min.js',
							'/js/flot/jquery.flot.spline.min.js',
							'/js/morris.min.js',
							'/js/moment.min.js',
							'/js/dashboard/functions.js',
							'/js/raphael-2.1.0.min.js'
			));
			echo $this->Html->css('morris');
		}
		
		if($loadBucketsJS) {
			echo $this->Html->script(
				array(	'/js/jquery/jquery.dataTables.min.js',
						'/js/jquery/jquery.dataTables.responsive.min.js',
						'/js/jquery/jquery.sparkline.min.js',
						'/js/jquery/jquery.validate.min.js',
						'/js/morris.min.js',
						'/js/raphael-2.1.0.min.js',
						'/js/bootstrap/bootstrap.dataTables.js',
						'/js/select2.min.js',
						'/js/moment.min.js',
						'/js/bucket/functions.js'));			
				echo $this->Html->css('select2');
				echo $this->Html->css('dataTables.bootstrap');
				echo $this->Html->css('morris');
		}
		
		if($loadAffiliatesJS){
			echo $this->Html->script(
				array(	'/js/jquery/jquery.dataTables.min.js',
						'/js/jquery/jquery.dataTables.responsive.min.js',
						'/js/jquery/jquery.sparkline.min.js',
						'/js/jquery/jquery.validate.min.js',
						'/js/jquery/jquery-ui-1.10.3.min.js',
						'/js/morris.min.js',
						'/js/raphael-2.1.0.min.js',
						'/js/bootstrap/bootstrap.dataTables.js',
						'/js/select2.min.js',
						'/js/moment.min.js',
						'/js/affiliate/functions.js'));
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('morris');
		}
				
		if($loadUsersJS) {
			echo $this->Html->script(
				array(	'/js/jquery/jquery.dataTables.min.js',
						'/js/jquery/jquery.dataTables.responsive.min.js',
						'/js/jquery/jquery.validate.min.js',						
						'/js/bootstrap/bootstrap.dataTables.js',
						'/js/select2.min.js',
						'/js/dropzone.min.js',
						'/js/users/functions.js'));
		
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('dropzone');
		}
		
		if($loadCpfJS){
			echo $this->Html->script(
					array(	'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/select2.min.js',
							'/js/dropzone.min.js',
							'/js/cpf/functions.js'));
			
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('dropzone');
		}
		
		if($loadReportsJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/highcharts/highcharts.js',
							'/js/moment.min.js',
							'/js/morris.min.js',
							'/js/raphael-2.1.0.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/reports/functions.js'));
			
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('morris');
		}
		
		if($loadLeadsJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/highcharts/highcharts.js',
							'/js/moment.min.js',
							'/js/morris.min.js',
							'/js/raphael-2.1.0.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/lead/functions.js'));
				
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('morris');
		}
		
		if($loadBillingJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/moment.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/billing/functions.js'));
				
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
		}
		if($loadToolsJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/highcharts/highcharts.js',
							'/js/moment.min.js',
							'/js/morris.min.js',
							'/js/raphael-2.1.0.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/tools/functions.js'));
		
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('morris');
		}
		if($loadPingtreeJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/moment.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/pingtree/functions.js'));
				
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
			echo $this->Html->css('pingtreerank');
		}
		
		if($loadFraudJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/moment.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/fraud/functions.js'));
		
			echo $this->Html->css('select2');
			echo $this->Html->css('dataTables.bootstrap');
		}
		
		if($loadSiteJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/moment.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/sites/functions.js',
							'/js/bootstrap-switch.js'));
		
					echo $this->Html->css('select2');
					echo $this->Html->css('dataTables.bootstrap');
					echo $this->Html->css('bootstrap-switch.css');
		}

		if($loadListmanagementJS) {
			echo $this->Html->script(
					array(	'/js/jquery/jquery-ui-1.11.min.js',
							'/js/jquery/jquery.dataTables.min.js',
							'/js/jquery/jquery.dataTables.responsive.min.js',
							'/js/jquery/jquery.validate.min.js',
							'/js/bootstrap/bootstrap.dataTables.js',
							'/js/moment.min.js',
							'/js/jquery/jquery.maskedinput.min.js',
							'/js/select2.min.js',
							'/js/listmanagement/functions.js',
							'/js/listmanagement/espmanage_functions.js',
							'/js/listmanagement/api_functions.js',
							'/js/listmanagement/blacklist_functions.js',
							'/js/listmanagement/esplist_functions.js',
							'/js/listmanagement/espoffer_functions.js'));						
					echo $this->Html->css('select2');
					echo $this->Html->css('dataTables.bootstrap');
					echo $this->Html->css('toggles');
		}

		
        ?>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript">var nonce = '<?php echo Configure::read('Ajax.nonce'); ?>';</script>
    </head>

    <body>
        
        <header>
            <div class="headerwrapper">
                <div class="header-left">
                    <a href="/dashboard" class="logo">
                        <img src="/images/logo.png" alt="" /> 
                    </a>
                    <div class="pull-right">
                        <a href="" class="menu-collapse">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                </div><!-- header-left -->
                
                <div class="header-right">
                    
                    <div class="pull-right">
                        <form class="form form-search" action="/leads/detail/" method="post">
                            <input type="search" class="form-control" placeholder="Lead Search" name="search" id="track_id_search">
                        </form>
                        <div class="btn-group btn-group-list btn-group-notification">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              <i class="fa fa-bell-o"></i>
                              <span class="badge">5</span>
                            </button>
                        </div><!-- btn-group -->
                        
                        <div class="btn-group btn-group-option">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                              <li><a href="/users/activity"><i class="glyphicon glyphicon-star"></i> Activity Log</a></li>
                              <?php 
                              if($this->Session->read('Settings.showtest') == '1'){
                              	echo '<li><a><span id="testoff" class="text-success pointer"><i class="glyphicon glyphicon-random"></i> Hide Test Activity</span></a></li>';
                              }else{
                              	echo '<li><a><span id="teston" class="text-danger pointer"><i class="glyphicon glyphicon-random"></i> Show Test Activity</span></a></li>';
                              }
                              ?>
                              <li class="divider"></li>
                              <li><a href="/users/logout"><i class="glyphicon glyphicon-log-out"></i>Sign Out</a></li>
                            </ul>
                        </div><!-- btn-group -->
                        
                    </div><!-- pull-right -->
                    
                </div><!-- header-right -->
                
            </div><!-- headerwrapper -->
        </header>
        <section>
            <div class="mainwrapper">
                <div class="leftpanel">
                    <div class="media profile-left">
                        <a class="pull-left profile-thumb">
                            <img class="img-circle" src="/files/users/<?php echo md5($this->Session->read('Auth.User.id')); ?>.jpg" alt="">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo ucfirst($this->Session->read('Auth.User.full_name'))?></h4>
                            <small class="text-muted"><?php echo ucfirst($this->Session->read('Auth.User.title'))?></small>
                        </div>
                    </div><!-- media -->
                	<?php echo $this->element('navigation_left'); ?>
                </div><!-- leftpanel -->
                <?php echo $this->fetch('content'); ?>
            </div><!-- mainwrapper -->
        </section>
    </body>
</html>