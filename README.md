PHP Single-File-Framework
=========================

PHP-SFF is a minimalistic framework that does just these things:
  - Simplistic class auto-loading (include $className.php)
  - Routing requests with the following format: /controller/action/param0/paramN..
  - CSRF prevention using Origin+Host check for non-GET/HEAD requests
  - Layout rendering
  - REST APIs output encoding using json_encode
