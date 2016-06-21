<style>
    form > div.row > div.col-lg-8{
        width: 100%;
    }
</style>
<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator \infinitydevphp\tableGenerator\TranslateTableGenerator\Generator */

use insolita\wgadminlte\Box;

//\common\generators\assets\GeneratorAssets::register($this);
\yii\web\YiiAsset::register($this);
\insolita\wgadminlte\JCookieAsset::register($this);
insolita\wgadminlte\ExtAdminlteAsset::register($this);
\dmstr\web\AdminLteAsset::register($this);
Box::begin([
    'type' => Box::TYPE_PRIMARY,
    'withBorder' => true,
    'title' => 'Base options',
    'collapse' => true,
]);
echo $form->field($generator, 'db');
echo $form->field($generator, 'migrationPath');
echo $form->field($generator, 'createMigration')->checkbox();
echo $form->field($generator, 'autoCreateTable')->checkbox();
echo $form->field($generator, 'dropIfExists')->checkbox();

Box::end();

echo $this->renderFile(__DIR__ . '/../TableGenerator/form.php', [
    'addition' => '[originTable]',
    'notVisible' => [
        'migrationPath',
        'createMigration',
        'autoCreateTable',
        'db',
        'dropIfExists',
    ],
    'form' => $form,
    'generator' => $generator->originTable,
    'titleBox' => 'Origin table'
]);

echo $this->renderFile(__DIR__ . '/../TableGenerator/form.php', [
    'addition' => '[translateTable]',
    'notVisible' => [
        'migrationPath',
        'createMigration',
        'autoCreateTable',
        'db',
        'dropIfExists',
    ],
    'form' => $form,
    'generator' => $generator->translateTable,
    'titleBox' => 'Translatable table'
]);
?>

<script>
    
</script>