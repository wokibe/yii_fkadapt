<?php

/**
 * This is the model class for table "obj".
 *
 * The followings are the available columns in table 'obj':
 * @property integer $id
 * @property string $name
 * @property string $info
 * @property integer $loc_id
 *
 * The followings are the available model relations:
 * @property Loc $loc
 */
class Obj extends CActiveRecord
{
  public $loc_search;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'obj';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, loc_id', 'required'),
			array('loc_id', 'numerical', 'integerOnly'=>true),
			array('info', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, info, loc_search', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'loc' => array(self::BELONGS_TO, 'Loc', 'loc_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'info' => 'Info',
			'loc_id' => 'Loc',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		
		$criteria->with = array('loc');

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.info',$this->info,true);
		$criteria->compare('loc.name',$this->loc_search, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
			  'attributes'=>array(
			    'loc_search'=>array(
			      'asc'=>'loc.name',
			      'desc'=>'loc.name DESC',
			    ),
			    '*',
			  ),
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Obj the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
