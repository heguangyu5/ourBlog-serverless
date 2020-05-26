<?php

class IndexController extends OurBlog_Controller_Action
{
    public function indexAction()
    {
        // category
        $category = OurBlog_Util::DBAIPK($this->getQuery('category'));
        // keywords
        $keywords = trim($this->getQuery('keywords'));
        $len      = mb_strlen($keywords, 'UTF-8');
        if ($len > 100) {
            $keywords = mb_substr($keywords, 0, 100);
        }
        // page
        $pageSize = 20;
        $page     = OurBlog_Util::DBAIPK($this->getQuery('page'));
        $offset   = $page ? ($page - 1) * $pageSize : 0;

        $where = array();
        $bind  = array();
        if ($category) {
            $where[] = 'category = ?';
            $bind[]  = $category;
        }
        if ($keywords) {
            $where[] = 'title LIKE ?';
            $bind[]  = '%' . $keywords . '%';
        }
        if ($where) {
            $where = 'WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $sql  = "SELECT id, title FROM posts $where ORDER BY id DESC LIMIT $offset, $pageSize";
        $rows = OurBlog_Db::getInstance()->fetchAll($sql, $bind);
        return $this->response($rows);
    }

    public function postAction()
    {
        $id = OurBlog_Util::DBAIPK($this->getQuery('id'));
        if (!$id) {
            return $this->invalidParams();
        }
        $post = OurBlog_Db::getInstance()->fetchRow(
            "SELECT title, content, external_post FROM posts WHERE id = $id"
        );
        if (!$post) {
            return $this->notFound404();
        }
        return $this->response($post);
    }

    public function categoriesAction()
    {
        $rows = OurBlog_Db::getInstance()->fetchAll('SELECT id, name FROM category');
        $categories = array();
        foreach ($rows as $row) {
            $categories[$row['id']] = $row['name'];
        }
        return $this->response($categories);
    }
}
