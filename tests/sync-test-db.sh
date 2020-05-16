#!/bin/bash
echo "dump..."
sudo mysqldump -d ourblog > /tmp/ourblog.sql
echo "import..."
sudo mysql -e "DROP DATABASE IF EXISTS ourblog_test;CREATE DATABASE ourblog_test DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"
sudo mysql ourblog_test < /tmp/ourblog.sql
echo "done"
