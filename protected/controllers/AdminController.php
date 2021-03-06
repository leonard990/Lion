<?php

class AdminController extends Controller {

    public $layout = 'admin_main';

    /**
     * Declares class-based actions.
     */
    public function actions() {
	return array(
	    // captcha action renders the CAPTCHA image displayed on the contact page
	    'captcha' => array(
		'class' => 'CCaptchaAction',
		'backColor' => 0xFFFFFF,
	    ),
	    // page action renders "static" pages stored under 'protected/views/site/pages'
	    // They can be accessed via: index.php?r=site/page&view=FileName
	    'page' => array(
		'class' => 'CViewAction',
	    ),
	);
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
	// renders the view file 'protected/views/site/index.php'
	// using the default layout 'protected/views/layouts/main.php'
	$this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
	if ($error = Yii::app()->errorHandler->error) {
	    if (Yii::app()->request->isAjaxRequest)
		echo $error['message'];
	    else
		$this->render('error', $error);
	}
    }

    /*
      public function accessRules ()
      {
      return array('allow', // allow authenticated user to perform 'create' and 'update' actions
      'actions'=>array('create','update','dynamiccities'),
      'users'=>array('@'),
      );
      }
     */

    public function actionAdminReportsManaging() {
	$model = new AdminReportsManaging;

	if (isset($_GET['report_id'])) {
	    $model->message = date('d.m.Y h:i.s')." Your action concerning report number ".$_GET['report_id'];

	    if ($_GET['type_int'] == 1) { //daily report
		$model->message .= ", daily, ";
		$report = new DailyReports;
		$report = DailyReports::model()->findByAttributes(array('report_id' => $_GET['report_id']));
	    } else if ($_GET['type_int'] == 2) {//monthly report
		$model->message .= ", monthly, ";
		$report = new MonthlyReports;
		$report = MonthlyReports::model()->findByAttributes(array('report_id' => $_GET['report_id']));
	    }

	    if (isset($_GET['sending'])) {
		if ($report->is_sending == '1'){
		    $report->is_sending = '0';
		    $model->message .= "stop sending, ";
		}
		else{
		    $report->is_sending = '1';
		    $model->message .= "start sending, ";
		}
		if ($report->save())
		    $model->message .= " was successfull!";
		else
		    $model->message .= " was NOT successfull!";
	    }
	    else if (isset($_GET['delete'])) {
		if ($report->delete())
		    $model->message .= "was successfully deleted";
		else
		    $model->message .= "was NOT successfully deleted";
	    }
	    else if (isset($_GET['request'])) {
		if ($report->is_active == '1'){
		    $model->message .= "decline process";
		    $report->is_active = '0';
		}
		else{
		    $model->message .= "approve process";
		    $report->is_active = '1';
		}
		if ($report->save())
		    $model->message .= " was successfull!";
		else
		    $model->message .= " was NOT successfull!";
	    }
	}
	//$this->renderPartial('_report_partial_view',array('model'=>$model,));
	unset($_GET['report_id']);
	$this->render('adminReportsManaging', array('model' => $model));
    }

    public function actionAdminGsnPrivileges() {
	$model = new AdminGsnPrivileges;

	/*
	  $model->model_view=new DiGsn('search_privileges');
	  $model->model_view->unsetAttributes();  // clear any default values
	  if(isset($_GET['DiGsn']))
	  $model->model_view->attributes=$_GET['DiGsn'];
	 */

	//if administrator has chosen any privilege we need to provide this request (approve, decline)
	if (isset($_REQUEST['gsn_privilege_id'])) {
	    $model_privilege = new DiGsnPrivileges;

	    $model_privilege = DiGsnPrivileges::model()->findByAttributes(array('gsn_privilege_id' => $_REQUEST['gsn_privilege_id']));
	    //if we find a row that equals this one, we do nothing at all
	    if (($model_privilege) != null) {
		if ($model_privilege->is_active == '1') {
		    $model_privilege->is_active = '0';
		    if ($model_privilege->save())
			$this->redirect(array('admin/adminGsnPrivileges'));
		}
		else {
		    $model_privilege->is_active = '1';
		    if ($model_privilege->save())
			$this->redirect(array('admin/adminGsnPrivileges'));
		}
	    }
	    /* else
	      {
	      DiGsnPrivileges::model()->deleteAllByAttributes(array('gsn_id' => $_REQUEST['gsn_id'], 'user_id' => Yii::app()->user->id));
	      $this->redirect(array('site/gsn'));
	      } */
	}


	$this->render('adminGsnPrivileges', array('model' => $model));
    }

