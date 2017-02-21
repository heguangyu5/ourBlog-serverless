<?php

class AdminController extends OurBlog_Controller_Action_PostLogin
{
    public function indexAction()
    {}

    public function addAction()
    {
        if ($_POST) {
            try {
                OurBlog_Util::killCSRF();
                $post = new OurBlog_Post($this->uid);
                $post->add($_POST);
                $this->redirect('/admin/');
            } catch (InvalidArgumentException $e) {
                $this->view->error = '参数不对';
            } catch (Exception $e) {
                $this->view->error = 'Server Error';
            }
        }
    }

    public function editAction()
    {
        if ($_POST) {
            try {
                OurBlog_Util::killCSRF();
                $post = new OurBlog_Post($this->uid);
                $post->edit($_POST);
                $this->redirect('/admin/');
            } catch (InvalidArgumentException $e) {
                $this->error = '参数不对';
            } catch (Exception $e) {
                $this->error = 'Server Error';
            }
        }

        try {
            $id = OurBlog_Util::DBAIPK($this->getQuery('id'));
            if (!$id) {
                throw new InvalidArgumentException('invalid id');
            }
            $post = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchRow(
                "SELECT id, category, title, content, external_post FROM posts WHERE id = $id AND uid = " . $this->uid,
                array(),
                Zend_Db::FETCH_OBJ
            );
            if (!$post) {
                throw new InvalidArgumentException('post not exist');
            }
        } catch (InvalidArgumentException $e) {
            $this->redirect('/admin/');
        }

        $this->view->post = $post;
    }

    public function deleteAction()
    {
        try {
            OurBlog_Util::killCSRF();
            $post = new OurBlog_Post($this->uid);
            $post->delete($this->getQuery('id'));
            $this->redirect('/admin/');
        } catch (InvalidArgumentException $e) {
            die('参数不对');
        } catch (Exception $e) {
            die('Server Error');
        }
    }
}
