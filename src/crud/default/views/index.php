<?php

use yii\helpers\Inflector;


/* @var $this yii\web\View */
/* @var $generator \infinitydevphp\gii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words($generator->getModelNameForView()))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id($generator->getModelNameForView()) ?>-index">

    <h1><?= "<?php echo " ?>Html::encode($this->title) ?></h1>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?php echo " ?>Html::a(<?= $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words($generator->getModelNameForView())]) ?>, ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= "<?php " ?>\yii\widgets\Pjax::begin(['enablePushState' => false,'timeout' => 3000]); ?>
<?php if ($generator->indexWidgetType === 'grid') { ?>
    <?= "<?php echo " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
<?php
$count = 0;
if (sizeof($generator->columns)) {
    $previous = ['id'];
    echo "            'id',\n";
    foreach ($generator->columns as $column) {
        /** @var $column \infinitydevphp\gii\models\WidgetsCrud */
        $arrays = explode('.', $column->fieldName);
        $fieldName = end($arrays);
        if (in_array($fieldName, $previous)) continue;

        if (substr_count(strtolower($column->widgetType), 'langselect') ) {
            echo "            [\n";
            echo "              'attribute' => '" . $fieldName . "',\n";
            echo "              'filter' => is_array(Yii::\$app->params['availableLocales']) ? Yii::\$app->params['availableLocales'] : [],\n";
            echo "            ],\n";
        } else if (substr_count(strtolower($column->widgetType), 'status') ) {
            echo "            [\n";
            echo "              'attribute' => '" . $fieldName . "',\n";
            echo "              'filter' => \$searchModel::getStatuses(),\n";
            echo "            ],\n";
        } else if (substr_count(strtolower($column->widgetType), 'select')) {
            echo "            [\n";
            echo "              'attribute' => '" . $fieldName . "',\n";
            echo "              'filter' => [],\n";
            echo "            ],\n";
        } else if (preg_match('(photo|avatar|image|img)', $fieldName)) {
            echo "            [\n";
            echo "              'attribute' => '" . $fieldName . "',\n";
            echo "              'format' => 'raw',\n";
            echo "              'filter' => false,\n";
            echo "              'value' => function (\$model, \$key, \$index) {\n";
            echo "                  return '<img src=\"' . \$model->getPreview" . ucfirst(mb_strtolower($fieldName)) . "() . '\" width=\"100\"/>';\n";
            echo "              },\n";
            echo "            ],\n";
        } else {
            echo "            '" . $fieldName . "',\n";
        }
        $previous[] = $fieldName;
    }
} else if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
            [
                'header' => Yii::t('backend', 'Action'),
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
            ],
        ],
    ]);
    ?>
    <?= "<?php " ?>\yii\widgets\Pjax::end(); ?>
<?php } else { ?>
    <?= "<?php echo " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php } ?>

</div>
