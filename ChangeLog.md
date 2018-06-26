# Changelog

v1.6.6
------
- Added test in `forms/new.html.twig` to check if user is defined to not display toolbar, as this form is  also called via "exceptionchecker_add" Route (26/06/2018)
- Corrected `forms/javascript.html.twig` that was hiding full form for certain configurations (26/06/2018)

v1.6.5
------
- Hide Redirection field when the kind is not a Redirection (25/06/2018)

v1.6.4
------
- Suppressed input field for secret code when signed in (24/06/2018)

v1.6.3
------
- Corrected flash message when adding from ec-add (19/06/2018)

v1.6.2
------
- Added possibility to change to reidrected kind when using `exceptionchecker_add` Route (26/05/2018)

v1.6.1.2
--------
- Updated `RouterInterface` (22/05/2018)

v1.6.1.1
--------
- Removed required in composer.json (22/05/2018)

v1.6.1
------
- Updated `README.md` (15/05/2018)

v1.6
----
- Added Method to Route `exceptionchecker_add` (15/05/2018)
- Added link to Route `exceptionchecker_add` in log, to be used by simply clicking on it when receiving log email (15/05/2018)

v1.5.1
------
- Modified toolbars calls due to modification of c975LToolbarBundle (13/05/2018)

v1.5
----
- Corrected the way the `GoneHttpException` is sent (13/05/2018)

v1.4.2
------
- Corrected names of supported Exception to full qualified ones (01/05/2018)
- Used `instanceof` in place of `is_a()` to check against supported Exceptions (01/05/2018)

v1.4.1
------
- Added missing info in `README.md` (01/05/2018)

v1.4
----
- Added possibility to add url (deleted/excluded) directly from url call with secret code (01/05/2018)
- Added missing methods for Routes (01/05/2018)

v1.3.3
------
- Changed utf8mb64 to utf8 for table definition (24/04/2018)
- Added supported list of Exceptions (27/04/2018)

v1.3.2
------
- Added default timestamp for creation field in `exception_checker.sql` (23/04/2018)
- Corrected `findByUrl` query as result can be multiple (23/04/2018)

v1.3.1
------
- Re-ordered variables in `ExceptionCheckerType.php` (16/04/2018)
- Corrected indentation in `ExceptionCheckerType.php` (16/04/2018)

v1.3
----
- Added Entity to allow adding, modifying, deleting new url exception (15/04/2018)
- Removed Route `exception_checker_excluded` and replaced by config valur `redirectExcluded` (15/04/2018)
- Removed condition test, in Listener, on `NotFoundHttpException` to test all Exceptions (except `GoneHttpException` as it will be throwed for deleted urls) (15/04/2018)
- Removed use of text files to rely only on database [BC-Break] (15/04/2018)

v1.2
----
- Added excludedUrls functionality (14/04/2018)

v1.1
----
- Added core files (14/04/2018)

v1.0
----
- Initial commit (14/04/2018)