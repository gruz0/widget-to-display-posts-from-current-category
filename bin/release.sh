#!/bin/bash

svn co https://plugins.svn.wordpress.org/widget-to-show-posts-in-current-category/ svn
rm -rf svn/assets/*
rm -rf svn/trunk/*

cp -Rv assets/screenshots/* svn/assets/
cp -Rv LICENSE readme.txt widget-to-display-posts-from-current-category.php svn/trunk/
