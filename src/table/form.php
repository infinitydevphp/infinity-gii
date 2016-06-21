<style>
    form > div.row > div.col-lg-8 {
        width: 100%;
    }
</style>
<?php
$addition = isset($addition) ? $addition : '';
$notVisible = isset($notVisible) && is_array($notVisible) ? $notVisible : [];
$titleBox = isset($titleBox) ? $titleBox : 'Origin table';
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator \infinitydevphp\gii\table\Generator */

use insolita\wgadminlte\Box;

\yii\web\YiiAsset::register($this);
\insolita\wgadminlte\JCookieAsset::register($this);
insolita\wgadminlte\ExtAdminlteAsset::register($this);
\dmstr\web\AdminLteAsset::register($this);

$emptyBox = in_array('migrationPath', $notVisible) &&
            in_array('autoCreateTable', $notVisible) &&
            in_array('db', $notVisible) &&
            in_array('createMigration', $notVisible);

if (!$emptyBox) {
    Box::begin([
        'type' => Box::TYPE_PRIMARY,
        'withBorder' => true,
        'title' => 'Base options',
        'collapse' => true,
    ]);
}
if (!in_array('db', $notVisible)) {
    echo $form->field($generator, $addition . 'db');
}
if (!in_array('migrationPath', $notVisible)) {
    echo $form->field($generator, $addition . 'migrationPath');
}

if (!in_array('createMigration', $notVisible)) {
    echo $form->field($generator, $addition . 'createMigration')->checkbox();
}

if (!in_array('autoCreateTable', $notVisible)) {
    echo $form->field($generator, $addition . 'autoCreateTable')->checkbox();
}

if (!in_array('useTablePrefix', $notVisible)) {
    echo $form->field($generator, $addition . 'useTablePrefix')->checkbox();
}

if (!in_array('dropIfExists', $notVisible)) {
    echo $form->field($generator, $addition . 'dropIfExists')->checkbox();
}

if (!$emptyBox) {
    Box::end();
}
Box::begin([
    'type' => Box::TYPE_PRIMARY,
    'withBorder' => true,
    'title' => $titleBox,
    'collapse' => true
]);
if (!in_array('migrationName', $notVisible)) {
    echo $form->field($generator, $addition . 'migrationName')->hiddenInput()->label(false);
}
if (!in_array('tableName', $notVisible)) {
    echo $form->field($generator, $addition . 'tableName');
}
Box::begin([
    'type' => Box::TYPE_PRIMARY,
    'withBorder' => true,
    'title' => 'Input columns origin table',
    'collapse' => true
]);

echo $form->field($generator, $addition . 'fields')
    ->widget(\infinitydevphp\MultipleModelValidator\widgets\MultipleInput::className(), [
        'attributeOptions' => [
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
            'validateOnChange' => true,
            'validateOnSubmit' => true,
            'validateOnBlur' => true,
        ],
        'data' => $generator->fields,
        'baseModel' => \infinitydevphp\gii\models\Field::className(),
        'columns' => [
            [
                'name' => 'name',
                'enableError' => true,
                'title' => 'Name'
            ],
            [
                'name' => 'type',
                'type' => \kartik\widgets\Select2::className(),
                'options' => [
                    'data' => \infinitydevphp\gii\models\Field::getTypes(),
                ], 'title' => 'Type'
            ],
            [
                'name' => 'length',
                'enableError' => true,
                'title' => 'Length'
            ],
            [
                'name' => 'is_not_null',
                'enableError' => true,
                'type' => 'checkbox',
                'title' => 'Is Not Null'
            ],
            [
                'name' => 'is_unique',
                'enableError' => true,
                'type' => 'checkbox',
                'title' => 'Unique'
            ],
            [
                'name' => 'unsigned',
                'enableError' => true,
                'type' => 'checkbox',
                'title' => 'Unsigned'
            ],
            [
                'name' => 'comment',
                'enableError' => true,
                'title' => 'Comment'
            ],
            [
                'name' => 'default',
                'enableError' => true,
                'title' => 'Default Value'
            ],
            [
                'name' => 'precision',
                'enableError' => true,
                'title' => 'Precision'
            ],
            [
                'name' => 'scale',
                'enableError' => true,
                'title' => 'Scale'
            ],
            [
                'name' => 'fk_name',
                'enableError' => true,
                'title' => 'FK Name'
            ],
            [
                'name' => 'related_table',
                'enableError' => true,
                'title' => 'Related table'
            ],
            [
                'name' => 'related_field',
                'enableError' => true,
                'title' => 'Related field'
            ],
        ],
    ]);
Box::end();
Box::end();

?>

<script>

</script>