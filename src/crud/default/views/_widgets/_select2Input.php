$form->field($model, {field})->widget(Select2::className(), [
    'data' => [
        'test' => 'test',
        'test1' => 'test1',
        'test2' => 'test2',
    ],
    'options' => ['prompt' => ''],
]);