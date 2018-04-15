# Changelog

v1.3
----
- Added Entity to allow adding, modifying, deleting new url exception (15/04/2018)
- Removed Route `exception_checker_excluded` and replaced by config valur `redirectExcluded` (15/04/2018)
- Removed condition test, in Listener, on `NotFoundHttpException` to test all Exceptions (except `GoneHttpException` as it will be throwed for deleted urls) (15/04/2018)
- Removed use of text files to rely only on database [BC-Break] (15/04/2018)

v1.2
----
- Added excludedUrls functionaltiy (14/04/2018)

v1.1
----
- Added core files (14/04/2018)

v1.0
----
- Initial commit (14/04/2018)