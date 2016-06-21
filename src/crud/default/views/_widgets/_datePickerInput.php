$form->field($model, {field})->widget(DatePicker::className(), [
    'type' => DatePicker::TYPE_COMPONENT_APPEND,
    'pickerButton' => [],
    'removeButton' => false,
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd'
    ],
]);