<?php
/**
 * @var $generator \infinitydevphp\gii\crud\Generator
 */
echo '<?php' . PHP_EOL;
?>
/**
 * @var integer $index
 * @var integer $key
 * @var \common\modules\service\models\Services $model
 */

use yii\helpers\Url;
use yii\helpers\Html;

?>
<?php
$attr = [];
foreach ($generator->columns as $column) {
    /** @var $column \infinitydevphp\gii\models\WidgetsCrud */
    $column = explode('.', $column->fieldName);
    $column = end($column);
    if (in_array($column, $attr)) {
        continue;
    }
    $attr[] = $column;
    ?>
<div>
    <?='<?=$model->' . $column . '?>' . PHP_EOL;?>
</div>
    <?php
}?>

<div>
    <?='<?=Html::a(Yii::t(\'frontend\', \'More\'), Url::to([\'/services/services/view?slug=\' . $model->slug]))?>'.PHP_EOL ?>
</div>
