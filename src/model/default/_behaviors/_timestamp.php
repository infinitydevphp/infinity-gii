<?php
/**
 * @var $model \infinitydevphp\gii\models\Behaviors
 */
?>
                    'class'=>'<?= $model->class?>',
<?php if ($model->createdAtAttribute) { ?>
                    'createdAtAttribute' => '<?= $model->createdAtAttribute ?>',
<?php } ?>
<?php if ($model->updatedAtAttribute) { ?>
                    'updatedAtAttribute' => '<?= $model->updatedAtAttribute?>',
<?php } ?>

