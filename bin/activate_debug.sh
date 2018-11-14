#!/bin/bash

sed -i "s/^define('WP_DEBUG'.*/define('WP_DEBUG', true);/g" wp-config.php

grep WP_DEBUG_DISPLAY wp-config.php > /dev/null
if [ $? -ne 0 ]; then
  sed -i -e "/'WP_DEBUG'/adefine('WP_DEBUG_DISPLAY', true);" wp-config.php
fi

grep WP_DEBUG_LOG wp-config.php > /dev/null
if [ $? -ne 0 ]; then
  sed -i -e "/'WP_DEBUG'/adefine('WP_DEBUG_LOG', true);" wp-config.php
fi

