# Changelog

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