<?php
/* @var $this ObjController */
/* @var $model Obj */

$this->breadcrumbs=array(
	'Objs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Obj', 'url'=>array('index')),
	array('label'=>'Manage Obj', 'url'=>array('admin')),
);
?>

<h1>Create Obj</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>