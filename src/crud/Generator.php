<?php


namespace infinitydevphp\gii\crud;

use borales\extensions\phoneInput\PhoneInputBehavior;
use common\helpers\StringHelper;
use infinitydevphp\gii\models\Field;
use infinitydevphp\gii\models\WidgetsCrud;
use infinitydevphp\MultipleModelValidator\MultipleModelValidator;
use trntv\filekit\behaviors\UploadBehavior;
use vova07\imperavi\Widget;
use yii\base\Model;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseInflector;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii2mod\gii\crud\Generator as BaseCrudGenerator;
use \Yii;

class Generator extends BaseCrudGenerator
{
    public $widgetsUseClass = [];
    public $columns = [];

    public $excludedField = [];
    protected $fieldExclude = [];
    public $enableI18N = true;
    protected $columnUsed = [
        '' => [
            
        ],
        'ckEditor' => [
            'mihaildev\elfinder\ElFinder',
            'mihaildev\elfinder\CKEditor',
        ],
        'color' => [
            'kartik\widgets\ColorInput'
        ],
        'datePicker' => [
            'kartik\widgets\DatePicker'
        ],
        'dateRange2FieldPicker' => [
            'kartik\widgets\DatePicker'
        ],
        'dateRangePicker' => [
            'kartik\daterange\DateRangePicker'
        ],
        'elFinderUpload' => [
            'mihaildev\elfinder\InputFile',
            'mihaildev\elfinder\ElFinder',
            'yii\web\JsExpression',
        ],
        'imperavi' => [
            'vova07\imperavi\Widget as ImperaviWidget',
        ],
        'langSelect' => [
            '\kartik\select2\Select2'
        ],
        'masked' => [
            'yii\widgets\MaskedInput'
        ],
        'phone' => [
            'borales\extensions\phoneInput\PhoneInput',
            'yii\web\JsExpression',
        ],
        'range' => [
            'kartik\range\RangeInput'
        ],
        'select2' => [
            '\kartik\select2\Select2',
        ],
        'tinymce' => [
            'zxbodya\yii2\tinymce\TinyMce',
            'zxbodya\yii2\elfinder\TinyMceElFinder',
        ],
        'input' => [],
        'text' => [],
        'textArea' => [],
        'timePicker' => [
            'kartik\widgets\TimePicker',
            'yii\web\JsExpression',
        ],
        'trevor' => [
            '\kato\sirtrevorjs\SirTrevor',
            'udokmeci\yii2kt\assets\CustomSirTrevorAsset',
            'yii\helpers\Url',
            'expression' => [
                'CustomSirTrevorAsset::register($this)'
            ],
        ],
        'upload' => [
            'trntv\filekit\widget\Upload'
        ],
        'password' => [

        ],
        '_default' => [
            'yii\helpers\Html',
            'yii\widgets\ActiveForm',
            'expression' => [],
        ],
    ];

    public $expressions = [];
    public $used = [];

    public $hasUploadBehavior = false;

    /** @var \Closure|null */
    public $excludeFieldCallback = null;

