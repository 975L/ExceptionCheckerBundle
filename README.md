ExceptionCheckerBundle
======================

ExceptionCheckerBundle will catch Symfony `NotFoundHttpException` and will check if the called url has been deleted or redirected.

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

How to use
==========

Deleted Urls
------------
Create the file `app/Resources/ExceptionChecker/deletedUrls.txt` with the list of all deleted Urls (one url per line), i.e.:

```txt
/url_deleted
/another_url_deleted
/url/deleted/
```
If the url is found, ExceptionCheckerBundle will throw a `GoneHttpException`.

Redirected Urls
---------------
Create the file `app/Resources/ExceptionChecker/redirectedUrls.txt` with the list of all redirected Urls (one url per line). As the redirection can be an url (relative or absolute) or a Route (with or without parameters) its format is a bit tricky.

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

**As the files above are text files, you can add comments, group data by inserting multiples lines breaks, etc.**
