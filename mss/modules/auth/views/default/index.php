<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
   'homeLink'=>CHtml::link('首页',Yii::app()->homeUrl),
    //这里可以修改HOME,变成中文
    'links'=>$this->breadcrumbs,
)); ?><!-- breadcrumbs -->  
