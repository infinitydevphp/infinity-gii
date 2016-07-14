$form->field($model, {field})->widget(MaskedInput::className(), [
        'mask' => '***-***-**-**',
        'clientOptions' => [
            'skipOptionalPartCharacter' => ' ',
            'autoUnmask' => true,
        ]
    ]);