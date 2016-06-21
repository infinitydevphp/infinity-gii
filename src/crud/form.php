<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator \infinitydevphp\gii\crud\Generator */
use infinitydevphp\gii\assets\CrudGeneratorAssets;
use kartik\form\ActiveForm;
use infinitydevphp\gii\models\WidgetsCrud;
use infinitydevphp\MultipleModelValidator\widgets\MultipleInput;
use kartik\widgets\Select2;

CrudGeneratorAssets::register($this);

$form = ActiveForm::begin();

echo $form->field($generator, 'modelClass')->textInput([
    'id' => 'model-class'
]);
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');
//echo $form->field($generator, 'moduleID');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);

echo $form->field($generator, 'columns')
    ->widget(MultipleInput::className(), [
        'attributeOptions' => [
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
            'validateOnChange' => true,
            'validateOnSubmit' => true,
            'validateOnBlur' => true,
        ],
        'data' => $generator->columns,
        'baseModel' => WidgetsCrud::className(),
        'columns' => [
            [
                'name' => 'fieldName',
                'enableError' => true,
                'title' => 'Length'
            ],
            [
                'name' => 'widgetType',
                'type' => Select2::className(),
                'options' => [
                    'data' => $generator->getWidgets(),
                ],
                'title' => 'Widget Type'
            ],
        ],
    ]);

echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
$form->end();