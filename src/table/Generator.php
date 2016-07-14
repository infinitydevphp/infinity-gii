<?php
namespace infinitydevphp\gii\table;

use infinitydevphp\MultipleModelValidator\MultipleModelValidator;
use infinitydevphp\tableBuilder\TableBuilder;
use infinitydevphp\tableBuilder\TableBuilderTemplateMigration;
use infinitydevphp\gii\models\Field;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\gii\Generator as GeneratorBase;
use Yii;
use yii\validators\RangeValidator;

class Generator extends GeneratorBase
{
    public $db = 'db';
    public $fields = [];
    public $tableName;
    public $createMigration = true;
    public $migrationPath = '@common/migrations/db';
    public $autoCreateTable = true;
    public $primaryKeyName = 'id';
    public $migrationCreate = true;
    public $fileName = '';
    public $migrationName = '';
    public $dropIfExists = true;
    public $tableNameRequired = true;
    public $useTablePrefix = true;
    public $forceTableCreate = false;

    protected $tablesList;
    public $enableI18N = true;

    public function init()
    {
        if (!sizeof($this->fields)) {
            $this->fields = [new Field([
                'name' => $this->primaryKeyName,
                'type' => Schema::TYPE_PK,
                'is_not_null' => true,
                'comment' => 'ID'
            ]),
            new Field([
                'name' => 'status',
                'type' => Schema::TYPE_SMALLINT,
                'default' => 1,
                'is_not_null' => true,
                'comment' => 'Status'
            ])];
        }
        $this->tablesList = Yii::$app->db->schema->tableNames;
        parent::init();
    }

    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
            'tableName' => 'Origin table name',
            'fieldsOrigin' => 'Origin table fields for DB table creation',
            'autoCreateTable' => 'Options for run create table query',
            'migrationPath' => 'Migration path',
            'fields' => 'Table fields'
        ]);
    }

    public function rules()
    {
        $rules = ArrayHelper::merge(parent::rules(), [
//            [['tableName'], RangeValidator::className(), 'not' => true, 'range' => $this->tablesList, 'message' => 'Table name exists'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['fields'], MultipleModelValidator::className(), 'baseModel' => Field::className()],
            [['autoCreateTable', 'useTablePrefix', 'tableNameRequired', 'dropIfExists'], 'boolean'],
            [['migrationPath', 'migrationCreate', 'migrationName'], 'safe'],
            [['primaryKeyName', 'fileName'], 'string', 'max' => 20],
            [['fields'], 'default', 'value' => [new Field(['type' => Schema::TYPE_PK, 'name' => $this->primaryKeyName])]],
            [['fields'], 'safe']
        ]);

        if ($this->tableNameRequired || $this->autoCreateTable) {
            $rules[] = ['tableName', 'required'];
        }

        return $rules;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'fields' => 'Table fields'
        ]);
    }

    public function generate()
    {
        $this->tableName = preg_replace('/({{%)(\w+)(}})?/', "$2", $this->tableName);
        $tableName = $this->tableName;
        if ($this->useTablePrefix) {
            $tableName = "{{%{$tableName}}}";
        }

        if (($this->autoCreateTable && isset($_POST['generate'])) || $this->forceTableCreate) {
            $this->dropIfExists = $this->forceTableCreate ? true : $this->dropIfExists;
            $tableGenerator = new TableBuilder([
                'tableName' => $tableName,
                'fields' => $this->fields,
                'useTablePrefix' => $this->useTablePrefix,
                'dropOriginTable' => $this->dropIfExists
            ]);
            $tableGenerator->runQuery(true);
        }
        $files = [];
        if ($this->migrationCreate) {
            $this->migrationName = Yii::$app->session->get($this->tableName) ?: false;
            $mCreate = new TableBuilderTemplateMigration([
                'tableName' => $tableName,
                'fields' => $this->fields,
                'useTablePrefix' => $this->useTablePrefix
            ]);
            if (!$this->migrationName) {
                Yii::$app->session->set($this->tableName, $mCreate->migrationName);
            }
            $this->migrationName = $this->migrationName ?: Yii::$app->session->get($this->tableName);
            $mCreate->migrationName = $this->migrationName ?: $mCreate->migrationName;
            $files[] = new CodeFile(
                Yii::getAlias($this->migrationPath) . '/' . $mCreate->migrationName . '.php',
                $mCreate->runQuery()
            );
        }

        return $files;
    }

    public function getName()
    {
        return 'Table Generator';
    }

    public function defaultTemplate()
    {
        return parent::defaultTemplate(); // TODO: Change the autogenerated stub
    }

    public function getDescription()
    {
        return 'This generator helps you create table';
    }

    public function stickyAttributes()
    {
        return ArrayHelper::merge(parent::stickyAttributes(), ['db', 'migrationPath']);
    }
}