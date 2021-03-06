<?php

class LReadingsController extends ERestController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function _filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function _accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new LReadings;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LReadings']))
		{
			$model->attributes=$_POST['LReadings'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->reading_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LReadings']))
		{
			$model->attributes=$_POST['LReadings'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->reading_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('LReadings');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new LReadings('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['LReadings']))
			$model->attributes=$_GET['LReadings'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=LReadings::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='lreadings-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function doRestList()
	{
		$criteria = array(
			'order'=>'time_id',
		);

		$readings = LReadings::model()->findAll($criteria );

		$this->renderJson(array(
			'success'=>true,
			'message'=>'Records Retrieved Successfully',
			'data'=>$readings
		));

	}

	public function doCustomRestPostSearch($data)
	{

		$startTime = $data['startTime'];
		$endTime = $data['endTime'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$sameDate = ') OR (';
		if ( $startDate == $endDate ) $sameDate = ') AND (';


		$condition = '((time_id >= ' . $startTime . ' AND ';
		$condition .= 'date_id = ' . $startDate . $sameDate;
		$condition .= 'time_id <= ' . $endTime . ' AND ';
		$condition .= 'date_id = ' . $endDate . ') OR ';
		$condition .= '(date_id > '. $startDate.' AND date_id < '. $endDate .')) AND ';
		$condition .= '(';


		$unitcount = count($data['units']);

		foreach ( $data['units'] as $key => $value ) {

			$condition .= '(unit_id = ' . $value['unit_id'] . ' AND ';
			$condition .= 'sensor_id = ' . $value['sensor_id'] . ')';

			if ( $key == $unitcount - 1 ) {
				$condition .= ')';
			} else {
				$condition .= ' OR ';
			}

		}

		$criteria = new CDbCriteria(array(
			'select' => 'gsn_id, sensor_id, unit_id, date_id, time_id, value, reading_id',
			'distinct' => true,
			'order' => 'date_id,time_id',
			'condition' => $condition
		));


		$lreadings = LReadings::model()->findAll($criteria);
		$freadings = FReadings::model()->findAll($criteria);
		$outdata = array_merge( $freadings, $lreadings );


		$datacount = count($outdata);


		// get only 100 points
		/*
		if ( $datacount > 150 ) {
			$temp = array();
			for ($i = 0; $i < $datacount - $unitcount; $i += ceil($datacount / 120)) {
				for ($j = 0; $j < $unitcount; $j++) {
					$temp[] = $outdata[$i+$j];
				}
			}
			$outdata = $temp;
		}
		*/

		$this->renderJson(array(
			'success' => true,
			'message' => 'Records Retrieved Successfully',
			'test' => $condition,
			'data' => $outdata,
		));

	}

}
