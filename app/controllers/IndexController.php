<?php

class IndexController extends OurBlog_Controller_Action
{
    public function indexAction()
    {
        $db     = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select()
                     ->from('posts', array('id', 'title'))
                     ->order('id DESC');

        $category = OurBlog_Util::DBAIPK($this->getQuery('category'));
        if ($category) {
            $select->where('category = ' . $category);
        }

        $this->view->posts = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
    }

    public function postAction()
    {
        try {
            $id = OurBlog_Util::DBAIPK($this->getQuery('id'));
            if (!$id) {
                throw new InvalidArgumentException('invalid id');
            }
            $post = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchRow(
                "SELECT title, content, external_post FROM posts WHERE id = $id",
                array(),
                Zend_Db::FETCH_OBJ
            );
            if (!$post) {
                throw new InvalidArgumentException('post not exist');
            }
        } catch (InvalidArgumentException $e) {
            $this->redirect('/admin/');
        }

        if ($post->external_post) {
            header('Location: ' . $post->content);
            exit;
        }

        $this->view->post = $post;
    }
}