    protected function searchColumn($name) {
        foreach ($this->columns as $_column) {
            if ($_column['fieldName'] === $name) {
                return true;
            }
        }

        return false;
    }
    /**
     * @param ActiveRecord $model
     */
    protected function excludeField($model)
    {
        $this->fieldExclude = ArrayHelper::merge(
            is_array($this->excludedField) ? $this->excludedField : [$this->excludedField],
            $this->fieldExclude
        );

        if (is_callable($this->excludeFieldCallback)) {
            $func = $this->excludeFieldCallback;
            $result = $func($this);
            $result = $result ? (is_array($result) ? $result : [$result]) : [];

            $this->fieldExclude = ArrayHelper::merge($this->fieldExclude, $result);
        }

        $this->fieldExclude = ArrayHelper::merge($this->fieldExclude, $model->getTableSchema()->primaryKey);

        $behaviors = $model->getBehaviors();

        foreach ($behaviors as $_next) {
            $_nextCopy = $_next;
            $_next = (array) $_next;
            $_next['class'] = $_nextCopy->className();

            if ($_next['class'] === BlameableBehavior::className()) {
                if (isset($_next['updatedByAttribute'])) {
                    if (!empty($_next['updatedByAttribute'])) {
                        $this->fieldExclude[] = $_next['updatedByAttribute'];
                    }
                } else {
                    $this->fieldExclude[] = 'updated_by';
                }
                if (isset($_next['createdByAttribute'])) {
                    if (!empty($_next['createdByAttribute'])) {
                        $this->fieldExclude[] = $_next['createdByAttribute'];
                    }
                } else {
                    $this->fieldExclude[] = 'created_by';
                }
            }
            if ($_next['class'] === TimestampBehavior::className()) {
                if (isset($_next['updatedAtAttribute'])) {
                    if (!empty($_next['updatedAtAttribute'])) {
                        $this->fieldExclude[] = $_next['updatedAtAttribute'];
                    }
                } else {
                    $this->fieldExclude[] = 'updated_at';
                }
                if (isset($_next['createdAtAttribute'])) {
                    if (!empty($_next['createdAtAttribute'])) {
                        $this->excludedField[] = $_next['createdAtAttribute'];
                    }
                } else {
                    $this->fieldExclude[] = 'created_at';
                }
            }
            if ($_next['class'] === PhoneInputBehavior::className()) {
                $this->columns[] = new WidgetsCrud([
                    'fieldName' => $_next['phoneAttribute'],
                    'widgetType' => 'phone'
                ]);
            }
            if ($_next['class'] === UploadBehavior::className()) {
                $this->hasUploadBehavior = $_next['attribute'];
                $this->excludedField[] = $_next['pathAttribute'];
                $this->excludedField[] = $_next['baseUrlAttribute'];
                $this->columns[] = new WidgetsCrud([
                    'fieldName' => $_next['attribute'],
                    'widgetType' => 'upload'
                ]);
                if (isset($this->columnUsed['upload']) && ($uses = $this->columnUsed['upload'])) {
                    if (isset($uses['expression'])) {
                        $this->expressions = ArrayHelper::merge($this->expressions, $uses['expression']);
                        unset($this->columnUsed['expression']);
                    }
                }

                $this->used = ArrayHelper::merge($this->used, $uses);
            }
        }

        $this->columnUsed = ArrayHelper::merge($this->columnUsed, $this->widgetsUseClass);
    }

    public function getWidgets()
    {
        return [
            '' => 'Not set',
            'ckEditor' => 'CK Editor Widget',
            'color' => 'Color Input Widget',
            'datePicker' => 'Date picker Widget',
            'dateRange2FieldPicker' => 'Range Date picker Widget',
            'dateRangePicker' => 'Range date picker in one field Widget',
            'elFinderUpload' => 'El-Finder Upload Input Widget',
            'imperavi' => 'Imperavi Editor Widget',
            'langSelect' => 'Language Select',
            'input' => 'Input',
            'phone' => 'Phone Input',
            'range' => 'Range Input',
            'select2' => 'Select2 Widget',
            'textarea' => 'Textarea field',
            'timePicker' => 'Time picker Widget',
            'tinyMce' => 'Tiny Mce editor Widget',
            'trevor' => 'Sir Trevor templates Widget',
            'upload' => 'Terentev Upload Widget',
            /*'blueimp' => 'BlueImp File Upload Widget for Yii2',
            'uploadDream' => 'Yii2 upload behavior'*/
        ];
    }

