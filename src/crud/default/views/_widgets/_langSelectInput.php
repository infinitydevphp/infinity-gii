$form->field($model, {field})
    ->widget(Select2::className(), [
        'data' => is_array(Yii::$app->params['availableLocales']) ? Yii::$app->params['availableLocales'] : [],
    ]);
