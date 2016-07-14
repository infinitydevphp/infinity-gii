$form->field($model, {field})->widget(InputFile::className(), [
        'language'      => Yii::$app->language,
        'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
        'path' => 'image', // будет открыта папка из настроек контроллера с добавлением указанной под деритории
        'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
        'options'       => ['class' => 'form-control'],
        'buttonOptions' => ['class' => 'btn btn-default'],
        'multiple'      => false,       // возможность выбора нескольких файлов
        'callbackFunction' => new JsExpression('function(file, id){}') // id - id виджета
    ]);