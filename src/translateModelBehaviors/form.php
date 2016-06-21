<style>
    form > div.row > div.col-lg-8 {
        width: 100%;
    }
</style>
<?php
$form = \kartik\widgets\ActiveForm::begin();

$addition = isset($addition) ? $addition : '';
$notVisible = isset($notVisible) && is_array($notVisible) ? $notVisible : [];
$titleBox = isset($titleBox) ? $titleBox : 'Origin table';
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator \infinitydevphp\gii\translateModel\Generator */

use insolita\wgadminlte\Box;
\yii\web\YiiAsset::register($this);
\insolita\wgadminlte\JCookieAsset::register($this);
insolita\wgadminlte\ExtAdminlteAsset::register($this);
\dmstr\web\AdminLteAsset::register($this);

echo $form->field($generator, 'languageField');

Box::begin([
    'type' => Box::TYPE_PRIMARY,
    'withBorder' => true,
    'title' => 'Origin model',
    'collapse' => true,
]);
echo $this->renderFile(__DIR__ . '/../model/form.php', [
    'generator' => $generator->baseModel,
    'form' => $form,
    'addition' => '[baseModel]'
]);
Box::end();

Box::begin([
    'type' => Box::TYPE_PRIMARY,
    'withBorder' => true,
    'title' => 'Translate model',
    'collapse' => true,
]);
echo $this->renderFile(__DIR__ . '/../model/form.php', [
    'generator' => $generator->translateModel,
    'form' => $form,
    'addition' => '[translateModel]'
]);
Box::end();
$form::end();
?>

<script>

</script>