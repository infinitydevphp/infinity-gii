$form->field($model, {field})->widget(DatePicker::className(), [
    'type' => DatePicker::TYPE_RANGE,
    'attribute2' => 'field2',
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd'
    ],
]);