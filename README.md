PHP Single-File-Framework
=========================

PHP-SFF is a minimalistic framework that does just these things:
  - Simplistic class auto-loading (include $className.php)
  - Routing requests with the following format: /controller/action/param0/paramN..
  - CSRF prevention using Origin+Host check for non-GET/HEAD requests
  - Layout rendering
  - REST APIs output encoding using json_encode

Using
=====

1. Fork this repository
```shell
git clone https://github.com/rodrigovr/php-sff.git my-project-name
cd my-project-name
```

2. Start PHP's bultin server

```shell
cd public
php -S 127.0.0.1:8888 index.php
```

3. Browse to http://localhost:8888/
```shell
xdg-open http://localhost:8888/
```

4. Understand what the framework is doing. It's less than 100 lines of code to read!
  - public/index.php
  - components/Application.php
  - components/SiteController.php
  - views/layout.php
  - views/error.php
  - views/index.php

Tips
====

1. Create new controller as needed, don't put everything on SiteController.php.
2. You can use subfolders for views and then $app->render('subfolder/view');
3. You can have multiple layouts and change then inside the action handler, just $app->layout = 'alternative-layout-file';