    protected function widgetByFieldType($type, $name)
    {
        if (in_array($name, $this->fieldExclude)) {
            return false;
        }

        $config = [
            'color' => ['patterns' => ['color']],
            'tinyMce' => [Schema::TYPE_TEXT],
            'uploadInput' => ['hasBehaviorClassName' => 'upload'],
            'datePicker' => [Schema::TYPE_DATE, Schema::TYPE_DATETIME, Schema::TYPE_TIMESTAMP],
            'timePicker' => [Schema::TYPE_TIME],
            'trevor' => ['patterns' => ['trevor']],
            'langSelect' => ['patterns' => ['language']],
            'password' => [
                'patterns' => ['^(password|pass|passwd|passcode)$']
            ],
        ];

        foreach ($config as $_nameWidget => $_next) {
            if (isset($_next['patterns'])) {
                $patterns = !is_array($_next['patterns']) ? [$_next['patterns']] : $_next['patterns'];
                foreach ($patterns as $pattern) {
                    if (preg_match("/" . $pattern . "/i", $name)) {
                        return $_nameWidget;
                    }
                }
            }

            if (in_array($type, $_next)) {
                return $_nameWidget;
            }
        }

        return '';
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['columns'], MultipleModelValidator::className(), 'baseModel' => WidgetsCrud::className(), 'skipOnEmpty' => true]
        ]);
    }

    /**
     * @param WidgetsCrud $nextModel
     * @return string
     */
    public function generateWidgetActiveField($nextModel) {
        $controllerName = explode(' ', BaseInflector::camel2words(str_replace('Controller', '', StringHelper::basename($this->controllerClass))));
        $controller = '';
        $module = $this->moduleID ? "/{$this->moduleID}" : '';

        foreach ($controllerName as $item) {
            $controller .= (strlen($controller) ? '-' : '') . (strtolower($item));
        }

        $nextModel->pathName = $nextModel->pathName ? : '_widgets';

        return str_replace(['{controller}', '{module}'], [$controller, $module],$this->render("views/{$nextModel->pathName}/_{$nextModel->widgetType}Input.php"));
    }

    public function getName()
    {
        return 'Crud generator with custom widget';
    }

    public function getDescription()
    {
        return 'Crud generator with custom widget';
    }

    public function generate()
    {
        /** @var ActiveRecord $model */
        $model = new $this->modelClass();
        $this->excludeField($model);

        foreach ($this->columns as $index => $column) {
            /** @var $column WidgetsCrud */
            if (!$column->fieldName) {
                unset($this->columns[$index]);
            }
        }

        if (!sizeof($this->columns) && $tableName = Yii::$app->db->schema->getRawTableName($model::tableName())) {
            $fields = Yii::$app->db->getTableSchema($tableName);
            $columns = $fields->columns;

            foreach ($columns as $column) {
                echo $column->name . ' ' . $column->type . "<br/>";
                $typeWidget = $this->widgetByFieldType($column->type, $column->name);
                if (!is_bool($typeWidget)) {
                    if (!$this->searchColumn($column->name)) {
                        $this->columns[] = new WidgetsCrud([
                            'widgetType' => $typeWidget,
                            'fieldName' => $column->name
                        ]);
                    }
                }
            }
        }

        foreach ($this->columns as $column) {
            /** @var WidgetsCrud $column */
            if (isset($this->columnUsed[$column->widgetType])) {
                $uses = $this->columnUsed[$column->widgetType];
                if (isset($uses['expression'])) {
                    $this->expressions = ArrayHelper::merge($this->expressions, $uses['expression']);
                    unset($uses['expression']);
                }
                $this->used = ArrayHelper::merge($this->used, $uses);
            }
        }

        if (isset($this->columnUsed['_default'])) {
            $default = $this->columnUsed['_default'];
            if (isset($default['expression'])) {
                $this->expressions = ArrayHelper::merge($this->expressions, $default['expression']);
                unset($default['expression']);
            }
            $this->used = ArrayHelper::merge($this->used, $default);
        }

        $this->used = array_unique($this->used);
        $this->expressions = array_unique($this->expressions);
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;

        return parent::generate();
    }
}