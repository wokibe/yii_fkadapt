<?php
/* @var $this ObjController */
/* @var $model Obj */

$this->breadcrumbs=array(
	'Objs'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Obj', 'url'=>array('index')),
	array('label'=>'Create Obj', 'url'=>array('create')),
	array('label'=>'View Obj', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Obj', 'url'=>array('admin')),
);
?>

<h1>Update Obj <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>