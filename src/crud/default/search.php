<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \infinitydevphp\gii\crud\Generator */

$modelClass = StringHelper::basename(ltrim(str_replace('/', '\\', $generator->reallySearchNs), '\\'));
$searchModelClass = StringHelper::basename($generator->reallySearchNs);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(str_replace('/', '\\', ltrim($generator->reallySearchNs, '\\'))) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use yii\db\ActiveQuery;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
<?php
$previousAttr = [];
    $attrTranslate = $generator->translateAttribute;
    $attrTranslate[] = $generator->relationField;
    if (count($generator->translateAttribute) && $generator->isMultilingual) {
        $rules[] = "[['" . implode("', '", $attrTranslate) . "'], 'safe']";
        $addAndWhere = [];
        foreach ($attrTranslate as $_next) {
            $column = explode('.', $_next);
            /** @var array $column */
            $column = end($column);
            if (in_array($column, $previousAttr)) continue;
            $previousAttr[] = $column;
            $addAndWhere[] = "translations.{$column} => \$this->{$column},";
?>
    public $<?= $_next?>;
<?php
    }
        $anotherColumn = [];
        foreach ($generator->columns as $column) {
            /** @var \infinitydevphp\gii\models\WidgetsCrud $column */
            $column = explode('.', $column->fieldName);
            /** @var array $column */
            $column = end($column);
            if (in_array($column, $previousAttr)) continue;

            $previousAttr[] = $column;
            $anotherColumn[] = $column;
            $rules[] = "[['{$column}'], 'safe']";

?>
    public $<?= $column?>;
<?php

        }
}
?>
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
<?php $relationClass = $generator->relationClass? '\\' . $generator->relationClass : (isset($modelAlias) ? $modelAlias : $modelClass)?>
    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /** @var $query ActiveQuery */
        $query = self::find();

        $query = $query->innerJoinWith(['translations' => function ($q) {
            /** @var $q yii\db\ActiveQuery */
            return $q->from(<?=$relationClass?>::tableName() . ' as translations');
        }]);
<?php
if ($generator->isMultilingual) {
    foreach ($attrTranslate as $item) {
        $searchConditions[] = "\$query = \$query->andFilterWhere(['translations.{$item}' => \$this->{$item}]);";
    }

        foreach ($anotherColumn as $item) {
        $searchConditions[] = "\$query = \$query->andFilterWhere(['translations.{$item}' => \$this->{$item}]);";
    }
?>
        if ($this-><?=$generator->languageField;?>) {
            $query = $query->andWhere(['translations.<?=$generator->languageField;?>' => $this-><?=$generator->languageField;?>]);
        }
<?php
}
?>
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

<?php if ($generator->isMultilingual) {
    foreach ($attrTranslate as $item) {
?>
        $dataProvider->sort->attributes['<?= $item?>'] = [
            'asc' => ['translations.<?= $item ?>' => SORT_ASC],
            'desc' => ['translations.<?= $item ?>' => SORT_DESC],
        ];
<?php
    }
}?>
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
}
