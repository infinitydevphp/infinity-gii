$form->field($model, {field})->widget(TimePicker::className(), [
    'disabled' => false,
    'readonly' => false,
    'pluginOptions' => [
        'template' => 'dropdown',
        'minuteStep' => 10,
        'secondStep' => 60,
        'defaultTime' => 'current',
        'showSeconds' => true,
        'showMeridian' => false,
        'disableMouseWheel' => false,
        'disableFocus' => false,
        'modalBackdrop' => false,
        'timeFormat' => 'HH:mm::ss'
    ],
    'pluginEvents' => [
//        "show" => (new JsExpression("function(e) {  // `e` here contains the extra attributes }"))->expression,
//        "hide" => (new JsExpression("function(e) {  // `e` here contains the extra attributes }"))->expression,
//        "update" => (new JsExpression("function(e) {  // `e` here contains the extra attributes }"))->expression,
    ]
]);