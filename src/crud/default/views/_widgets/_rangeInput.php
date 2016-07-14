$form->field($model, {field})->widget(RangeInput::className(), [
        'html5Options' => [
            'min' => 0,
            'max' => 1000,
            'addon' => [
                'append' => [
                    'content' => '<i class="glyphicon glyphicon-star"></i>'
                ]
            ]
        ]
    ]);