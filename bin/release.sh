#!/bin/bash

rm -rf svn

svn co https://plugins.svn.wordpress.org/widget-to-show-posts-in-current-category/ svn

cp -Rv assets/screenshots/* svn/assets/
cp -Rv assets/banners/* svn/assets/
cp -Rv assets/icons/* svn/assets/
cp -Rv LICENSE readme.txt widget-to-display-posts-from-current-category.php svn/trunk/
