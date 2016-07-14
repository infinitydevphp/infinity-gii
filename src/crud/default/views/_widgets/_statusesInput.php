$form->field($model, {field})->widget(Select2::className(), [
        'data' => {model}::getStatuses(),
        'options' => ['prompt' => ''],
    ]);