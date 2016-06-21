<?php
/**
 * @var $model \infinitydevphp\gii\models\Behaviors
 */
?>
                    'class'=>'<?= $model->class?>',
<?php if ($model->phoneAttribute) { ?>
                    'phoneAttribute' => '<?= $model->phoneAttribute ?>',
<?php } ?>