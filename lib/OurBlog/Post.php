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
    {}

    public function delete($id)
    {}
}
