<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'di-gsn-privileges-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'date_id_given'); ?>
		<?php echo $form->textField($model,'date_id_given'); ?>
		<?php echo $form->error($model,'date_id_given'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'gsn_id'); ?>
		<?php echo $form->textField($model,'gsn_id'); ?>
		<?php echo $form->error($model,'gsn_id'); ?>
	</div>
<?php /*
	<div class="row">
		<?php echo $form->labelEx($model,'time_privilege_given'); ?>
		<?php echo $form->textField($model,'time_privilege_given'); ?>
		<?php echo $form->error($model,'time_privilege_given'); ?>
	</div>
	*/
	?>

	<div class="row">
		<?php echo $form->labelEx($model,'is_active'); ?>
		<?php echo $form->textField($model,'is_active',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'is_active'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->