<?php


namespace infinitydevphp\gii\translateModelBehavior;

use infinitydevphp\gii\models\Behaviors;
use infinitydevphp\gii\models\Field;
use infinitydevphp\tableBuilder\TableBuilder;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\IntegrityException;
use yii\db\Schema;
use yii\gii\Generator as BaseGenerator;
use yii\helpers\ArrayHelper;
use infinitydevphp\gii\model\Generator as BaseModelGenerator;
use infinitydevphp\gii\table\Generator as TableGenerator;
use \Yii;

/**
 * Class Generator
 * @property BaseModelGenerator $baseModel
 * @property BaseModelGenerator $translateModel
 *
 * @package infinitydevphp\gii\TranslateModelGenerator
 */
class Generator extends BaseGenerator
{
    public $baseModel = null;
    public $translateModel = null;
    public $languageField = 'language';

    public function init()
    {
        parent::init();
        $this->baseModel = new BaseModelGenerator([
            'additionName' => 'Generator[baseModel]',
            'createForm' => false,
            'customBehaviors' => [
                'trans' => [
                    'title' => 'Multilingual behavior',
                    'alias' => 'trans',
                    'checked' => true,
                    'languageField' => $this->languageField ?: 'language',
                    'langClassSuffix' => '',
                    'dynamicLangClass' => false,
                    'requireTranslations' => false,
                    'abridge' => false,
                    'langForeignKey' => '',
                    'attributesLang' => 'title, body, lang',
                    'langClassName' => '',
                    'languages' => null,
                    'class' => 'omgdef\multilingual\MultilingualBehavior',
                ]
            ],
            'addUseQuery' => 'use omgdef\multilingual\MultilingualTrait;' . PHP_EOL,
            'addTraitsQuery' => 'use MultilingualTrait;' . PHP_EOL,
            'translateGenerator' => true,
        ]);
        $this->translateModel = new BaseModelGenerator([
            'additionName' => 'Generator[translateModel]',
            'createForm' => false
        ]);

        $this->translateModel->tableBuilder->fields[] = new Field([
            'name' => $this->languageField,
            'type' => Schema::TYPE_STRING,
            'length' => 50,
            'is_not_null' => true,
            'comment' => 'Language',
        ]);
    }

