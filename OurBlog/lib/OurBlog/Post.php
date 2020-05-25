<?php

class OurBlog_Post
{
    protected $uid;

    public function __construct($uid)
    {
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
        if (isset($data['externalPost']) && $data['externalPost'] == '1') {
            if ($len > 1000) {
                throw new InvalidArgumentException('external post url too long');
            }
            if (!preg_match('#^https?://[^"]+$#', $data['content'])) {
                throw new InvalidArgumentException('invalid external post url');
            }
        } else {
            if ($len > 64000) {
                throw new InvalidArgumentException('content maxlength is 64000');
            }
            $data['externalPost'] = 0;
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
                $tagRows = OurBlog_Db::getInstance()->fetchAll(
                    'SELECT id, name FROM tag WHERE name IN (?' . str_repeat(', ?', count($tagIdMap) - 1) . ')',
                    array_keys($tagIdMap),
                    PDO::FETCH_OBJ
                );
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

        $db = OurBlog_Db::getInstance();
        $db->beginTransaction();
        try {
            // post
            $db->insert('posts', array(
                'uid'           => $this->uid,
                'category'      => $data['category'],
                'title'         => $data['title'],
                'content'       => $data['content'],
                'external_post' => $data['externalPost'],
                'create_date'   => $createDate,
                'update_date'   => $createDate
            ));
            // tags
            if (isset($data['tagIdMap'])) {
                $postId = $db->lastInsertId();
                // tag
                if ($data['newTags']) {
                    $stmt = $db->prepare('INSERT INTO tag(name) VALUES (?)');
                    foreach ($data['newTags'] as $tag) {
                        $stmt->execute(array($tag));
                        $data['tagIdMap'][$tag] = $db->lastInsertId();
                    }
                }
                // post_tag
                $stmt = $db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                foreach ($data['tagIdMap'] as $tagId) {
                    $stmt->execute(array($postId, $tagId));
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    protected function prepareEditPostData(array $data)
    {
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('missing required key id');
        }
        $data['id'] = OurBlog_Util::DBAIPK($data['id']);
        if (!$data['id']) {
            throw new InvalidArgumentException('invalid id');
        }

        $post = OurBlog_Db::getInstance()->fetchRow(
            'SELECT id, external_post FROM posts WHERE id = ' . $data['id'] . ' AND uid = ' . $this->uid,
            array(),
            PDO::FETCH_OBJ
        );
        if (!$post) {
            throw new InvalidArgumentException('you can only edit your own post');
        }

        $data['externalPost'] = $post->external_post;
        $data = $this->preparePostData($data);

        return $data;
    }

    public function edit(array $data)
    {
        $data = $this->prepareEditPostData($data);
        $db   = OurBlog_Db::getInstance();

        if (isset($data['tagIdMap'])) {
            // 取得post原有的tag
            $postTagIds = $db->fetchCol('SELECT tag_id FROM post_tag WHERE post_id = ' . $data['id']);
            // diff
            $tagIds = array();
            foreach ($data['tagIdMap'] as $tagId) {
                if ($tagId) {
                    $tagIds[] = $tagId;
                }
            }
            $tagIdsToBeAdded   = array_diff($tagIds, $postTagIds);
            $tagIdsToBeDeleted = array_diff($postTagIds, $tagIds);
        }

        $updateDate = date('Y-m-d H:i:s');

        $db->beginTransaction();
        try {
            // post
            $db->update('posts', array(
                'category'    => $data['category'],
                'title'       => $data['title'],
                'content'     => $data['content'],
                'update_date' => $updateDate
            ), 'id = ' . $data['id']);
            // tags
            if (isset($data['tagIdMap'])) {
                // newTags
                if ($data['newTags']) {
                    $stmtTag     = $db->prepare('INSERT INTO tag(name) VALUES (?)');
                    $stmtPostTag = $db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                    foreach ($data['newTags'] as $tag) {
                        $stmtTag->execute(array($tag));
                        $stmtPostTag->execute(array($data['id'], $db->lastInsertId()));
                    }
                }
                // toBeAdded
                if ($tagIdsToBeAdded) {
                    if (!$data['newTags']) {
                        $stmtPostTag = $db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                    }
                    foreach ($tagIdsToBeAdded as $tagId) {
                        $stmtPostTag->execute(array($data['id'], $tagId));
                    }
                }
                // toBeDeleted
                if ($tagIdsToBeDeleted) {
                    $db->exec('DELETE FROM post_tag WHERE post_id = ' . $data['id'] . ' AND tag_id IN (' . implode(',', $tagIdsToBeDeleted) . ')');
                }
            } else {
                $db->exec('DELETE FROM post_tag WHERE post_id = ' . $data['id']);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        $id = OurBlog_Util::DBAIPK($id);
        if (!$id) {
            throw new InvalidArgumentException('invalid id');
        }

        $db = OurBlog_Db::getInstance();
        $id = $db->fetchOne("SELECT id FROM posts WHERE id = $id AND uid = " . $this->uid);
        if (!$id) {
            throw new InvalidArgumentException('you can only delete your own post');
        }

        $db->beginTransaction();
        try {
            $db->exec("DELETE FROM posts WHERE id = $id");
            $db->exec("DELETE FROM post_tag WHERE post_id = $id");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
