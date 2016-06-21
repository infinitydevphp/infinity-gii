$form->field($model, {field})->widget(DateRangePicker::className(), [
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd'
    ],
]);