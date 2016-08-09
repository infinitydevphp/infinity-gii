<?php
namespace infinitydevphp\gii\model;

use borales\extensions\phoneInput\PhoneInputBehavior;
use infinitydevphp\gii\models\Behaviors;
use infinitydevphp\gii\models\Field;
use infinitydevphp\gii\table\Generator as TableGenerator;
use infinitydevphp\MultipleModelValidator\MultipleModelValidator;
use kartik\builder\Form;
use omgdef\multilingual\MultilingualBehavior;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\base\UnknownPropertyException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\gii\generators\model\Generator as ModelGeneratorBase;
use yii\helpers\ArrayHelper;

/**
 * Class ModelGenerator
 */
class Generator extends ModelGeneratorBase
{
    public $behaviorsType = [];
    /** @var Behaviors[] */
    public $behaviorModels = [];
    public $customBehaviors = [];
    public $additionName = 'Generator';
    /** @var  TableGenerator */
    public $tableBuilder;
    public $createTable = false;
    public $migrationPath = '';
    protected $files = [];
    protected $autoCreateField = [];
    public $createForm = true;
    public $addTraitsQuery = '';
    public $addUseQuery = '';
    public $translateGenerator = false;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Model Generator with behaviors';
    }

    /**
     * @param null $class
     * @return array|null
     */
    public function checkMultilingualBehavior($class = null) {
        $class = $class ? : MultilingualBehavior::className();
        $classes = [];
        foreach ($this->behaviorModels as $behaviorModel) {
            if ($behaviorModel->class === $class) {
                $_class = isset($behaviorModel['langClassName']) ? $behaviorModel['langClassName'] : '';
                if ($_class)
                    $classes[] = $_class;
            }
        }

        return count($classes) ? $classes : null;
    }

    public function predefineBehaviors() {
        return [
            'bleamble' => [
                'checked' => false,
                'title' => 'Blameable behavior',
                'alias' => 'bleamble',
                'updatedByAttribute' => [
                    'name' => 'updater_id',
                    'type' => Schema::TYPE_INTEGER,
                    'comment' => 'Updater',
                    'related_table' => 'user',
                    'related_field' => 'id',
                ],
                'createdByAttribute' => [
                    'name' => 'author_id',
                    'type' => Schema::TYPE_INTEGER,
                    'related_table' => 'user',
                    'related_field' => 'id',
                    'comment' => 'Author',
                ],
                'class' => BlameableBehavior::className(),
                'userTable' => 'user'
            ],
            'timestamp' => [
                'checked' => false,
                'alias' => 'timestamp',
                'title' => 'Timestamp behavior',
                'createdAtAttribute' => [
                    'name' => 'created_at',
                    'type' => Schema::TYPE_INTEGER,
                    'length' => 20,
                    'comment' => 'Created At',
                ],
                'updatedAtAttribute' => [
                    'name' => 'updated_at',
                    'type' => Schema::TYPE_INTEGER,
                    'length' => 20,
                    'comment' => 'Updated At',
                ],
                'class' => TimestampBehavior::className()
            ],
            'sluggable' => [
                'alias' => 'sluggable',
                'checked' => false,
                'title' => 'Sluggable behavior',
                'slugAttribute' => [
                    'name' => 'slug',
                    'type' => Schema::TYPE_STRING,
                    'comment' => 'Slug',
                    'length' => 2000,
                ],
                'attribute' => [
                    'name' => 'attribute',
                    'type' => Schema::TYPE_STRING,
                    'comment' => 'Base Attribute',
                    'length' => 2000,
                ],
                'immutable' => true,
                'class' => SluggableBehavior::className(),
            ],
            'upload' => [
                'alias' => 'upload',
                'checked' => false,
                'title' => 'Upload behavior',
                'pathAttribute' => [
                    'name' => 'photo_path',
                    'type' => Schema::TYPE_STRING,
                    'length' => 1024,
                    'comment' => 'Photo path',
                ],
                'baseUrlAttribute' => [
                    'name' => 'photo_url',
                    'type' => Schema::TYPE_STRING,
                    'length' => 1024,
                    'comment' => 'Photo url'
                ],
                'class' => UploadBehavior::className(),
                'attribute' => 'photo'
            ],
            'phone' => [
                'alias' => 'phone',
                'checked' => false,
                'title' => 'Phone behavior',
                'phoneAttribute' => [
                    'name' => 'phone',
                    'type' => Schema::TYPE_INTEGER,
                    'length' => 20,
                    'comment' => 'Phone'
                ],
                'class' => PhoneInputBehavior::className(),
            ],
        ];
    }

    protected function buildNextType($name, $behName, $addition, $index, &$behaviorAttributes) {
        $attributes = [];
        foreach ($behaviorAttributes as $_name => &$_next) {
            if (is_array($_next)) {

                if (isset($_next['type'])) {

                    if (!isset($this->autoCreateField[$behName])) {
                        $this->autoCreateField[$behName] = [];
                    }
                    if (!is_array($this->autoCreateField[$behName])) {
                        $this->autoCreateField[$behName][$_name] = [];
                    }

                    $this->autoCreateField[$behName][$_name] = new Field(array_merge($_next, [
                        'name' => $_name,
                    ]));
                    $_next = $_next['name'];
                } else {
                }
            }
            $attributes[$_name] = [
                'type' => $_name === 'class' ? Form::INPUT_HIDDEN : (is_bool($_next) ? Form::INPUT_CHECKBOX : Form::INPUT_TEXT),
                'options' => [
                    'name' => $addition . '[behaviorModels][' . $index . '][' . $_name . ']'
                ]
            ];
        }

        $config = [
            'name' => $name,
            'attributes' => $attributes
        ];

        return $config;
    }

    public function buildBehaviors($configs) {
        if (!sizeof($this->behaviorsType)) {
            $this->behaviorModels = $this->behaviorsType = [];
        }
        foreach ($configs as $name => $behaviorConfig) {
            $behaviorConfig = (array) $behaviorConfig;
            $title = isset($behaviorConfig['title']) ? $behaviorConfig['title'] : $name;

            if (isset($behaviorConfig['title'])) {
                unset($behaviorConfig['title']);
            }

            $config = $this->buildNextType($title, $name, $this->additionName, $name, $behaviorConfig);

            $this->behaviorsType[$name] = $config;
            $this->behaviorModels[$name] = new Behaviors($behaviorConfig);
        }
    }

    public function init()
    {
        parent::init();
        $this->tableBuilder = new TableGenerator([
            'tableNameRequired' => false,
            'useTablePrefix' => $this->useTablePrefix,
        ]);

        $behaviorsConfig = array_merge($this->predefineBehaviors(), $this->customBehaviors);

        $this->buildBehaviors($behaviorsConfig);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class for the specified database table with behaviors.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = ArrayHelper::merge(parent::rules(), [
        ]);

        foreach ($rules as $index => &$_next) {
            if ($_next[1] == 'required' && is_array($_next[0])) {
                if (($key = array_search('tableName', $_next[0])) !== false) {
                    unset($_next[0][$key]);
                }
            } else if ($_next[0] == 'tableName') {
                unset($rules[$index]);
            }
        }

        $rules[] = [['tableName'], 'tableNameValidate', 'skipOnEmpty' => false, 'skipOnError' => false];
        $rules[] = [['behaviorModels'], MultipleModelValidator::className(), 'baseModel' => Behaviors::className()];
        $rules[] = [['createTable'], 'boolean'];
        $rules[] = [['tableBuilder', 'customBehaviors'], 'safe'];

        return $rules;
    }

    public function tableNameValidate($attribute, $params)
    {

        if (empty($this->tableName) && !$this->createTable) {
            $this->addError('tableName', 'Table name required if not create table');
        } else if (empty($this->tableBuilder->tableName)) {
            $this->addError('tableBuilder', 'Table name is empty');
            $this->tableBuilder->addError('tableName', 'Table name is emty');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), [

        ]);
    }

    public function beforeValidate()
    {
        $options = ArrayHelper::merge(
            $this->tableBuilder,
            [
                'tableNameRequired' => $this->createTable,
                'autoCreateTable' => $this->createTable,
            ]
        );

        $this->tableBuilder = new TableGenerator($options);
        $this->tableBuilder->forceTableCreate = $this->createTable = false;
        $result = $this->tableBuilder->validate() && parent::beforeValidate();

        if (!$result) {
            foreach ($this->behaviorModels as &$behaviorModel) {
                if (is_array($behaviorModel)) {
                    $behaviorModel = new Behaviors($behaviorModel);
                }
            }
        } else {
            $this->tableBuilder->tableName = $this->tableBuilder->tableName ? : $this->tableName;
            $this->files = $this->tableBuilder->generate();
        }

        return $result;
    }

    protected function findField($name)
    {
        foreach ($this->tableBuilder->fields as $index => $_next) {
            if ($_next->name == $name) {
                return $index;
            }
        }

        return false;
    }

    public function addNewField($name, $type, $comment, $reltable = null, $reffield = null, $length = null)
    {
        $ind = $this->findField($name);

        return ['field' => new Field([
            'name' => $name,
            'type' => $type,
            'comment' => $comment,
            'length' => $length,
            'related_table' => $reltable,
            'related_field' => $reffield
        ]), 'index' => $ind];
    }

    protected function getTranslateBehavior() {
        foreach ($this->behaviorModels as $behaviorModel) {
            if ($behaviorModel->class === MultilingualBehavior::className())
                return $behaviorModel;
        }

        return null;
    }

    public function getBehaviorByClass($class) {
        foreach ($this->behaviorModels as &$behaviorModel) {
            if ($behaviorModel->className() === $class) {
                return $behaviorModel;
            }
        }

        return null;
    }

    public function getAnotherPublicAttribute() {
        $attribute = [];
        foreach ($this->behaviorModels as $behaviorModel) {
            if ($behaviorModel->class === UploadBehavior::className()) {
                $attribute[] = $behaviorModel->attribute;
            }
        }

        return $attribute;
    }

    public function getDefinitionAttribute() {
        $attribute = [];
        $model = $this->getTranslateBehavior();

        if ($model) {
            $attribute = ArrayHelper::merge($attribute, $model->attributesLang);
        }

        return $attribute;
    }

    public function afterValidate()
    {
        $size = count($this->tableBuilder->fields);
        foreach ($this->behaviorModels as $_name => $_next) {
            if ($_next->checked && isset($this->autoCreateField[$_next->alias])) {
                foreach ($this->autoCreateField[$_next->alias] as $field) {
                    /** @var $field Field */
                    $_next->name = $_next[$field['name']];
                    $res = $this->addNewField($_next[$field->name], $field->type, $field->comment, $field->related_table, $field->related_field, $field->length);
                    $index = is_bool($res['index']) ? $size++ : $res['index'];
                    $this->tableBuilder->fields[$index] = $res['field'];
                }
            }
        }

        parent::afterValidate();
    }

    public function getPublicOptions()
    {
        $options = [];
//        if ($beh = $this->behaviorModels[3] && $this->behaviorModels[3]->checked) {
//            /** @var $beh Behaviors */
//            $options[] = [
//                'name' => $beh->attribute
//            ];
//        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $this->tableName = $this->createTable ? $this->tableBuilder->tableName : $this->tableName;
        $files = $this->files;

        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            if (!isset($relations[$tableName])) {
                $relations[$tableName] = [];
            }

            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'behaviors' => $this->behaviorModels
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }
        }

        return $files;
    }

    protected function getBehaviorsTypeByName($alias) {
        foreach ($this->behaviorsType as $name => $behaviorType) {
            if ($name === $alias || (isset($behaviorType['alias']) && $behaviorType['alias'] === $alias)) {
                return $behaviorType;
            }
        }

        return false;
    }

    public function generateRules($table)
    {
        $rules = parent::generateRules($table);

        if (isset($this->behaviorsType['phone']) && isset($this->behaviorsType['phone']['checked'])
            && $this->behaviorsType['phone']['checked']) {
            if ($this->behaviorType['phone']['phoneAttribute']) {
                $rules[] = "[['{$this->behaviorType['phone']['phoneAttribute']}'], 'borales\\extensions\\phoneInput\\PhoneInputValidator']";
            }
        }

        return $rules;
    }
}