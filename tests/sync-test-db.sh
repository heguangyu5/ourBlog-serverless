#!/bin/bash
echo "dump..."
mysqldump -uroot -p123456 -d ourblog > /tmp/ourblog.sql
echo "import..."
mysql -uroot -p123456 -e "DROP DATABASE IF EXISTS ourblog_test;CREATE DATABASE ourblog_test DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"
mysql -uroot -p123456 ourblog_test < /tmp/ourblog.sql
echo "done"
