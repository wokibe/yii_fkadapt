<?php
/* @var $this ObjController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Objs',
);

$this->menu=array(
	array('label'=>'Create Obj', 'url'=>array('create')),
	array('label'=>'Manage Obj', 'url'=>array('admin')),
);
?>

<h1>Objs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
