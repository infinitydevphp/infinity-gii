<?php
namespace infinitydevphp\gii\model;

use borales\extensions\phoneInput\PhoneInputBehavior;
use infinitydevphp\gii\models\Behaviors;
use infinitydevphp\gii\models\Field;
use infinitydevphp\gii\table\Generator as TableGenerator;
use infinitydevphp\MultipleModelValidator\MultipleModelValidator;
use kartik\builder\Form;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
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

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Model Generator with behaviors';
    }

    public function predefineBehaviors() {
        return [
            'bleamble' => [
                'checked' => false,
                'title' => 'Blameable behavior',
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
                'title' => 'Timestamp behavior',
                'createdAtAttribute' => [
                    'name' => 'created_at',
                    'type' => Schema::TYPE_TIMESTAMP,
                    'comment' => 'Created At',
                ],
                'updatedAtAttribute' => [
                    'name' => 'updated_at',
                    'type' => Schema::TYPE_TIMESTAMP,
                    'comment' => 'Updated At',
                ],
                'class' => TimestampBehavior::className()
            ],
            'sluggable' => [
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
                'immutable' => true,
            ],
            'upload' => [
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
                if (!is_array($this->autoCreateField[$behName])) {
                    $this->autoCreateField[$behName][$_name] = [];
                }

                $this->autoCreateField[$behName][$_name] = new Field(array_merge($_next, [
                    'name' => $_name
                ]));
                $_next = $_next['name'];
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

    protected function buildBehaviors($configs) {
        $this->behaviorModels = $this->behaviorsType = [];
        foreach ($configs as $name => $behaviorConfig) {
            $title = isset($behaviorConfig['title']) ? $behaviorConfig['title'] : $name;

            if (isset($behaviorConfig['title'])) {
                unset($behaviorConfig['title']);
            }

            $this->behaviorsType[$name] = $this->buildNextType($title, $name, $this->additionName, $name, $behaviorConfig);
            $this->behaviorModels[$name] = new Behaviors($behaviorConfig);
        }
    }

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->tableBuilder = new TableGenerator([
            'tableNameRequired' => false
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
        $rules[] = ['createTable', 'boolean'];
        $rules[] = ['tableBuilder', 'safe'];

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
        $this->tableBuilder->forceTableCreate = $this->createTable;
        $result = $this->tableBuilder->validate() && parent::beforeValidate();

        if (!$result) {
            foreach ($this->behaviorModels as &$behaviorModel) {
                if (is_array($behaviorModel)) {
                    $behaviorModel = new Behaviors($behaviorModel);
                }
            }
        } else {
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
        $ind = is_bool($ind) ? sizeof($this->tableBuilder->fields) + 2 : $ind;

        $this->tableBuilder->fields[$ind] = new Field([
            'name' => $name,
            'type' => $type,
            'comment' => $comment,
            'length' => $length,
            'related_table' => $reltable,
            'related_field' => $reffield
        ]);
    }

    public function afterValidate()
    {
        foreach ($this->behaviorModels as $_name => $_next) {
            if ($_next->checked && isset($this->autoCreateField[$_name])) {
                foreach ($this->autoCreateField[$_name] as $field) {
                    /** @var $field Field */
                    $_next->name = $_next[$field['name']];
                    $this->addNewField($_next[$field->name], $field->type, $field->comment, $field->related_table, $field->related_field, $field->length);
                }
            }
        }

        parent::afterValidate(); // TODO: Change the autogenerated stub
    }

    public function getPublicOptions()
    {
        $options = [];
        if ($beh = $this->behaviorModels[3] && $this->behaviorModels[3]->checked) {
            /** @var $beh Behaviors */
            $options[] = [
                'name' => $beh->attribute
            ];
        }

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
            $publicOptions = $this->getPublicOptions();
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

    public function generateRules($table)
    {
        $rules = parent::generateRules($table);

        if ($this->behaviorModels[4]->checked) {
            if ($this->behaviorModels[4]->phoneAttribute) {
                $rules[] = "[['{$this->behaviorModels[4]->phoneAttribute}'], 'borales\\extensions\\phoneInput\\PhoneInputValidator']";
            }
        }

        return $rules;
    }
}