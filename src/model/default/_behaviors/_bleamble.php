<?php
/**
 * @var $model \infinitydevphp\gii\models\Behaviors
 */
?>
                    'class'=>'<?= $model->class?>',
<?php if ($model->createdByAttribute) { ?>
                    'createdByAttribute' => '<?= $model->createdByAttribute ?>',
<?php } ?>
<?php if ($model->updatedByAttribute) { ?>
                    'updatedByAttribute' => '<?= $model->updatedByAttribute?>',
<?php } ?>
