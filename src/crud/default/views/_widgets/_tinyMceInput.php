$form->field($model, {field})->widget(TinyMce::className(), [
        'settings' => [
    //        'plugins' => [
    //            "advlist autolink lists link charmap print preview anchor",
    //            "searchreplace visualblocks code fullscreen",
    //            "insertdatetime media table contextmenu paste spellchecker",
    //            "fullpage fullscreen media preview print save searchreplace table textpattern visualblocks wordcount"
    //        ],
    //        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spellchecker | fullpage | fullscreen | media | preview | print | save | searchreplace | table | textpattern | visualblocks | wordcount",
            'fileManager' => [
                'class' => TinyMceElFinder::className(),
                'connectorRoute' => 'elfinder/connector',
            ],
    //        'spellchecker_languages' => 'Russian=ru,Ukrainian=uk,English=en',
    //        'spellchecker_language' => 'ru',  // default language
    //        'spellchecker_rpc_url' => 'http://speller.yandex.net/services/tinyspell'
        ]
    ]);