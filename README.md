## Yii: Better Foreign Key Adaption

### A Cookbook

  For a little project I needed a web application with some CRUD (Create, Read, Update, Delete) support for a small database with foreign keys (seems, that I have some old fashioned habits). With Google I found Yii, which claims to have these properties.  
  
  In principle it worked, and Yii provided also for each table a management screen, which allows sorting and filtering of each field of a table. But I learned, that I have to modify the generated code to get the features I wanted: instead of the index number of the auto-increment key I wanted to see the corresponding name from the relating table.
  
  This writeup will summarize the steps I had to perform (as a cookbook about the necessary actions I found in the web).
  
### Setup

  I learned a lot about Yii from: "The Yii Book" by Larry Ullman.

  I am on a Mac 10.9.4 (Mavericks) and use SQLite 3.7.13 and use Apache 2.2.26 as Webserver. I downloaded the Yii framework from http://www.yiiframework.com/download/ (actual yii-1.1.15.022a51.tar.gz) and expanded it.
  
  For the Yii applications and the framework I generated in the Apache webroot (on this installation /Library/WebServer/Documents) a directory called yii and set the permission for me (the developer). Then I moved the downloaded Yii code in this location and generated a symbolic link it (as recommended by Larry Ullman).
  
  Now I am ready to generate the skeleton of the planned "fkadapt" application. 
  
    iMac:yii kittekat$ pwd
    /Library/WebServer/Documents/yii
    iMac:yii kittekat$ php yii/framework/yiic webapp fkadapt git
    ...
    iMac:yii kittekat$ ls -l
    drwxr-xr-x   9 kittekat  wheel  306 10 Aug 19:02 fkadapt
    lrwxr-xr-x   1 kittekat  wheel   18 26 Jul 21:57 yii -> yii-1.1.15.022a51/
    drwxr-xr-x@ 10 kittekat  wheel  340 22 Jul 22:45 yii-1.1.15.022a51


  The first approximation of the application is ready to be tested. Enter in your browser "localhost/yii/fkadapt"!!!
  
### Initial Customizations

  There are several files in the fkadapt/protected directories, which can be customized for our installation.

#### Create Database
 
  Use your favorite approach to generate the database in fkadapt/protected/data. I used SQLiteStudio (v2.1.5).
  
    -- Table: loc
    CREATE TABLE loc ( 
        id   INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT    NOT NULL
                     UNIQUE,
        info TEXT 
    );


    -- Table: obj
    CREATE TABLE obj ( 
        id     INTEGER PRIMARY KEY AUTOINCREMENT,
        name   TEXT    NOT NULL
                       UNIQUE,
        info   TEXT,
        loc_id INTEGER NOT NULL
                       REFERENCES loc ( id ) 
    );
    
This construction (an auto-increment primary key index and an unique name for that entry) allows a later change of the name (which was a requirement of my project) without compromising all foreign key relations, which are based on the index.  And it allows less space requirements in the referencing tables. But, as we will see later, generates additional challenges!

#### Adapting "protected/config/main.php"

1. Change the name of this application: at the beginning of the config/main.php file replace the 'My Web Application' name for instance by

  	'name'=>'Better Foreign Key Adaption',
  	
2. Uncomment the 'gii'=>array and select a suitable password for the Gii tool.
  	
3. Uncomment the 'urlManager'=>array

4. Adapt the database connections string:

		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/fkadapt.db',
		),

#### Change some protected/ permissions

  The application and the developer will need to write to the database and some other directories. To avoid the usage of "chmod 777" I propose the following adaptions:
  
1. cd to protected/

2. "sudo chgrp _www" for controllers/, data/, models/, runtime/ and views/.

3. "sudo chmod 775" for controllers/, data/, models/, runtime/ and views/.

4. cd to data/

5. "sudo chgrp _www fkadapt.db"

6. "sudo chmod 775 fkadapt.db"

### Generating CRUD Code with Gii

* Login to Gii with "localhost/yii/fkadapt/index.php/gii"

* Enter the Gii password

