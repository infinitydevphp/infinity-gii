<?php


namespace infinitydevphp\gii\ModelGenerator;

use \Yii;
use yii\db\Connection;

trait RestApi
{
    protected $isAjaxQuery = false;

    public function checkQuery() {
        if (Yii::$app->request->isAjax) {
            if ($getTableAttribute = Yii::$app->request->post('table_name_attr')) {
                return $this->jsonTableAttr($getTableAttribute);
            }

            if ($allTables = Yii::$app->request->post('table_list')) {
                return $this->jsonTables();
            }
        }

        return false;
    }

    public function jsonTableAttr($tableName) {
        $dbID = Yii::$app->request->post('db');
        /** @var Connection $conn */
        $conn = Yii::$app->{$dbID};

        $tbl = $conn->schema->getTableSchema($tableName, true);

        return is_object($tbl) ? $tbl->columnNames : [];
    }

    public function jsonTables() {
        $dbID = Yii::$app->request->post('db');
        /** @var Connection $conn */
        $conn = Yii::$app->{$dbID};

        return $conn->schema->tableNames;
    }
}