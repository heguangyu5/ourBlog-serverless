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
        $requiredKeys = array(
            // key => required
            'category' => true,
            'title'    => true,
            'content'  => true,
            'tags'     => false
        );
        foreach ($requiredKeys as $key => $required) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("missing required key $key");
            }
            $data[$key] = trim($data[$key]);
            if ($required && empty($data[$key])) {
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
        // tags
        if ($data['tags']) {
            $len = mb_strlen($data['tags']);
            if ($len > 600) {
                throw new InvalidArgumentException('tags too long');
            }
            $tags = explode(',', $data['tags']);
            if (count($tags) > 10) {
                throw new InvalidArgumentException('too many tags');
            }
            $tagIdMap = array();
            foreach ($tags as $idx => $tag) {
                $tag = trim($tag);
                $len = mb_strlen($tag, 'UTF-8');
                if ($len > 50) {
                    throw new InvalidArgumentException('tag too long');
                }
                if ($len == 0) {
                    continue;
                }
                $tagIdMap[$tag] = 0;
            }
            unset($data['tags']);
            if ($tagIdMap) {
                // filter out exist tags
                $stmt = $this->db->prepare('SELECT id, name FROM tag WHERE name IN (?' . str_repeat(', ?', count($tagIdMap) - 1) . ')');
                $stmt->execute(array_keys($tagIdMap));
                $tagRows = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach ($tagRows as $row) {
                    $tagIdMap[$row->name] = $row->id;
                }
                $data['tagIdMap'] = $tagIdMap;
                // filter out new tags
                $newTags = array();
                foreach ($tagIdMap as $tag => $tagId) {
                    if (!$tagId) {
                        $newTags[] = $tag;
                    }
                }
                $data['newTags'] = $newTags;
            }
        }

        return $data;
    }

    public function add(array $data)
    {
        $data = $this->preparePostData($data);

        $createDate = date('Y-m-d H:i:s');

        $this->db->beginTransaction();
        try {
            // post
            $stmt = $this->db->prepare('INSERT INTO posts(uid, category, title, content, create_date, update_date) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute(array(
                $this->uid,
                $data['category'],
                $data['title'],
                $data['content'],
                $createDate,
                $createDate
            ));
            // tags
            if (isset($data['tagIdMap'])) {
                $postId = $this->db->lastInsertId();
                // tag
                if ($data['newTags']) {
                    $stmt = $this->db->prepare('INSERT INTO tag(name) VALUES (?)');
                    foreach ($data['newTags'] as $tag) {
                        $stmt->execute(array($tag));
                        $data['tagIdMap'][$tag] = $this->db->lastInsertId();
                    }
                }
                // post_tag
                $stmt = $this->db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                foreach ($data['tagIdMap'] as $tagId) {
                    $stmt->execute(array($postId, $tagId));
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
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
