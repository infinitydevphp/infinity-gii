<?php


namespace infinitydevphp\gii\models;


use yii\base\Model;

class WidgetsCrud extends Model
{
    public $fieldName;
    public $widgetType;
    public $pathName;

    public function rules() {
        return [
            [['fieldName'], 'required'],
            [['widgetType'], 'safe'],
            [['pathName'], 'default', 'value' => '_widgets'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'fieldName' => 'Field Name',
            'widgetType' => 'Widget type'
        ];
    }
}