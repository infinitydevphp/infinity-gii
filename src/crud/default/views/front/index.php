<?php
echo '<?php' . PHP_EOL;
?>

use yii\widgets\ListView;
/** @var \yii\data\ActiveDataProvider $dataProvider */
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_item',
    'layout' => '{items}{pager}',
    'itemOptions' => [
        'tag' => 'div',
        'class' => 'item-services-list'
    ],
    'viewParams' => [

    ],
    'options' => [
        'tag' => 'div',
        'class' => 'services-list'
    ],
]);