* Select "Model Generator", 
  * enter "*" for Table Name 
  * press Preview
  * press Create
  
* Select "Crud Generator"
  * enter "Loc" for Model Class
  * press Preview
  * press Create

  * enter "Obj" for Model Class
  * press Preview
  * press Create

### Add CRUD Entries to Menu

  Insert new entries in the mainmenu widgets items array in the file "protected/views/layout/main.php". After
  
	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),

insert the following two lines

				array('label'=>'Loc', 'url'=>array('loc/index')),
				array('label'=>'Obj', 'url'=>array('obj/index')),

### Test the CRUD

*  Enter the Application with "localhost/yii/fkadapt/index.php"

*  Select "Login" in the menu, use admin/admin

*  Select "Loc" in the menu to create some locations

*  Select "Obj" in the menu to create some objects, which reference to locs

### Observing the Challenge

   You can clearly see the deficiency of the foreign key support in the Gii generated "Obj" code: you can only enter/view the index of the location and not the associated name from the related entry as it is expected with an auto-increment primary key.
   
### Improving the Foreign Key Support

We have to modify the Gii created code for the "obj" table on several locations.
 
#### Find the Relation Name

First we have a look in the model file of the table, which contains the foreign key. In our example this is "protected/models/Obj.php". There we search for the "relations" function. In this function there is a line which contains the string "BELONGS_TO". The key at the begin of this line is the needed "relation name":

    'loc' => array(self::BELONGS_TO, 'Loc', 'loc_id'),
    
#### Adapt the List Page Screen

In the file "protected/views/obj/_view.php" we replace the line

	  <?php echo CHtml::encode($data->loc_id); ?>
	  
by the following line, where we use the above located relation name and the name of the field of the table, which should be used instead of the index number:

  	<?php echo CHtml::encode($data->loc->name); ?>
  	
Test the change by selecting "Obj" in the main menu. The lines with "Loc:" show now the name!

#### Adapt the List Single Record Screen

In the file "protected/views/obj/view.php" we replace the line with 'loc_id' in the 'zii.widgets.CDetailView' by an array, which defines adapted name (the old field name) and value ($data with the relation construct as just above) infos:

    'attributes'=>array(
      'id',
      'name',
      'info',
      array (
        'name'  => 'loc_id',
        'value' => $model->loc->name,
      ),
    ),

Test the change by selecting an "id" in the "Objs" list page screen. The line with "Loc" display also shows now the name!

#### Adapt the Create/Update Screen

In the file "protected/views/obj/\_form.php" we replace the line with "$form->textField($model,'loc\_id')" by the definition of a dropdown list, which gets filled with all available, sorted foreign keys:
 
 		<?php echo $form->dropDownList($model,'loc_id',
		  CHtml::ListData(Loc::model()->findAll(array('order'=>'name')),
		  'id', 'name')); ?>
		
Test the change by creating or updating an obj! 

#### Adapt the Manage Screen

This step was the hardest. Google for "searching and sorting by related model in cgridview" and read the article from 

    http://www.yiiframework.com/wiki/281/
      
First we add a new search attribute "$loc_search" to the class Obj:
  
    class Obj extends CActiveRecord
    {
      public $loc_search;
      
Then we replace in the rules() function the loc\_id by loc\_search:

    public function rules()
    {
      return array(
        ...
        array('id, name, info, loc_search', 'safe', 'on'=>'search'),
      );
    }

And we modify the search() function:

    public function search()
    {
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
    
Finally we adapt the "protected/views/obj/admin.php" file. Replace the the grid call the line with "loc_id" with an array:

    <?php $this->widget('zii.widgets.grid.CGridView', array(
      'id'=>'obj-grid',
      'dataProvider'=>$model->search(),
      'filter'=>$model,
      'columns'=>array(
        'id',
        'name',
        'info',
        array(
          'name'=>'loc_search',
          'header'=>'Location',
          'value'=>'$data->loc->name',
        ),
        array(
          'class'=>'CButtonColumn',
        ),
      ),
    )); ?>

Now you can test the full search and filter possibilities of the management screen, including the location column!
 