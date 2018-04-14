ExceptionCheckerBundle
======================

ExceptionCheckerBundle will catch Symfony `NotFoundHttpException` and will check if the called url has been deleted or redirected or is an excluded one.

[ExceptionChecker Bundle dedicated web page](https://975l.com/en/pages/exception-checker-bundle).

Bundle installation
===================

Step 1: Download the Bundle
---------------------------
Use [Composer](https://getcomposer.org) to install the library
```bash
    composer require c975l/exceptionchecker-bundle
```

Step 2: Enable the Bundle
-------------------------
Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new c975L\ExceptionCheckerBundle\c975LExceptionCheckerBundle(),
        ];
    }
}
```

Step 3: Enable the Routes
-------------------------
Then, enable the routes by adding them to the `app/config/routing.yml` file of your project:

```yml
c975_l_exception_checker:
    resource: "@c975LExceptionCheckerBundle/Controller/"
    type:     annotation
```

How to use
==========

Deleted Urls
------------
Create the file `app/ExceptionChecker/deletedUrls.txt` with the list of all deleted Urls (one url per line), i.e.:

```txt
/url_deleted
/another_url_deleted
/url/deleted/
```
If the url is found, ExceptionCheckerBundle will throw a `GoneHttpException`.

Redirected Urls
---------------
Create the file `app/ExceptionChecker/redirectedUrls.txt` with the list of all redirected Urls (one url per line). As the redirection can be an url (relative or absolute) or a Route (with or without parameters) its format is a bit tricky.

The url must be ended by `#` (you can add one space before and after the # for better readability) and followed by either `Asset:`, `Route:` or `Url:`, i.e.:

```txt
ASSET
/asset_redirected # Asset:/path_to_asset

ROUTE
/url_redirected # Route:new_route_name
/another_url_redirected # Route:new_route_name_with_param['param_key' => 'param_value']
/still_another_url_redirected # Route:new_route_name_with_multiple_params['param_key' => 'param_value', 'another_param_key' => 'another_param_value']

URL
/again_another_url_redirected # Url:http://absolute/url
```
If the url is found, ExceptionCheckerBundle will update Event Response to redirect to the new url.

Excluded Urls
-------------
Excluded Urls are the unwanted 404 HTTP errors, like when an attacker scans your app for some well-known application paths (e.g. /phpmyadmin).

**We advise you to place this file in `.gitgnore` (if you're using Git) to be able to update it easily without having to commit and push.**

This function can replace the `excluded_404s` to place in Monolog to avoid being flooded by too many 404 errors.

Create the file `app//ExceptionChecker/excludedUrls.txt` with the list of all excluded Urls (one url per line), i.e.:

```txt
/apple-app-site-association
/admin
/phpmyadmin
/wordpress
/wp-admin
```
If the url is found, ExceptionCheckerBundle will redirect to the Route `exception_checker_excluded`. You can use the [Override Templates from Third-Party Bundles feature](http://symfony.com/doc/current/templating/overriding.html) to integrate fully with your site.

For this, simply, create the following structure `app/Resources/c975LExceptionCheckerBundle/views/` in your app and then duplicate the file `layout.html.twig` and `pages/excluded.html.twig` in it, to override the existing Bundle files.

**But we advise to NOT override files and let the minimalist template displayed, as if this Route is accessed, it's because you have set the specific url to be excluded and then they don't need more...**

In `layout.html.twig`, it will mainly consist to extend your layout and define specific variables, i.e. :
```twig
{% extends 'layout.html.twig' %}

{# Defines specific variables #}
{% set title = 'PageEdit (' ~ title ~ ')' %}

{% block content %}
    {% block exceptionchecker_content %}
    {% endblock %}
{% endblock %}
```

**As the files above are text files, you can add comments, group data by inserting multiples lines breaks, etc.**
