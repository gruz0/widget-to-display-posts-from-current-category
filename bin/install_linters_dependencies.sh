#!/bin/bash

rm -rf phpcs wpcs

git clone https://github.com/squizlabs/PHP_CodeSniffer.git phpcs
git clone -b master https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git wpcs

cp bin/pre-commit .git/hooks/pre-commit
