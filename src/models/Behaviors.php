<?php


namespace infinitydevphp\gii\models;


use borales\extensions\phoneInput\PhoneInputBehavior;
use omgdef\multilingual\MultilingualBehavior;
use trntv\filekit\behaviors\UploadBehavior;
use yii\base\Model;
use yii\behaviors\BlameableBehavior;
use \Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Schema;
use yii\validators\BooleanValidator;
use yii\behaviors\AttributeBehavior;
use yii\validators\DefaultValueValidator;

class Behaviors extends Model
{
    public $checked;
    public $class;
    public $createdByAttribute;
    public $updatedByAttribute;
    public $createdAtAttribute;
    public $updatedAtAttribute;
    public $slugAttribute;
    public $userTable = 'user';
    public $immutable=true;

    public $languages;
    public $languageField;
    public $dynamicLangClass;
    public $requireTranslations;
    public $abridge;
    public $tableName;
    public $_attributesLang;
    public $multilingualBehavior;
    public $langClassName;

    public $name;
    public $attribute;
    public $pathAttribute;
    public $baseUrlAttribute;

    public $phoneAttribute;

    protected function checkMultLangBehavior() {
        return $this->class === MultilingualBehavior::className();
    }

    public function rules() {
        return [
            [['createdByAttribute', 'updatedByAttribute'], 'blameable'],
            [['createdAtAttribute', 'updatedAtAttribute'], 'timestampValidator'],
            [['slugAttribute'], 'sluggableValidator'],
            [['userTable', 'attribute', 'pathAttribute', 'baseUrlAttribute', 'name'], 'match', 'pattern' => '/^([\w ]+\.)?([\w\* ]+)$/', 'message' => 'Only word characters, and optionally spaces, an asterisk and/or a dot are allowed.'],
            [['immutable'], BooleanValidator::className()],
            [['attribute', 'pathAttribute', 'baseUrlAttribute'], 'uploadValidation'],
            [['createdAtAttribute', 'updatedAtAttribute', 'createdByAttribute', 'updatedByAttribute'], 'string', 'max' => 255],
            [['phoneAttribute'], 'phoneValidate'],
            [['dynamicLangClass'], DefaultValueValidator::className(), 'value' => function ($value, $model, $attribute=null) {
                return $this->class || $this->tableName ? true : null;
            }],
            [['abridge'], DefaultValueValidator::className(), 'value' => function ($value, $model, $attribute=null) {
                return $this->class || $this->tableName ? false : null;
            }],
            [['requireTranslations'], DefaultValueValidator::className(), 'value' => function ($value, $model, $attribute=null) {
                return $this->class || $this->tableName ? false : null;
            }],
            [['tableName', 'class'], 'classValid'],
            [['languageField'], DefaultValueValidator::className(), 'value' => function ($value, $model, $attribute=null) {
                return $this->class || $this->tableName ? 'language' : $value;
            }],
            [['_attributesLang'], DefaultValueValidator::className(), 'value' => function () {
                $this->class || $this->tableName ? [
                    new Field([
                        'name' => 'title',
                        'type' => Schema::TYPE_STRING,
                        'length' => 500,
                        'comment' => 'Title'
                    ]),
                    new Field([
                        'name' => 'body',
                        'type' => Schema::TYPE_TEXT,
                        'comment' => 'Body'
                    ]),
                    new Field([
                        'name' => 'slug',
                        'type' => Schema::TYPE_STRING,
                        'length' => 2000,
                        'comment' => 'Slug'
                    ]),
                ] : null;
            }]
        ];
    }

    public function classValid($attribute) {

        if (!$this->multilingualBehavior) {
            return;
        }

        if ($attribute == 'langClassName' && $this->langClassName && !$this->dynamicLangClass) {
            $this->addError($attribute, 'Class name is required if don\'t use dynamic model creation');
        } else if ($attribute === 'tableName' && $this->dynamicLangClass && !$this->tableName) {
            $this->addError($attribute, 'Table name required if using dynamic class');
        }
    }

    public function phoneValidate() {
        if ($this->class == PhoneInputBehavior::className()) {
            if (!preg_match('/^([\w ]+\.)?([\w\* ]+)$/', $this->phoneAttribute) || empty($this->phoneAttribute)) {
                $this->addError('phoneAttribute', 'Phone attribute name must be defined');
            }
        }
    }

    public function uploadValidation () {
        if ($this->class === UploadBehavior::className()) {
            if (empty($this->attribute)) {
                $this->addError('attribute', "Attribute empty");
            }
            if (empty($this->pathAttribute)) {
                $this->addError('pathAttribute', "Path attribute empty");
            }
            if (empty($this->baseUrlAttribute)) {
                $this->addError('baseUrlAttribute', "Base url attribute empty");
            }
        }
    }

    public function blameable($attribute, $params) {
        if ($this->class == BlameableBehavior::className() && empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('common', 'Required field bleamble ' . $attribute));
        } 
    }
    
    public function timestampValidator($attribute, $params) {
        if ($this->class == TimestampBehavior::className() && empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('common', 'Required field timestamp ' . $attribute));
        }
    }

    public function sluggableValidator($attribute, $params) {
        if ($this->class == SluggableBehavior::className() && empty($this->$attribute) && $this->checked ) {
            $this->addError('attribute', Yii::t('common', 'Required field sluggable ' . $attribute));
        }
    }

    public function attributeValidator($attribute, $params) {
        if ($this->class == AttributeBehavior::className() && empty($this->$attribute) && $this->checked) {
            $this->addError('attribute', Yii::t('common', 'Required field '));
        }
    }

    public function attributeLabels()
    {
        return [
            'checked' => 'Enable behavior in model',
            'class' => ''
        ];
    }

}