<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
        <link href="http://fonts.googleapis.com/css?family=Oswald:400,700" rel="stylesheet" type="text/css" />
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/default.css" rel="stylesheet" type="text/css" media="all" />
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascript/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascript/jquery.dropotron-1.0.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/protected/javascript/init.js"></script>
        <script language="JavaScript">
            function reload(form){
                var not_type="not_selected";

                var radioButtons = document.getElementsByName('UserNotifications[notification_type]');

                not_type = radioButtons.length;

                for (var x = 0; x < radioButtons.length; x ++) {

                    if (radioButtons[x].type=='radio' && radioButtons[x].checked) {
                        not_type=radioButtons[x].value;
                    }
                }

                self.location = '/lion/index.php/user/userNotifications?notification_type=' + not_type;
            }
        </script>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
	<div id="header-wrapper">
	    <div id="header">
		<div id="logo">
		    <h1><a href="#">Lion</a></h1>
		    <p><?php if (!Yii::app()->user->isGuest) echo "Welcome " . Yii::app()->user->name; ?></p>
		</div>
	    </div>
	</div>
	<div id="menu-wrapper">
	    <div id="menu-content">
		<ul id="menu">
		    <?php if (Yii::app()->user->isGuest) : ?>
			<li <?php if ($this->breadcrumbs[0] == "Home") echo 'class="first"' ?>><?php echo CHtml::link('<span>Home</span>', array('/site/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Contact") echo 'class="first"' ?>><?php echo CHtml::link('<span>Contact</span>', array('/site/contact')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Login") echo 'class="first"' ?>><?php echo CHtml::link('<span>Login</span>', array('/site/login')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Registration") echo 'class="first"' ?>><?php echo CHtml::link('<span>Registration</span>', array('/site/registrationForm')); ?></li>
		    <?php else : ?>
			<li <?php if ($this->breadcrumbs[0] == "Home") echo 'class="first"' ?>><?php echo CHtml::link('<span>Home</span>', array('/site/index')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "GSN") echo 'class="first"' ?>><?php echo CHtml::link('<span>GSN</span>', array('/user/userGsnList')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Sensors") echo 'class="first"' ?>><?php echo CHtml::link('<span>Sensors</span>', array('/user/userSensors')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Notification" || $this->breadcrumbs[0]=='Email notifications') echo 'class="first"' ?>><?php echo CHtml::link('<span>Notifications</span>', array('/user/userNotifications')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Reports</span>', array('/reportSubscription/userReportsMain')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Graphs") echo 'class="first"' ?>><?php echo CHtml::link('<span>Graphs</span>', array('/graphs/graphs')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Contact")echo 'class="first"' ?>><?php echo CHtml::link('<span>Contact</span>', array('/site/contact')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Logout") echo 'class="first"' ?>><?php echo CHtml::link('<span>Logout</span>', array('/site/logout')); ?></li>
			<?php if (Yii::app()->user->group == 1) : ?>
			    <li><?php echo CHtml::link('<span>Admin</span>', array('/admin/index')); ?></li>
			<?php endif; ?>
		    <?php endif; ?>
		</ul>
	    </div>
	</div>
	<div id="banner-wrapper">
	    <div id="banner">
		<div class="image">
		<?php
		    $this->widget('application.extensions.seqimage.seqimage.SeqImage',array(
		    'widthImage' => 900,
		    'heightImage' => 257,
		    'slides'=>array(
			array(
			    'image'=>array('src'=>Yii::app()->request->baseUrl.'/images/presentation_1.jpg'),
			    'link'=>array('url'=>'mypage','htmlOptions'=>array())
			),
			array(
			    'image'=>array('src'=>Yii::app()->request->baseUrl.'/images/presentation_2.jpg'),
			),
			array(
			    'image'=>array('src'=>Yii::app()->request->baseUrl.'/images/pics02.jpg'),
			)
		  )));
		?>
		</div>
<!--		<div class="image"><a href="#"><img src="<?php //echo Yii::app()->request->baseUrl; ?>/images/pics02.jpg" width="900" height="257" alt="" /></a></div>-->
		<div class="border"></div>
	    </div>
	</div>
	<div id="page">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id="content">
		    <?php if (isset($this->breadcrumbs)): ?>
			<?php
			    $this->widget('zii.widgets.CBreadcrumbs', array(
			    'links' => $this->breadcrumbs,
			    ));
			?><!-- breadcrumbs -->
		    <?php endif ?>
		    <?php echo $content; ?>
		</div>

	    <?php if ($this->breadcrumbs[0]=='Notification' || $this->breadcrumbs[0]=='Notification requests') : ?>
	    <div id="sidebar">
		<div>
		    <h2 class="title">Sidemenu</h2>
		    <p>
			<ul>
			    <li <?php if ($this->breadcrumbs[0] == "Notification") echo 'class="first"' ?>><?php echo CHtml::link('<span>New notification</span>', array('/user/userNotifications')); ?></li>
			    <li <?php if ($this->breadcrumbs[0] == "Notification requests") echo 'class="first"' ?>><?php echo CHtml::link('<span>Notification requests</span>', array('/user/userNotificationRequests')); ?></li>
			</ul>
		    </p>
		</div>
	    </div>
	    <?php elseif ($this->breadcrumbs[0]=='New report subscription' || $this->breadcrumbs[0]=='Report subscriptions' || $this->breadcrumbs[0]=='Monthly reports' || $this->breadcrumbs[0]=='Daily reports' || $this->breadcrumbs[0]=='Reports') :?>
	    <div id="sidebar">
		<div>
		    <h2 class="title">Sidemenu</h2>
		    <p>
			<li <?php if ($this->breadcrumbs[0] == "Reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Reports</span>', array('/reportSubscription/userReportsMain')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Daily reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Daily reports</span>', array('/reportSubscription/userReportsDaily')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Monthly reports") echo 'class="first"' ?>><?php echo CHtml::link('<span>Monthly reports</span>', array('/reportSubscription/userReportsMonthly')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "New report subscription") echo 'class="first"' ?>><?php echo CHtml::link('<span>New report subscription</span>', array('/reportSubscription/userReportsNewSubscription')); ?></li>
			<li <?php if ($this->breadcrumbs[0] == "Report subscriptions") echo 'class="first"' ?>><?php echo CHtml::link('<span>Report subscriptions</span>', array('/reportSubscription/userReportsSubscription')); ?></li>
		    </p>
		</div>
		<div>
		    <h2 class="title">Lion team</h2>
		    <p>Lion was developed by students<br/><br/>Matija Renić<br/>Luka Postružin<br/><br/>under supervision of dr.sc.Mario Žagar</p>
		</div>
	    </div>
	    <?php elseif ($this->breadcrumbs[0]=='Sensors') :?>
		<div id="sidebar">
		<div style="vertical-align:middle">
		    <h2 class="title">Sensor map</h2>
		<?php
		    Yii::import('application.extensions.EGMap.*');

		    $gMap = new EGMap();
		    $gMap->setHeight(600);
		    $gMap->setWidth(298);

		    $gMap->zoom = 7;
		    $mapTypeControlOptions = array(
			'position' => EGMapControlPosition::LEFT_BOTTOM,
			'style' => EGMap::MAPTYPECONTROL_STYLE_DROPDOWN_MENU
		    );

		    $gMap->mapTypeControlOptions = $mapTypeControlOptions;
	    //centar je zagreb
		    $gMap->setCenter(45.817, 15.983);

		    $all_user_sensors = Yii::app()->db->createCommand()
				    ->selectDistinct('s.*')
				    ->from('di_sensors s')
				    ->join('di_gsn_privileges p', 'p.gsn_id=s.gsn_id')
				    ->where('p.user_id=:id', array(':id' => Yii::app()->user->id))
				    ->queryAll();

		    foreach ($all_user_sensors as $sensor) {
			$last_measured_data = Yii::app()->db->createCommand(
			    'SELECT 
				u.unit_name
			     ,	r.value
			     ,	r.time_of_the_reading
			     FROM
			     (
			     SELECT z.*, row_number() over (partition by z.sensor_id, z.unit_id order by time_of_the_reading desc) as rang
			     (
				SELECT *
				FROM l_readings
				UNION ALL f_readings
			      ) z
			      ) r
			      JOIN di_sensors s ON r.sensor_id = s.sensor_id
			      JOIN di_units u ON r.unit_id = u.unit_id
			      WHERE 
				1 = 1
			      and r.rang = 1
			      and s.sensor_id = '.$sensor['sensor_id']
			)->queryAll();
			$data = "";
			foreach ($last_measured_data as $measured_data){
			    $data .= "<br/>".$measured_data['unit_name'].": ".$measured_data['value'].", at ".$measured_data['time_of_the_reading'];
			}

			$info_window_a = new EGMapInfoWindow('<div>Sensor name ' . $sensor['sensor_user_name'] .$data .'</div>');
			// Create marker for every sensor user can see
			$icon = new EGMapMarkerImage("http://mapicons.nicolasmollet.com/wp-content/uploads/mapicons/shape-default/color-128e4d/shapecolor-dark/shadow-1/border-white/symbolstyle-white/symbolshadowstyle-no/gradient-no/water.png");

			$icon->setSize(32, 37);
			$icon->setAnchor(16, 16.5);
			$icon->setOrigin(0, 0);

			$marker = new EGMapMarker($sensor['location_y'], $sensor['location_x'], array('title' => "Sensor " . $sensor['sensor_user_name'], 'icon' => $icon));

			$marker->addHtmlInfoWindow($info_window_a);
			$gMap->addMarker($marker);
		    }

	    // enabling marker clusterer just for fun
	    // to view it zoom-out the map
	    //$gMap->enableMarkerClusterer(new EGMapMarkerClusterer());

		    $gMap->renderMap();
		?>
		</div>
	    </div>
		<?php else: ?>
	    <div id="sidebar">
		<div>
		    <h2 class="title">About Lion</h2>
		    <p>Lion is a project developed under Faculty of Electrical Engineering and Computing. It collects data from all around the world and provides user interface for managing all data sources and actions.</p>
		</div>
		<div>
		    <h2 class="title">Lion team</h2>
		    <p>Lion was developed by students<br/><br/>Matija Renić<br/>Luka Postružin<br/><br/>under supervision of dr.sc.Mario Žagar</p>
		</div>
	    </div>
	    <?php endif;?>
	</div>
	<div class="bgbtm"></div>
	</div>
	<div id="footer-content">
	    <div class="bgtop"></div>
	    <div class="content-bg">
		<div id="column1">
		    <div class="box1">
			<h2>What is GSN?</h2>
			<p>GSN stands for Global sensor networks project. Basically it is a web server that collects various data from multiple sources.</p>
		    </div>
		    <div class="box2">
			<h2>What is RASIP?</h2>
			<p><a href ="http://www.fer.unizg.hr/rasip/onama">RASIP</a> is a department on FER. It holds many open source courses and  a course about distributed software development.</p>
		    </div>
		</div>
		<div id="column2">
		    <div class="box3">
			<h2>What is FER?</h2>
			<p><a href ="http://www.fer.unizg.hr">FER</a> is a faculty from Zagreb, Croatia. It is a part of University of Zagreb.</p>
		    </div>
		</div>
	    </div>
	    <div class="bgbtm"></div>
	</div>
        <div id="footer">
            <p>Copyright &copy; <?php echo date('Y'); ?> by FER.</p>
            <p>All Rights Reserved.</p>
            <p>Matija Renić, Luka Postružin.</p>
        </div><!-- footer -->
    </body>
</html>