    /**
     * unction for managing SMS notification requests. It is responisble for managing user requests and approving/declining them
     */
    public function actionAdminNotificationRequests() {
	//$model = new AdminSmsNotificationRequests;

	$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];
	$subject = "SMS Notification approval/disapproval";
	$body = "";
	$start = "Notification request deleting process started! Time: " . date('Y-m-d H:i:s');

	//if administrator has chosen any notification request we need to provide this request (approve, decline)
	//if administrator has chosen any notification request we need to provide this request (approve, decline)
	if (isset($_REQUEST['notification_id']) && isset($_REQUEST['type'])) {
	    if ($_REQUEST['type'] != "SMS") {
		$email_notification = new ProdEmailNotifications;

		$email_notification = ProdEmailNotifications::model()->findByAttributes(array('notification_id' => $_REQUEST['notification_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($email_notification) != null) { //if notification request exists proceed
		    if ($email_notification->is_active == '1') { //if the request was declined we need to delete it and make it inactive
			$email_notification->is_active = '0';

			//we first collect info on GSN server of this notification
			$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $email_notification->gsn_id));

			if ($gsn_row != null) {
			    try {
				$sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
				$sftp_obj->connect();

				try {
				    //first we try to delete original notification file
				    if ($sftp_obj->isDir($gsn_row['notification_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_folder']);
				    $sftp_obj->removeFile($email_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Primary notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}

				try {
				    //then we try to delete backup notification file
				    if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";

				    $sftp_obj->chdir($gsn_row['notification_backup_folder']);
				    $sftp_obj->removeFile($email_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}
				//if this was successful we need to inform our administrator about the action
				if ($body == "")
				    $body = "File was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Notification ID: " . $email_notification->nofitication_id;
			    } catch (Exception $e) {
				$body = "File was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nNotification ID: " . $email_notification->notification_id . "\nError message: " . $e->getMessage();
			    }

			    //mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);


			    if (isset($_REQUEST['action'])) {
				if ($email_notification->delete())
				    $body.="\nNotification successfuly deleted!";
				else
				    $body.="\nNotification could not be deleted!";
			    }
			    else
			    if ($email_notification->save()) {
				mail("hyracoidea@gmail.com", $subject, $body, $headers);
				$this->redirect(array('admin/adminNotificationRequests'));
			    }
			}
			else
			    $body = "Something went wrong when acquiring data for the GSN!\nNotification ID: " . $email_notification->notification_id;
		    }
		    else
		    if (isset($_REQUEST['action'])) {
			if ($email_notification->delete())
			    $body.="\nNotification successfuly deleted!";
			else
			    $body.="\nNotification could not be deleted!";
		    }
		    else {

			$email_notification->is_active = '1';

			if ($email_notification->save())
			    $this->redirect(array('admin/adminNotificationRequests'));
		    }
		}
		else
		    $body = "There was no email notification request with Notification ID: " . $_REQUEST['notification_id'];

		mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
	    }
	    else {
		$sms_notification = new ProdSmsNotifications();

		$sms_notification = ProdSmsNotifications::model()->findByAttributes(array('notification_id' => $_REQUEST['notification_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($sms_notification) != null) { //if notification request exists proceed
		    if ($sms_notification->is_active == '1') { //if the request was declined we need to delete it and make it inactive
			$sms_notification->is_active = '0';

			//we first collect info on GSN server of this notification
			$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $sms_notification->gsn_id));

			if ($gsn_row != null) {
			    try {
				$sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
				$sftp_obj->connect();

				try {
				    //first we try to delete original notification file
				    if ($sftp_obj->isDir($gsn_row['notification_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_folder']);
				    $sftp_obj->removeFile($sms_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Primary notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}

				try {
				    //then we try to delete backup notification file
				    if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";

				    $sftp_obj->chdir($gsn_row['notification_backup_folder']);
				    $sftp_obj->removeFile($sms_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}
				//if this was successful we need to inform our administrator about the action
				if ($body == "")
				    $body = $body . "\nFile was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Notification ID: " . $sms_notification->nofitication_id;
			    } catch (Exception $e) {
				$body = $body . "\nFile was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nNotification ID: " . $email_notification->notification_id . "\nError message: " . $e->getMessage();
			    }

			    //mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);

			    if (isset($_REQUEST['action'])) {
				if ($sms_notification->delete())
				    $body.="\nNotification successfuly deleted!";
				else
				    $body.="\nNotification could not be deleted!";
			    }
			    else
			    if ($sms_notification->save())
				$this->redirect(array('admin/adminNotificationRequests'));
			}
			else
			    $body .= "\nSomething went wrong when acquiring data for the GSN!\nNotification ID: " . $sms_notification->notification_id;
		    }
		    else
		    if (isset($_REQUEST['action'])) {
			if ($sms_notification->delete())
			    $body.="\nNotification successfuly deleted!";
			else
			    $body.="\nNotification could not be deleted!";
		    }
		    else {
			$sms_notification->is_active = '1';

			if ($sms_notification->save())
			    $this->redirect(array('admin/adminNotificationRequests'));
		    }
		}
		else
		    $body .= "\nThere was no email notification request with Notification ID: " . $_REQUEST['notification_id'];

		mail("hyracoidea@gmail.com", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
	    }
	}

	$this->render('adminNotificationRequests');
    }

    public function actionAdminWatchdogRequests() {

	$headers = "From: " . Yii::app()->params['adminEmail'] . "\r\nReply-To: " . Yii::app()->params['adminEmail'];

	$subject = "Watchdog approval/disapproval";
	$body = "";

	$start = "Watchdog timer request deleting process started! Time: " . date('Y-m-d H:i:s');

	//if administrator has chosen any notification request we need to provide this request (approve, decline)
	if (isset($_REQUEST['watchdog_id']) && isset($_REQUEST['type'])) {
	    if ($_REQUEST['type'] != "SMS") {

		$email_notification = new ProdEmailWatchdogTimer;

		$email_notification = ProdEmailWatchdogTimer::model()->findByAttributes(array('watchdog_id' => $_REQUEST['watchdog_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($email_notification) != null) { //if notification request exists proceed
		    if ($email_notification->is_active == '1') { //if the request was declined we need to delete it and make it inactive
			$email_notification->is_active = '0';

			//we first collect info on GSN server of this notification
			$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $email_notification->gsn_id));

			if ($gsn_row != null) {
			    try {
				$sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
				$sftp_obj->connect();

				try {
				    //first we try to delete original notification file
				    if ($sftp_obj->isDir($gsn_row['notification_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_folder']);
				    $sftp_obj->removeFile($email_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Primary notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}

				try {
				    //then we try to delete backup notification file
				    if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_backup_folder']);
				    $sftp_obj->removeFile($email_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}
				//if this was successful we need to inform our administrator about the action
				if ($body == "")
				    $body = "File was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Notification ID: " . $email_notification->watchdog_id;
			    } catch (Exception $e) {
				$body = "File was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nNotification ID: " . $email_notification->watchdog_id . "\nError message: " . $e->getMessage();
			    }

			    mail("leonard.beus@fer.hr", $subject, $start . "\n" . $body . "Watchdog request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);


			    if (isset($_REQUEST['action'])) {
				if ($email_notification->delete())
				    $body.="\nWatchdog successfuly deleted!";
				else
				    $body.="\nWatchdog could not be deleted!";
			    }
			    else
			    if ($email_notification->save()) {
				mail("leonard.beus@fer.hr", $subject, $body, $headers);
				$this->redirect(array('admin/adminWatchdogRequests'));
			    }
			}
			else
			    $body = "Something went wrong when acquiring data for the GSN!\nNotification ID: " . $email_notification->watchdog_id;
		    }
		    else
		    if (isset($_REQUEST['action'])) {
			if ($email_notification->delete())
			    $body.="\nWatchdog timer successfuly deleted!";
			else
			    $body.="\nWatchdog timer could not be deleted!";
		    }
		    else {

			$email_notification->is_active = '1';

			if ($email_notification->save())
			    $this->redirect(array('admin/adminWatchdogRequests'));
		    }
		}
		else
		    $body = "There was no email watchdog timer request with WatchDog ID: " . $_REQUEST['watchdog_id'];

		mail("leonard.beus@fer.hr", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
	    }
	    else {

		$sms_notification = new ProdSmsWatchdogTimer();

		$sms_notification = ProdSmsWatchdogTimer::model()->findByAttributes(array('watchdog_id' => $_REQUEST['watchdog_id']));
		//if we find a row that equals this one, we do nothing at all

		if (($sms_notification) != null) { //if notification request exists proceed
		    if ($sms_notification->is_active == '1') { //if the request was declined we need to delete it and make it inactive
			$sms_notification->is_active = '0';

			//we first collect info on GSN server of this notification
			$gsn_row = DiGsn::model()->find(array('select' => 'gsn_name, gsn_ip, notification_folder, notification_backup_folder, sftp_username, sftp_password', 'condition' => 'gsn_id=' . $sms_notification->gsn_id));

			if ($gsn_row != null) {
			    try {
				$sftp_obj = new SftpComponent($gsn_row['gsn_ip'], $gsn_row['sftp_username'], $gsn_row['sftp_password']);
				$sftp_obj->connect();

				try {
				    //first we try to delete original notification file
				    if ($sftp_obj->isDir($gsn_row['notification_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";
				    $sftp_obj->chdir($gsn_row['notification_folder']);
				    $sftp_obj->removeFile($sms_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Primary notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}

				try {
				    //then we try to delete backup notification file
				    if ($sftp_obj->isDir($gsn_row['notification_backup_folder']))
					$body.="\nNotification backup folder exists!\n";
				    else
					$body.="\nNotification backup folder does not exist!\n";

				    $sftp_obj->chdir($gsn_row['notification_backup_folder']);
				    $sftp_obj->removeFile($sms_notification->xml_name . ".xml");
				} catch (Exception $er) {
				    $body = $body . "Backup notification was not succesfully removed. Please check the problem!\nError message " . $er->getMessage() . "\n";
				}
				//if this was successful we need to inform our administrator about the action
				if ($body == "")
				    $body = $body . "\nFile was succesfully removed!\nGSN name: " . $gsn_row['gsn_name'] . "Notification ID: " . $sms_notification->watchdog_id;
			    } catch (Exception $e) {
				$body = $body . "\nFile was NOT successfully removed! Please make a manual check on the problem!\nIt seems that the connection could not be established.\nNotification ID: " . $email_notification->watchdog_id . "\nError message: " . $e->getMessage();
			    }

			    mail("leonard.beus@fer.hr", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);

			    if (isset($_REQUEST['action'])) {
				if ($sms_notification->delete())
				    $body.="\nWatchdog timer successfuly deleted!";
				else
				    $body.="\nWatchdog timer could not be deleted!";
			    }
			    else
			    if ($sms_notification->save())
				$this->redirect(array('admin/adminWatchdogRequests'));
			}
			else
			    $body .= "\nSomething went wrong when acquiring data for the GSN!\nNotification ID: " . $sms_notification->watchdog_id;
		    }
		    else
		    if (isset($_REQUEST['action'])) {
			if ($sms_notification->delete())
			    $body.="\nWatchdog timer successfuly deleted!";
			else
			    $body.="\nWatchdog timer could not be deleted!";
		    }
		    else {
			$sms_notification->is_active = '1';

			if ($sms_notification->save())
			    $this->redirect(array('admin/adminWatchdogRequests'));
		    }
		}
		else
		    $body .= "\nThere was no email watchdog request with Watchdog ID: " . $_REQUEST['watchdog_id'];

		mail("leonard.beus@fer.hr", $subject, $start . "\n" . $body . "Notification request deleting process finished! Time: " . date('Y-m-d H:i:s'), $headers);
	    }
	}

	$this->render('adminWatchdogRequests');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
	Yii::app()->user->logout();
	$this->redirect(Yii::app()->homeUrl);
    }

}
