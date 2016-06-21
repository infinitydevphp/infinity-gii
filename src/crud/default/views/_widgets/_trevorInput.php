$form->field($model, {field})->widget(SirTrevor::className(),[
    'debug'=>true,
    'imageUploadUrl'=> Yii::$app->urlManager->createUrl(['/api/widget/trevor-upload', ['folder' => 'source/trevor/images']]),
    'blockTypes' => [ 'Imagetop','Redactor', "Headex", 'Imageleft', 'Twocolumns', 'Threecolumns','Imageright','Textblock','Hr'],
]);