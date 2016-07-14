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

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
<?php
    if (count($generator->translateAttribute) && $generator->isMultilingual) {
        $rules[] = "[['" . implode("', '", $generator->translateAttribute) . "'], 'safe']";
        $addAndWhere = [];
        foreach ($generator->translateAttribute as $_next) {
            $addAndWhere[] = "translations.{$_next} => \$this->{$_next},";
?>
    public $<?= $_next?>;
<?php
    }}
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
<?php $relationClass = $generator->relationClass? : (isset($modelAlias) ? $modelAlias : $modelClass)?>
    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $query = $query->innerJoinWith(['translations' => function ($q) {
            /** @var $q ActiveQuery */
            return $q->from(<?=$relationClass?>::tableName() . ' as translations');
        }]);
<?php
if ($generator->isMultilingual) {
    foreach ($generator->translateAttribute as $item) {
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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
}
