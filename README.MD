# Install 

```
composer require infinitydevphp "dev-master@dev"
```

# Config Example
```
'inifinity-model' => [
            'class' => \infinitydevphp\gii\model\Generator::className(),
        ],
        'inifinity-crud' => [
            'class' => \infinitydevphp\gii\crud\Generator::className(),
        ],
        'inifinity-translate-model' => [
            'class' => \infinitydevphp\gii\translateModel\Generator::className()
        ],
        'inifinity-translate-model-behavior' => [
            'class' => \infinitydevphp\gii\translateModelBehavior\Generator::className()
        ],
        'inifinity-tablebuilder' => [
            'class' => \infinitydevphp\gii\table\Generator::className()
        ],
        'inifinity-translate-tablebuilder' => [
            'class' => \infinitydevphp\gii\translateTable\Generator::className()
        ],
```