<?php
/**
 * @var $model \infinitydevphp\gii\models\Behaviors
 */
?>
                    'class' => '<?= $model->class ?>',
                    //'value' => function ($event) {
                    //    /** @var Event $event */
                    //    /** @var UserProfileTranslate $profile */
                    //    $model = $event->sender;
                    //
                    //    return Inflector::slug(implode('-', [$model->id, $model->attr1]));
                    //    return Inflector::slug(implode('-', [$model->id, $model->attr1, $model->attr2, $model->attr3]));
                    //}
                    'attribute' => '<?= $model->attribute ?>',
                    'immutable' => <?= $model->immutable ? 'true' : 'false' ?>

