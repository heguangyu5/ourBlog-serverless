<?php

return array(
    'posts' => array(
        array(
            'id'       => 1,
            'uid'      => 1,
            'category' => 2,
            'title'    => '云招初级PHP工程师培训 - 概览 - edit',
            'content'  => '> 本培训面向刚刚入职云招科技的初级PHP工程师.
通过此培训的工程师才能向云招代码库提交代码.

此培训包含以下内容:

1. 必读书籍列表
2. LAMP环境搭建及配置
3. 通过开发一个简单的blog系统来分析常见的web安全问题
4. 给blog添加新的功能,增加一定的复杂度
5. 重构blog代码,使其可测试 (TDD/PHPUnit)
6. 使用Zend Framework 1重构blog

预期用时:

1. 开发blog 用时1周
2. TDD/PHPUnit 用时1周
3. Zend Framework 用时1周 - edit',
            'create_date' => '2017-02-17 17:30:00'
        ),
        array(
            'id'       => 2,
            'uid'      => 2,
            'category' => 2,
            'title'    => '云招初级PHP工程师培训 - web安全 - 权限',
            'content'  => '为什么一开始就要讲权限? 
访问权限没做好,其它的都白搭.
直接上案例.

[新人写博客，前天刚把删除功能做出来，今天一看被 google 爬虫全删掉了](https://www.v2ex.com/t/336226 "新人写博客，前天刚把删除功能做出来，今天一看被 google 爬虫全删掉了")

为防止这个帖子以后被删掉了,截图备份.

**当然这个案例是最简单的权限判断,在实际应用中往往要比这复杂得多.**

![](/uploads/1/screencapture-v2ex-t-336226-1486605040878.png)',
            'create_date' => '2017-02-17 17:35:00'
        )
    ),
    'tag'      => array(
        array('id' => 1, 'name' => 'PHP'),
        array('id' => 2, 'name' => 'Linux'),
        array('id' => 3, 'name' => 'MySQL')
    ),
    'post_tag' => array(
        array('id' => 3, 'post_id' => 2, 'tag_id' => 2)
    )
);
