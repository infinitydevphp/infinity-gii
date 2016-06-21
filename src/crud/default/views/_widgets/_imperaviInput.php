$form->field($model, {field})->widget(ImperaviWidget::className(), [
    'settings' => [
        'plugins' => [
            'clips',
            'counter',
            'definedlinks',
            'filemanager',
            'fullscreen',
            'imagemanager',
            'fontcolor',
            'limiter',
            'table',
            'textdirection',
            'textexpander',
            'video'
        ],
        'definedLinks' => Yii::$app->urlManager->createUrl(['/api/widget/imperavi-links']),
        'imageUpload' => Yii::$app->urlManager->createUrl(['/api/widget/imperavi-upload-image/', ['folder' => 'source/imperavi/images']]),
        'fileUpload' => Yii::$app->urlManager->createUrl(['/api/widget/imperavi-upload-file', ['folder' => 'source/imperavi/files']]),
        'fileManagerJson' => Yii::$app->urlManager->createUrl(['/api/widget/imperavi-default-file-list', ['folder' => 'source/imperavi/files']]),
        'imageManagerJson' => Yii::$app->urlManager->createUrl(['/api/widget/imperavi-default-image-list', ['folder' => 'source/imperavi/images']]),
    ],
]);
