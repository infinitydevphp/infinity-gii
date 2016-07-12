<?php

namespace infinitydevphp\gii\assets;

use yii\web\AssetBundle;

class CrudGeneratorAssets extends AssetBundle {

    public $js = [
        'crud/js/crud.js',
        'model/js/model-generator.js'
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];

    public function init() {
        $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR;
    }
}