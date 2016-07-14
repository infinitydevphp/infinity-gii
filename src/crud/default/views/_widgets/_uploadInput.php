$form->field($model, {field})->widget(Upload::className(), [
        'url' => [
            '{module}{controller}/{field}-upload'
        ],
        'maxFileSize' => 5000000, // 5 MiB
    ]);