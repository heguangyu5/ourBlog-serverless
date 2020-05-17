<?php

class Zend_View_Helper_CategoryList extends Zend_View_Helper_Abstract
{
    public function categoryList($default = null)
    {
        $rows = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchAll('SELECT id, name FROM category');

        $categoryList = array();
        foreach ($rows as $row) {
            $categoryList[$row['id']] = $row['name'];
        }

        return $this->view->ourFormSelect(
            'category',
            $default,
            array('class' => 'form-control'),
            $categoryList,
            true,
            $default ? null : '<option value="0">-所属栏目-</option>'
        );
    }
}
