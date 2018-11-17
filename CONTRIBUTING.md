This plugin is an open source project and we would love you to help us make it better.

## Reporting Issues

A well formatted issue is appreciated, and goes a long way in helping us help you.

* Make sure you have a [GitHub account](https://github.com/signup/free)
* Submit a [Github issue](https://github.com/gruz0/widget-to-display-posts-from-current-category/issues/new) by:
  * Clearly describing the issue
  * Provide a descriptive summary
  * Explain the expected behavior
  * Explain the actual behavior
  * Provide steps to reproduce the actual behavior
  * Put application stacktrace as text (in a [Gist](https://gist.github.com) for bonus points)
  * Any relevant stack traces

If you provide code, make sure it is formatted with the triple backticks (\`\`\`).

At this point, we'd love to tell you how long it will take for us to respond,
but we just don't know.

## Pull requests

We accept pull requests to plugin for:

* Fixing bugs
* Adding new features

Not all features proposed will be added but we are open to having a conversation
about a feature you are championing.

Here's a quick guide:

1. Fork the repo.
2. Create a new branch and make your changes.
3. Push to your fork and submit a pull request. For more information, see
[Github's pull request help section](https://help.github.com/articles/using-pull-requests/).

At this point you're waiting on us.

Expect a conversation regarding your pull request, questions, clarifications, and so on.

## How to run plugin inside Docker environment

Ensure that you have installed Docker and docker-compose in your Operating System.

Then use following commands:

1. `make dockerize` – to run WordPress instance on [http://localhost:8000/](http://localhost:8000/)
2. `make shell` – to open `bash` inside Docker container

## How to cleanup database

Simply delete the `.data` directory from the root directory.

## How to activate Debug Mode

Inside container run the script `/usr/local/bin/activate_debug`.

## How to write debug logs

Use custom function `write_log( $smth );` from the plugin
and look at the `/wp-content/debug.log` inside the container.

## How to install linters locally

Run `make install_linters` inside repo's directory.

What it does:

1. Install [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) to `phpcs` directory
2. Install [WordPress Coding Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards) to `wpcs` directory
3. Install `bin/pre-commit` Git hook to `.git/hooks/pre-commit`.

On each `git commit` command `phpcs` will started automatically! :-)

Report's example output:

```bash
$ git commit
[PHP Style][Info]: Checking PHP Style

FILE: ...rrent-category/widget-to-display-posts-from-current-category.php
----------------------------------------------------------------------
FOUND 1 ERROR AFFECTING 1 LINE
----------------------------------------------------------------------
 3 | ERROR | You must use "/**" style comments for a file comment
   |       | (Squiz.Commenting.FileComment.WrongStyle)
----------------------------------------------------------------------

Time: 165ms; Memory: 8Mb

[PHP Style][Error]: Fix the issues and commit again
```
