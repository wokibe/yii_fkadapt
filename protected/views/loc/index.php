<?php
/* @var $this LocController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Locs',
);

$this->menu=array(
	array('label'=>'Create Loc', 'url'=>array('create')),
	array('label'=>'Manage Loc', 'url'=>array('admin')),
);
?>

<h1>Locs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
