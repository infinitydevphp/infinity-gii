$form->field($model, {field})
    ->widget(Select2::className(), [
        'data' => Yii::$app->params['availableLocales']
    ]);
