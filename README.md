ExceptionCheckerBundle
======================

**Imagine...** There's a bad request on your Symfony website, which leads Monolog to send you an email (action level set to error). Then, you simply just delete the email OR set the action level to critical (and then lose all warnings about real links problems that should be solved!).

With `ExceptionCheckerBundle`, excluding and redirect to your homepage, or wherever you want, a bad url call is **as easy as clicking on a link!**.

A link is provided in the log file sent by email, this link will lead to a form where you need to enter your defined secret code (secret code is not needed if you're logged in), and that's it! The url is excluded and redirected!

ExceptionCheckerBundle does the following:

- Catches Symfony's exceptions and checks if the called url has been deleted or redirected or is an excluded one,
- Allows to use **wildcards** to match urls,
- Provides forms to create, modify, duplicate, delete the ExceptionCheckers,
- Will reduce errors trigerred as if urls are registered, they will not be exception anymore (except for deleted urls which will throw GoneHttpException),
- Integrates with your web design,
- Creates new ExceptionChecker with a simple url call (+ secret code or already signed in), provided in the log file sent by email,

This Bundle relies on the use of [jQuery](https://jquery.com/) and [Bootstrap](http://getbootstrap.com/).

[ExceptionCheckerBundle dedicated web page](https://975l.com/en/pages/exception-checker-bundle).

[ExceptionCheckerBundle API documentation](https://975l.com/apidoc/c975L/ExceptionCheckerBundle.html).

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

Step 3: Configure the Bundles
-----------------------------
Check [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) for its specific configuration.

v2.0+ of c975LExceptionCheckerBundle uses [c975L/ConfigBundle](https://github.com/975L/ConfigBundle) to manage configuration parameters. Use the Route "/exception-checker/config" with the proper user role to modify them.

**Upgrading from v1.x? Check UPGRADE.md.**

Step 4: Enable the Routes
-------------------------
Then, enable the routes by adding them to the `app/config/routing.yml` file of your project:

```yml
c975_l_exception_checker:
    resource: "@c975LExceptionCheckerBundle/Controller/"
    type:     annotation
    prefix: /
    #Multilingual website use the following
    #prefix: /{_locale}
    #defaults:   { _locale: '%locale%' }
    #requirements:
    #    _locale: en|fr|es
```

Step 5: Create MySql table
--------------------------
Use `/Resources/sql/exception_checker.sql` to create the table `exception_checker`. The `DROP TABLE` is commented to avoid dropping by mistake.

**As a bonus some well known tested links to be excluded are provided in the sql file, simply un-comment this part before running the sql file.**

Step 6: Integration with your website
-------------------------------------
It is strongly recommended to use the [Override Templates from Third-Party Bundles feature](http://symfony.com/doc/current/templating/overriding.html) to integrate fully with your site.

For this, simply, create the following structure `app/Resources/c975LExceptionCheckerBundle/views/` in your app and then duplicate the file `layout.html.twig` in it, to override the existing Bundle file, then apply your needed changes, such as language, etc.

In `layout.html.twig`, it will mainly consist to extend your layout and define specific variables, i.e. :
```twig
{% extends 'layout.html.twig' %}

{# Defines specific variables #}
{% set title = 'ExceptionChecker (' ~ title ~ ')' %}

{% block content %}
    {% block exceptionchecker_content %}
    {% endblock %}
{% endblock %}
```

How to use
==========
Use the Route `exceptionchecker_dashboard` (url: "/exception-checker/dashboard") to access Dashboard. Then you can add urls.

Matching of urls is made with `LIKE url%` so it means that if the searched url is the beginning of a checked url, the match will be met, i.e. `/wp-login.php` will be matched by `/wp-login`.

**You can use wildcards, to match a set of urls, by adding `*` at the end of the url, i.e. url `/wp*` will be matched by `/wp-login`, `/wp-admin`, `/wp-login.php`, etc.**

Add deleted or excluded via url call
------------------------------------
This bundle provides a great feature to add deleted|excluded url easily and fastly!

Imagine, you receive a new email (from Monolog i.e.), saying "No Route found for...". To add this url to ExceptionCheckerBundle, simply indicate {kind} (deleted or excluded) and copy/paste the requested {url} in `http://example.com/ec-add/{kind}?u={url}` (this will use the Route `exceptionchecker_add`). Type your secret code (if you're not signed in) and the url is added!

i.e. `http://example.com/ec-add/excluded?u=/js/mage/cookies.js`.

**You need to have set `exceptionCheckerSecret` in `app/config/parameters.yml` to be able to add urls without having to sign in.**

Deleted Urls
------------
If the url is found, ExceptionCheckerBundle will throw a `GoneHttpException`.

Redirected Urls
---------------
Redirected Urls can redirect to an `Asset`, a `Route` or an `Url`. You will need to enter the following type of data

- Asset: path to your asset, i.e. `/path_to_asset`
- Url: relative or absolute url, i.e. `http://example.com`
- Route without parameters: `route_name`
- Route with one parameter: `route_name['param_key' => 'param_value']`
- Route with multiple parameters: `route_name['param_key' => 'param_value', 'another_param_key' => 'another_param_value']`

If the url is found, ExceptionCheckerBundle will update Event Response to redirect to the defined url.

Excluded Urls
-------------
Excluded Urls are the unwanted 404 HTTP errors, like when an attacker scans your app for some well-known application paths (e.g. /phpmyadmin).

If the url is found, ExceptionCheckerBundle will redirect to the Route defined in the config value `redirectExcluded`. We advise you to redirect to your homepage.

ExceptionCheckerBundle can easily replace `excluded_404s` placed in Monolog to avoid being flooded by too many 404 errors, so you can remove this option from your `config_prod.yml`.

**If this project help you to reduce time to develop, you can [buy me a coffee](https://www.buymeacoffee.com/LaurentMarquet) :)**