<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \infinitydevphp\gii\crud\Generator */

$controllerClass = StringHelper::basename($generator->reallyControllerNs);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->reallySearchNs);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(str_replace('/', '\\', ltrim($generator->reallyControllerNs, '\\'))) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim(str_replace('/', '\\', $generator->reallySearchNs), '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerFrontendClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
<?= $generator->isMultilingual ? ' * @property string $language' : ' *';?>
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerFrontendClass) . "\n" ?>
{
<?php if ($generator->isMultilingual) { ?>
    public $languageParam = 'lang';
    public $defaultLanguage = 'ru-RU';

    public function init() {
        parent::init();
        if (isset(Yii::$app->params['defaultLanguage'])) {
            $this->defaultLanguage = Yii::$app->params['defaultLanguage'];
        }
    }
<?php } ?>
   /**
    * Behaviors
    * @return array
    */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
<?php if ($generator->isMultilingual) { ?>
        $searchModel-><?=$generator->languageField?> = $this->language;
<?php    }
?>
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

<?php endif; ?>
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($slug) {
        return $this->render('view', [
            'model' => $this->findModel($slug),
        ]);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
        $model = <?= $modelClass ?>::find();
<?php
if (count($pks) === 1 && !$generator->isMultilingual) {
    $condition = '$id';
} else {
?>
        <?php //$model = $model->joinWith(['translations']); ?>
<?php
    $condition = [];
    foreach ($pks as $pk) {
        $condition = ["'$pk' => \$$pk"];
    }
    $condition = '$condition = [' . implode(', ', $condition) . '];';
?>
        <?= $condition . PHP_EOL; ?>
<?php
    if ($generator->isMultilingual) {
        $condition = 'if ($this->' . $generator->languageField . ') {' . PHP_EOL;
        $condition.= '            $condition[\'translations.' . $generator->languageField . '\'] = $this->getLanguage();' . PHP_EOL;
        $condition.= '        }' . PHP_EOL;
?>
<?php //$condition; ?>
<?php }
}
?>
        if (($model = $model->andWhere($condition)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('frontend', 'The requested page does not exist.'));
        }
    }

<?php if ($generator->isMultilingual) { ?>
    public function getLanguage($default=null) {
        return Yii::$app->request->get($this->languageParam,
                    Yii::$app->request->post($this->languageParam, $default ? :
                        $this->defaultLanguage));
    }
<?php } ?>
}
