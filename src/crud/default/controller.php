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
<?php
$attribute = '';
if ($attribute = $generator->hasUploadBehavior) {
    ?>
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
<?php
}
?>
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim(str_replace('/', '\\', $generator->reallySearchNs), '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerBackendClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerBackendClass) . "\n" ?>
{
    public $deleteFromDB = false;
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

    public function actions() {
        return ArrayHelper::merge(parent::actions(), [
<?php   if ($attribute) {
    foreach ($attribute['attributes'] as $attr) {
        $attr = explode('.', $attr);
        $attr = end($attr);
?>
                '<?=$attr?>-upload' => [
                    'class' => UploadAction::className(),
                    'deleteRoute' => '<?=$attr?>-delete',
                ],
                    '<?=$attr?>-delete' => [
                    'class' => DeleteAction::className()
                ]
<?php
}?>
<?php } ?>
        ]);
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '<?= \yii\helpers\Inflector::camel2words($generator->getModelNameForView()); ?> has been created.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '<?= \yii\helpers\Inflector::camel2words($generator->getModelNameForView()); ?> has been saved.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
<?php $actionParams = explode(',', $actionParams);
        foreach ($actionParams as $actionParam) { $actionParam = trim($actionParam);?>
        <?=$actionParam?> = <?=$actionParam?> ? : Yii::$app->request->get('<?=$actionParam?>');
<?php } ?>
        if ($this->deleteFromDB) {
            $this->findModel(<?= implode(',', $actionParams) ?>)->delete();
        } else {
            $model = $this->findModel(<?= implode(',', $actionParams) ?>);
            $model->status = <?= $modelClass ?>::STATUS_DRAFT;
            $model->update();
        }
        Yii::$app->session->setFlash('success', '<?= \yii\helpers\Inflector::camel2words($generator->getModelNameForView()); ?> has been deleted.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= implode(',', $actionParams) ?>)
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
//        $condition = 'if ($this->' . $generator->languageField . ') {' . PHP_EOL;
//        $condition.= '            $condition[] = [\'translations.' . $generator->languageField . '\' => $this->getLanguage()];' . PHP_EOL;
//        $condition.= '        }' . PHP_EOL;
?>
<?php //$condition; ?>
<?php }
}
?>
        if (($model = $model->andWhere($condition)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
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
