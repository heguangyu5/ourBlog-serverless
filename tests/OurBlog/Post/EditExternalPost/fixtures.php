<?php

return array(
    'posts' => array(
        array(
            'id'       => 1,
            'uid'      => 1,
            'category' => 2,
            'title'    => '记录一次Mysql死锁排查过程',
            'content'  => 'http://www.kissyu.org/2017/02/19/%E8%AE%B0%E5%BD%95%E4%B8%80%E6%AC%A1Mysql%E6%AD%BB%E9%94%81%E6%8E%92%E6%9F%A5%E8%BF%87%E7%A8%8B/?hmsr=toutiao.io&utm_medium=toutiao.io&utm_source=toutiao.io',
            'external_post' => 1,
            'create_date'   => '2017-02-20 12:00:00',
            'update_date'   => '2017-02-20 12:00:00',
        )
    ),
    'tag' => array(
        array('id' => 1, 'name' => 'PHP'),
        array('id' => 2, 'name' => 'Linux'),
        array('id' => 3, 'name' => 'MySQL'),
        array('id' => 4, 'name' => '死锁')
    ),
    'post_tag' => array(
        array('id' => 1, 'post_id' => 1, 'tag_id' => 3),
        array('id' => 2, 'post_id' => 1, 'tag_id' => 4)
    )
);
