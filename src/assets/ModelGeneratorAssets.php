<?php

/**
 * @author infinitydevphp <infinitydevphp@gmai.com>
 */

namespace infinitydevphp\gii\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class ModelGeneratorAssets extends AssetBundle
{
    public $js = [
        'js/model-generator.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];

    public function init() {
        $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "model";
    }
}