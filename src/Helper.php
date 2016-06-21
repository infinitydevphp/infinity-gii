<?php

namespace infinitydevphp\gii\Helper {
    /**
     * @param string $attrName
     * @param \infinitydevphp\gii\ModelGenerator\Generator $gen
     * @return mixed
     */
    function getName($attrName, \infinitydevphp\gii\model\Generator $gen) {
        return $gen->additionName . "[{$attrName}]";
    }
}