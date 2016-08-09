<?php
namespace infinitydevphp\gii\migration;

use infinitydevphp\MultipleModelValidator\MultipleModelValidator;
use infinitydevphp\tableBuilder\TableBuilder;
use infinitydevphp\tableBuilder\TableBuilderTemplateMigration;
use infinitydevphp\gii\models\Field;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
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
    public $migrationPath = '@common/migrations/db';
    public $fileName = '';
    public $migrationName = '';
    public $useTablePrefix = true;

    public function init()
    {
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
            [['tableName'], 'required'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['fields'], MultipleModelValidator::className(), 'baseModel' => Field::className()],
            [['useTablePrefix'], 'boolean'],
            [['migrationPath', 'migrationName'], 'safe'],
        ]);

        return $rules;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'fields' => 'Table fields'
        ]);
    }


    protected function getTableFields() {
        if (sizeof($this->fields) > 1)
            return;
        $pks = [];


        $table = Yii::$app->db->schema->getTableSchema($this->tableName);

        if ($table && $columns = $table->columns) {
            $pks = $table->primaryKey;
            /** @var ColumnSchema[] $columns */
            $this->fields = [];
            foreach ($columns as $name => $column) {
                $this->fields[] = new Field([
                    'name' => $name,
                    'length' => $column->size,
                    'type' => $column->phpType,
                    'precision' => $column->precision,
                    'scale' => $column->scale,
                    'comment' => $column->comment,
                    'is_not_null' => !$column->allowNull,
                    'isCompositeKey' => in_array($name, $pks),
                ]);
            }
        }

        return $pks;
    }

    public function generate()
    {
        $this->tableName = preg_replace('/({{%)(\w+)(}})?/', "$2", $this->tableName);
        $tableName = $this->tableName;
        if ($this->useTablePrefix) {
            $tableName = "{{%{$tableName}}}";
        }

        $primary = $this->getTableFields();

        $files = [];
        $this->migrationName = Yii::$app->session->get($this->tableName) ?: false;
        $mCreate = new TableBuilderTemplateMigration([
            'tableName' => $tableName,
            'fields' => $this->fields,
            'useTablePrefix' => $this->useTablePrefix,
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

        return $files;
    }

    public function getName()
    {
        return 'Migration Generator';
    }

    public function defaultTemplate()
    {
        return parent::defaultTemplate();
    }

    public function getDescription()
    {
        return 'This generator helps you create migration from existing table';
    }

    public function stickyAttributes()
    {
        return ArrayHelper::merge(parent::stickyAttributes(), ['db', 'migrationPath']);
    }
}