<?php
/**
 * @var $model \infinitydevphp\gii\models\Behaviors
 */

$langs = $model->languages ? [] : explode(',', $model->languages);
$model->attributesLang = !is_array($model->attributesLang) ? explode(',', $model->attributesLang) : $model->attributesLang;

?>

                    'languageField' => '<?= $model->languageField ? : 'language' ?>',
                    'langClassSuffix' => '<?= $model->langClassSuffix ?>',
                    'dynamicLangClass' => <?= $model->dynamicLangClass ? 'true' : 'false' ?>,
                    'requireTranslations' => <?= $model->requireTranslations ? 'true' : 'false'?>,
                    'abridge' => <?= $model->abridge ? 'true' : 'false'?>,
                    'attributes' => ['<?= implode('\', \'', $model->attributesLang) ?>'],
                    'class' => '<?= $model->class ?>',
                    'langClassName' => '<?= $model->langClassName ?>',
<?php if (count($langs)) { ?>
                    'languages' => is_array(Yii::$app->params['availableLocales']) ? Yii::$app->params['availableLocales'] : ['<?= implode('\', \'', $langs); ?>'],
<?php } ?>

