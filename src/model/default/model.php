<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \infinitydevphp\gii\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/** @var $behaviors \infinitydevphp\gii\models\Behaviors[] list of behaviors */
echo "<?php\n";
$multi = $generator->checkMultilingualBehavior();
?>

namespace <?= $generator->ns ?>;

use Yii;
use yii\helpers\ArrayHelper;
<?php
if ($multi) {
    foreach ($multi as $item) {
?>
use <?= $item.';' . PHP_EOL;?>
<?php
}}
?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php
    $attributes = $generator->getDefinitionAttribute();
    $publicAttributes = $generator->getAnotherPublicAttribute();
    if (count($publicAttributes)) {
    foreach ($publicAttributes as $item) {
?>
 * @property $<?= $item ?>

<?php
    }}
    if (count($attributes)) {
?>

 *
 * # Language attributes
 *
<?php
        foreach ($attributes as $item) { ?>
 * @property string <?="\${$item}\n" ?>
<?php
        }
?>
 *
 * # End Language attributes
 *
<?php
    }
?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BUYING = 2;
    const STATUS_DISABLED = 3;
    const STATUS_PAY_PROCESS = 4;
    const STATUS_TRASHED = 5;
<?php     if (count($publicAttributes)) {
    foreach ($publicAttributes as $item) {
        ?>

    public $<?= $item ?>;

<?php
    }}
?>
    /**
     * Get model statuses
     *
     * @return array
     */
    public function getStatuses() {
        return [
            self::STATUS_DRAFT => <?= $generator->generateString('Draft') ?>,
            self::STATUS_ACTIVE => <?= $generator->generateString('Active') ?>,
            self::STATUS_BUYING => <?= $generator->generateString('Buying') ?>,
            self::STATUS_DISABLED => <?= $generator->generateString('Disabled') ?>,
            self::STATUS_PAY_PROCESS => <?= $generator->generateString('Pay process') ?>,
            self::STATUS_TRASHED => <?= $generator->generateString('Trashed') ?>,
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
<?php $beh = []; foreach ($behaviors as $_name => $_nextBehavior) { $viewPath  =__DIR__ . '/_behaviors/_' . $_nextBehavior->alias . '.php';
if ($_nextBehavior->checked && is_file($viewPath) && !in_array($_nextBehavior->alias, $beh)) { ?>
                '<?=$_nextBehavior->alias; ?>' => [
<?= $this->renderFile($viewPath, ['model' => $_nextBehavior]); ?>
                ],
<?php $beh[] = $_nextBehavior->alias; }} ?>
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
<?php
if ($multi = $generator->checkMultilingualBehavior()) {
    $rulesMerge = "";
    foreach ($multi as $item) {
        $rulesMerge .= ", (new " . basename(str_replace('\\', '/', $item)) . "())->rules()";
    }
    ?>
        return ArrayHelper::merge([<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>
]<?= $rulesMerge ? $rulesMerge : '[]'?>);
<?php } else { ?>
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
<?php } ?>
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return (new <?= $queryClassFullName ?>(get_called_class()))<?=$generator->translateGenerator ? '->multilingual()' : ''?>;
    }
<?php endif; ?>
}
