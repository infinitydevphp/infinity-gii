<style>
    form > div.row > div.col-lg-8 {
        width: 100%;
    }
</style>
<?php
use yii\gii\generators\model\Generator;
use kartik\builder\Form;
use insolita\wgadminlte\Box;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator \infinitydevphp\gii\model\Generator */
\infinitydevphp\gii\assets\ModelGeneratorAssets::register($this);
\dmstr\web\AdminLteAsset::register($this);

if ($generator->createForm) {
    $form = \kartik\widgets\ActiveForm::begin();
}

echo $form->errorSummary($generator);
echo $form->errorSummary($generator->tableBuilder);
foreach ($generator->behaviorModels as $field) {
    echo $form->errorSummary($field);
}
//echo $form->errorSummary($generator->tableBuilder->fields);

use infinitydevphp\gii\Helper;

?>
<div class="block-hide">
    <?php
    echo $form->field($generator, 'createTable')->checkbox([
        'name' => Helper\getName('createTable', $generator),
        'class' => 'checkbox-toggle'
    ]);
    ?>
    <div class="hideInBlock">
        <?php
            echo $this->renderFile(__DIR__ . "/../table/form.php", [
                'addition' => preg_replace('/^Generator/', '', $generator->additionName . '[tableBuilder]'),
                'generator' => $generator->tableBuilder,
                'form' => $form
            ]);
        ?>
    </div>
    <div class="showInBlock">
        <?php echo $form->field($generator, 'tableName')->textInput([
            'table_prefix' => $generator->getTablePrefix(),
            'name' => Helper\getName('tableName', $generator),
        ]); ?>
    </div>
</div>
<?php

echo $form->field($generator, 'modelClass')->textInput([
    'name' => Helper\getName('modelClass', $generator)
]);
echo $form->field($generator, 'ns')->textInput([
    'name' => Helper\getName('ns', $generator)
]);
echo $form->field($generator, 'baseClass')->textInput([
    'name' => Helper\getName('baseClass', $generator)
]);
echo $form->field($generator, 'db')->textInput([
    'name' => Helper\getName('db', $generator)
]);
echo $form->field($generator, 'useTablePrefix')->checkbox([
    'name' => Helper\getName('useTablePrefix', $generator)
]);
echo $form->field($generator, 'generateRelations')->dropDownList([
    Generator::RELATIONS_NONE => 'No relations',
    Generator::RELATIONS_ALL => 'All relations',
    Generator::RELATIONS_ALL_INVERSE => 'All relations with inverse'
], [
    'name' => Helper\getName('generateRelations', $generator)
]);

echo $form->field($generator, 'generateLabelsFromComments')->checkbox([
    'name' => Helper\getName('generateLabelsFromComments', $generator)
]);
echo $form->field($generator, 'generateQuery')->checkbox([
    'name' => Helper\getName('generateQuery', $generator)
]);
echo $form->field($generator, 'queryNs')->textInput([
    'name' => Helper\getName('queryNs', $generator)
]);
echo $form->field($generator, 'queryClass')->textInput([
    'name' => Helper\getName('queryClass', $generator)
]);
echo $form->field($generator, 'queryBaseClass')->textInput([
    'name' => Helper\getName('queryBaseClass', $generator)
]);
echo $form->field($generator, 'enableI18N')->checkbox([
    'name' => Helper\getName('enableI18N', $generator)
]);
echo $form->field($generator, 'messageCategory')->textInput([
    'name' => Helper\getName('messageCategory', $generator)
]);
echo $form->field($generator, 'useSchemaName')->checkbox([
    'name' => Helper\getName('useSchemaName', $generator)
]);

foreach ($generator->behaviorModels as $key => $_next) {
    $behavior = $generator->behaviorsType[$key];

    Box::begin([
        'type' => Box::TYPE_PRIMARY,
        'withBorder' => true,
        'title' => $behavior['name'],
        'collapse' => true,
    ]);
    echo Form::widget([
        'model' => $_next,
        'columns' => 2,
        'form' => $form,
        'attributes' => $behavior['attributes'],
    ]);
    Box::end();
}
if ($generator->createForm) {
    \kartik\widgets\ActiveForm::end();
}
