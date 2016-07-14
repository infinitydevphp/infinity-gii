<?php

use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator \infinitydevphp\gii\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
\infinitydevphp\gii\assets\ModelGeneratorAssets::register($this);
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";

foreach ($generator->used as $use) {
    if (is_string($use)) {
        echo "use {$use};" . PHP_EOL;
    }
}
?>

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */

?>

<?php
foreach ($generator->expressions as $expression) {
    if (is_string($expression)) {
        echo "{$expression};";
    }
}
?>

<div class="<?= Inflector::camel2id($generator->getModelNameForView()) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'options' => [
            'class' => 'form-vertical'
        ],
    ]); ?>

<?php
if (count($generator->columns)) {
    foreach ($generator->columns as $column) {
        /** @var $column \infinitydevphp\gii\models\WidgetsCrud */
        $arrays = explode('.', $column->fieldName);
        $field = end($arrays);
        echo "    <?php \n    echo " . str_replace(['{field}', '{model}'], ['\'' . $field . '\'', '$model'], $generator->generateWidgetActiveField($column)) . " \n    ?>\n\n";
    }
} else {
foreach ($safeAttributes as $attribute) {
    echo "    <?php echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
}} ?>
    <div class="form-group">
        <?= "<?php \n echo " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) <?= "\n" ?> ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>