    public function afterValidate()
    {
        $bV = $this->baseModel->validate();
        $tV = $this->translateModel->validate();

        return parent::afterValidate() && $bV && $tV;
    }

    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName); // TODO: Change the autogenerated stub

        if (isset($data['Generator'])) {
            foreach ($data['Generator'] as $attr => $value) {
                if (is_array($value)) {
                    $this->{$attr} = new BaseModelGenerator(ArrayHelper::merge($value, [
                        'additionName' => 'Generator[' . $attr . ']',
                        'createForm' => false,
                        'customBehaviors' => [
                            'trans' => [
                                'title' => 'Multilingual behavior',
                                'alias' => 'trans',
                                'checked' => true,
                                'languageField' => $this->languageField ?: 'language',
                                'langClassSuffix' => '',
                                'dynamicLangClass' => false,
                                'requireTranslations' => false,
                                'abridge' => false,
                                'langForeignKey' => '',
                                'attributesLang' => 'title, body, lang',
                                'langClassName' => '',
                                'languages' => null,
                                'class' => 'omgdef\multilingual\MultilingualBehavior',
                            ]
                        ],
                        'addUseQuery' => $attr == 'baseModel' ? 'use omgdef\multilingual\MultilingualTrait;' . PHP_EOL : '',
                        'addTraitsQuery' => $attr == 'baseModel' ? 'use MultilingualTrait;' . PHP_EOL : '',
                        'translateGenerator' => $attr == 'baseModel' ? true : false,
                    ]));

                    $this->{$attr}->tableBuilder = new TableGenerator($value['tableBuilder']);
                    $this->{$attr}->behaviorModels = [];
                    foreach ($value['behaviorModels'] as $_next) {
                        $this->{$attr}->behaviorModels[] = new Behaviors($_next);
                    }
                } else {
                    $this->{$attr} = $value;
                }
            }

            $result = $this->baseModel->validate();

            if (!$result) {
                $this->addError('baseModel', 'Base model error data');
            }

            $result = $this->translateModel->validate();
            if (!$result) {
                $this->addError('translateModel', 'Translate model error data');
            }

            $tblName = $this->baseModel->createTable ? $this->baseModel->tableBuilder->tableName : $this->baseModel->tableName;
            $table_name = preg_replace('/({{%)(\w+)(}})?/', "$2", $tblName);
            $db = $this->baseModel->db;
            /** @var Connection $_conn */
            $_conn = Yii::$app->{$db};
            $this->translateModel->addNewField($_conn->schema->getRawTableName($table_name) . '_' . $this->baseModel->tableBuilder->primaryKeyName,
                Schema::TYPE_INTEGER, 'Origin content', $tblName, $this->baseModel->tableBuilder->primaryKeyName);
        }

        return $result;
    }

    public function beforeValidate()
    {
        $result = $this->baseModel->validate();
        if (!$result) {
            $this->addError('baseModel', 'Base model error data');
        }

        $this->translateModel->modelClass = $this->translateModel->modelClass ? : $this->baseModel->modelClass . "Translate";
        $this->translateModel->ns = $this->translateModel->ns ? : $this->baseModel->ns;
        if ($this->translateModel->generateQuery) {
            $this->translateModel->queryNs = $this->translateModel->queryNs ?: $this->baseModel->queryNs;
            $this->translateModel->queryClass = $this->translateModel->queryClass ?: $this->baseModel->queryClass . "Translate";
            $this->translateModel->migrationPath = $this->baseModel->migrationPath;
            $this->translateModel->tableBuilder->tableName = $this->translateModel->tableBuilder->tableName ? : $this->baseModel->tableBuilder->tableName . "_translate";
        }

        $result = $this->translateModel->validate();
        if (!$result) {
            $this->addError('translateModel', 'Translate model error data');
        }

        return parent::beforeValidate() && $result; // TODO: Change the autogenerated stub
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['languageField'], 'required'],
            [['baseModel', 'translateModel'], 'safe'],
            [['languageField'], 'string', 'max' => 50],
            [['languageField'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.']
        ]);
    }

    public function getDescription()
    {
        return 'Translate model generator with multilingual behavior';
    }

    public function getName()
    {
        return 'Translate model & multilingual behavior';
    }

    protected function findTranslateField($name) {
        foreach ($this->translateModel->behaviorModels as $behavior) {
            /** @var $behavior Behaviors */
            if ($behavior->createdAtAttribute == $name ||
                $behavior->updatedAtAttribute == $name ||
                $behavior->updatedByAttribute == $name ||
                $behavior->createdByAttribute == $name) {
                return false;
            }
        }

        return true;
    }

    protected function findTranslatableBehavior() {
        foreach ($this->baseModel->behaviorModels as &$behaviorModel) {
            if ($behaviorModel->class === 'omgdef\multilingual\MultilingualBehavior') {
                return $behaviorModel;
            }
        }

        return null;
    }

    public function generate()
    {
        try {
            if ($this->translateModel->createTable) {
                $tb = new TableBuilder([
                    'tableName' => $this->translateModel->createTable ? $this->translateModel->tableBuilder->tableName : $this->translateModel->tableName,
                    'fields' => $this->translateModel->tableBuilder->fields,
                    'db' => $this->baseModel->db
                ]);
                $tb->dropTable();
            }
            if ($this->baseModel->createTable) {
                $tb = new TableBuilder([
                    'tableName' => $this->baseModel->createTable ? $this->baseModel->tableBuilder->tableName : $this->baseModel->tableName,
                    'fields' => $this->baseModel->tableBuilder->fields,
                    'db' => $this->translateModel->db
                ]);
                $tb->dropTable();
            }
        } catch (IntegrityException $exp) {} catch (Exception $exp) {}

        if (!isset($this->baseModel->behaviorModels['trans'])) {
            $this->baseModel->buildBehaviors($this->baseModel->customBehaviors);
        }

        if (isset($this->baseModel->behaviorModels['trans']) && ($trans = $this->findTranslatableBehavior())) {
            /** @var Behaviors $trans */

            $trans->checked = true;
            if (!$trans->dynamicLangClass) {
                $trans->attributesLang = [];

                foreach ($this->translateModel->tableBuilder->fields as $field) {
                    /** @var Field $field */
                    if ($field->name == $this->baseModel->tableBuilder->tableName . '_id' ||
                        !$this->findTranslateField($field->name) || $field->name == $this->translateModel->tableBuilder->primaryKeyName) {
                        continue;
                    }

                    $trans->attributesLang[] = $field->name;
                    $trans->langClassName = $this->translateModel->ns . "\\" . $this->translateModel->modelClass;
                }
            } else {
                $trans->attributesLang = is_array($trans->attributesLang) ? $trans->attributesLang : explode(',', $trans->attributesLang);
            }

            foreach ($trans->attributesLang as &$item) {
                $item = trim($item);
            }
        }

        $files = ArrayHelper::merge(
            $this->baseModel->generate(),
            $this->translateModel->generate()
        );

        if (isset($trans)) {
            $trans->attributesLang = implode(",", $trans->attributesLang);
        }

        return $files;
    }
}