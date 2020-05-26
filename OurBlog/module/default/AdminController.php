<?php

class AdminController extends OurBlog_Controller_Action_PostLogin
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

        $where = array('uid = ?');
        $bind  = array($this->uid);
        if ($category) {
            $where[] = 'category = ?';
            $bind[]  = $category;
        }
        if ($keywords) {
            $where[] = 'title LIKE ?';
            $bind[]  = '%' . $keywords . '%';
        }
        $where = implode(' AND ', $where);

        $sql  = "SELECT id, title FROM posts WHERE $where ORDER BY id DESC LIMIT $offset, $pageSize";
        $rows = OurBlog_Db::getInstance()->fetchAll($sql, $bind);
        return $this->response($rows);
    }

    public function addAction()
    {
        try {
            $post = new OurBlog_Post($this->uid);
            $post->add($_POST);
            return $this->response(null);
        } catch (InvalidArgumentException $e) {
            return $this->invalidParams($e->getMessage());
        } catch (Exception $e) {
            return $this->errorOccurred();
        }
    }

    public function postAction()
    {
        $id = OurBlog_Util::DBAIPK($this->getQuery('id'));
        if (!$id) {
            return $this->invalidParams();
        }
        $post = OurBlog_Db::getInstance()->fetchRow(
            "SELECT id, category, title, content, external_post FROM posts WHERE id = $id AND uid = " . $this->uid
        );
        if (!$post) {
            return $this->notFound404();
        }
        return $this->response($post);
    }

    public function editAction()
    {
        try {
            $post = new OurBlog_Post($this->uid);
            $post->edit($_POST);
            return $this->response(null);
        } catch (InvalidArgumentException $e) {
            return $this->invalidParams();
        } catch (Exception $e) {
            return $this->errorOccurred();
        }
    }

    public function deleteAction()
    {
        try {
            $post = new OurBlog_Post($this->uid);
            $post->delete($this->getQuery('id'));
            return $this->response(null);
        } catch (InvalidArgumentException $e) {
            return $this->invalidParams();
        } catch (Exception $e) {
            return $this->errorOccurred();
        }
    }
}
