<?php
/**
 * @var $model \infinitydevphp\gii\models\Behaviors
 */

$langs = $model->languages ? [] : explode(',', $model->languages);

?>

                    'languageField' => '<?= $model->languageField ? : 'language' ?>',
                    'dynamicLangClass' => '<?= $model->dynamicLangClass?>',
                    'requireTranslations' => <?= $model->requireTranslations ? 'true' : 'false'?>,
                    'abridge' => <?= $model->abridge ? 'true' : 'false'?>,
                    'attributes' => ['<?= implode('\', \'', $model->_attributesLang) ?>'],
                    'class' => <?= $model->class ?>,
                    'langClassName' => <?= $model->langClassName ?>,
<?php if (count($langs)) { ?>
                    'languages' => ['<?= implode('\', \'', $langs); ?>'],
<?php } ?>