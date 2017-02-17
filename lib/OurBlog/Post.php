<?php

class OurBlog_Post
{
    protected $db;
    protected $uid;

    public function __construct(PDO $db, $uid)
    {
        $this->db  = $db;
        $this->uid = $uid;
    }

    protected function preparePostData(array $data)
    {
        $requiredKeys = array('category', 'title', 'content');
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("missing required key $key");
            }
            $data[$key] = trim($data[$key]);
            if (empty($data[$key])) {
                throw new InvalidArgumentException("$key required");
            }
        }
        // category
        $data['category'] = OurBlog_Util::DBAIPK($data['category']);
        if (!$data['category']) {
            throw new InvalidArgumentException('invalid category');
        }
        // title
        $len = mb_strlen($data['title'], 'UTF-8');
        if ($len > 500) {
            throw new InvalidArgumentException('title maxlength is 500');
        }
        // content
        $len = strlen($data['content']);
        if ($len > 64000) {
            throw new InvalidArgumentException('content maxlength is 64000');
        }

        return $data;
    }

    public function add(array $data)
    {
        $data = $this->preparePostData($data);

        $createDate = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare('INSERT INTO posts(uid, category, title, content, create_date, update_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute(array(
            $this->uid,
            $data['category'],
            $data['title'],
            $data['content'],
            $createDate,
            $createDate
        ));
    }

    public function edit(array $data)
    {
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('missing required key id');
        }
        $id = OurBlog_Util::DBAIPK($data['id']);
        if (!$id) {
            throw new InvalidArgumentException('invalid id');
        }

        $data = $this->preparePostData($data);

        $updateDate = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare("UPDATE posts SET category = ?, title = ?, content = ?, update_date = ? WHERE id = ? AND uid = ?");
        $stmt->execute(array(
            $data['category'],
            $data['title'],
            $data['content'],
            $updateDate,
            $id,
            $this->uid
        ));
    }

    public function delete($id)
    {
        $id = OurBlog_Util::DBAIPK($id);
        if (!$id) {
            throw new InvalidArgumentException('invalid id');
        }

        $this->db->exec("DELETE FROM posts WHERE id = $id AND uid = " . $this->uid);
    }
}
