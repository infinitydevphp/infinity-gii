<?php
/**
 * @author infinitydevphp <infinitydevphp@gmail.com>
 */

namespace infinitydevphp\gii\models;

use infinitydevphp\MultipleModelValidator\MultipleModelValidator;
use yii\base\Model;
use yii\db\Migration;
use yii\db\Schema;
use yii\gii\Generator;
use yii\helpers\ArrayHelper;
use yii\validators\BooleanValidator;
use yii\validators\DefaultValueValidator;
use yii\validators\NumberValidator;
use yii\validators\RangeValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use \Yii;

/**
 * Class Field
 * @property mixed $type
 * @property string $name
 *
 * @package common\generators\TranslateTable\models\Field
 */
class Field extends Generator
{
    public $type;
    public $name;
    public $length;
    public $is_not_null;
    public $is_unique;
    public $comment;
    public $default;
    public $related_table;
    public $related_field;
    public $fk_name;
    public $precision;
    public $scale;
    public $unsigned;
    public $isCompositeKey;

    public $messageCategory = 'translate_table';

    public function Init()
    {
        parent::init();

        $dir = __DIR__ . '/../messages/';
        Yii::$app->i18n->translations[$this->messageCategory] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => $dir,
        ];
    }

    public function rules()
    {
        return [
            [['type', 'name'], RequiredValidator::className()],
            [['length', 'precision', 'scale'], NumberValidator::className()],
            [['is_not_null', 'is_unique', 'unsigned', 'isCompositeKey'], BooleanValidator::className()],
            [['name'], StringValidator::className(), 'max' => 50],
            [['comment', 'fk_name'], StringValidator::className(), 'max' => 255],
            [['type'], RangeValidator::className(), 'range' => $this->getTypeList()],
        ];
    }

    public function getTypeList() {
        $types = [];
        $current = self::getTypes();

        foreach ($current as $_nextOptionGroup) {
            foreach ($_nextOptionGroup as $name => $title) {
                $types[] = $name;
            }
        }

        return $types;
    }

    public function attributeLabels()
    {
        return [
            'type' => Yii::t($this->messageCategory, 'Type'),
            'length' => Yii::t($this->messageCategory, 'Length'),
            'comment' => Yii::t($this->messageCategory, 'Comment'),
            'name' => Yii::t($this->messageCategory, 'Name'),
            'default' => Yii::t($this->messageCategory, 'Default'),
            'is_not_null' => Yii::t($this->messageCategory, 'Is not null'),
            'unsigned' => Yii::t($this->messageCategory, 'Unsigned'),
        ];
    }

    public function generate()
    {
        // TODO: Implement generate() method.
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function getDescription()
    {
        return parent::getDescription(); // TODO: Change the autogenerated stub
    }

    public static function getTypes()
    {
        $model = new self();
        $schema = new Migration();
        return [
            Yii::t($model->messageCategory, 'Primary Key') => [
                Schema::TYPE_BIGPK => 'Big primary key',
                Schema::TYPE_PK => 'Primary key',
                Schema::TYPE_UPK => 'Unsigned primary key',
                Schema::TYPE_UBIGPK => 'Unsigned big primary key',
            ],
            Yii::t($model->messageCategory, 'String type') => [
                Schema::TYPE_STRING => 'String',
                Schema::TYPE_CHAR => 'Char',
                Schema::TYPE_TEXT => 'Text',
            ],
            Yii::t($model->messageCategory, 'Number type') => [
                Schema::TYPE_BIGINT => 'Big integer',
                Schema::TYPE_DECIMAL => 'Decimal',
                Schema::TYPE_DOUBLE => 'Double',
                Schema::TYPE_FLOAT => 'Float',
                Schema::TYPE_INTEGER => 'Integer',
                Schema::TYPE_BIGINT => 'Big integer',
                Schema::TYPE_SMALLINT => 'Small integer',
            ],
            Yii::t($model->messageCategory, 'Date & time type') => [
                Schema::TYPE_DATE => 'Date',
                Schema::TYPE_DATETIME => 'DateTime',
                Schema::TYPE_TIMESTAMP => 'Timestamp',
                Schema::TYPE_TIME => 'Time',
            ],
            Yii::t($model->messageCategory, 'Other type') => [
                Schema::TYPE_BINARY => 'Binary',
                Schema::TYPE_MONEY => 'Money',
            ],
        ];
    }
}