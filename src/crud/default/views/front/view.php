<?php
/**
 * @var $generator \infinitydevphp\gii\crud\Generator
 */
echo "<?php\n";
?>
/**
 * @var integer $index
 * @var integer $key
 * @var <?=$generator->modelClass;?> $model
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