# Changelog

## v5.5

- Removed use of`c975L/ServicesBundle` and replaced by `c975L/SiteBundle` (09/03/2025)
- Removed use of`c975L/IncludeLibraryBundle` (09/03/2025)

## v5.4.1

- Added conditions to avoid errors on $redirectUrl (26/11/2024)
- Replaced setException with setThrowable (26/11/2024)

## v5.4

- Added ->setMaxAge(3600) to controllers (15/09/2024)

## v5.3

- Suppressed spaceless filter as it's deprecated (12/09/2024)

## v5.2.2

- Changed DependencyInjection Extension (10/09/2024)

## v5.2.1

- Added nullable: true for redirectKind (14/03/2024)

## v5.2

- Added type integer for id (14/03/2024)

## v5.1.1

- Added missing void (19/02/2024)

## v5.1

- Converted entity to attributes (15/02/2024)

## v5.0

- Changed to new recomended bundle SF 7 structure (15/02/2024)
- Converted Routes to attributes (15/02/2024)

Upgrading from v5.x? **Check UPGRADE.md**

## v4.1

- Added trailing slash redirection (29/05/2023)

## v4.0.1

- Added TreeBuilder return type (29/05/2023)

## v4.0

- Changed compatibility to PHP 8 (25/07/2022)

Upgrading from v3.x? **Check UPGRADE.md**

## v3.0.3

- Made use of ExceptionEvent (24/07/2022)

## v3.0.2

- Changed composer versions constraints (24/07/2022)

## v3.0.1

- Corrected return types (24/07/2022)

## v3.0

- Changed `localizeddate` to `format_datetime` (11/10/2021)

Upgrading from v2.x? **Check UPGRADE.md**

## v2.6

- Removed versions constraints in composer (03/09/2021)

## v2.5.1

- Cosmetic changes due to Codacy review (04/03/2020)

## v2.5

- Removed use of symplify/easy-coding-standard as abandonned (19/02/2020)

## v2.4

- Made use of `getThrowable()` instead of `getException` as it's deprecated since SF 4.4 (05/02/2020)

## v2.3

- Changed doctrine-bundle version (18/12/2019)

## v2.2.1

- Updated README.md (06/08/2019)

## v2.2

- Made use of apply spaceless (05/08/2019)

## v2.1.2

- Made use of KnpPagnigatorBundle v4 (15/07/2019)

## v2.1.1

- Added "ignored" case (04/07/2019)

## v2.1.0.1

- Changed Github's author reference url (08/04/2019)

## v2.1

- Corrected index values for Entity (15/02/2019)
- Modified Entity to use typehint (15/02/2019)
- Documented the possibility to use `php bin/console make:migration` (15/02/2019)

## v2.0.5.1

- Corrected Dependencyinjection (14/02/2019)

## v2.0.5

- Removed deprecations for @Method (13/02/2019)
- Implemented AstractController instead of Controller (13/02/2019)
- Modified Dependencyinjection rootNode to be not empty (13/02/2019)

## v2.0.4.1

- Corrected code for other charcter case search (25/12/2018)

## v2.0.4

- Added search and redirection for any kind of different character case (25/12/2018)

## v2.0.3

- Added rector to composer dev part (23/12/2018)
- Modified required versions in composer (23/12/2018)
- Made use of `??` (25/12/2018)
- Modified required versions in `composer.json` (25/12/2018)

## v2.0.2

- Corrected `UPGRADE.md` for `php bin/console config:create` (03/12/2018)
- Corrected call for redirected Url (04/12/2018)

## v2.0.1

- Updated `README.md` (31/08/2018)
- Updated `UPGRADE.md` (01/09/2018)
- Updated composer.json (01/09/2018)
- Corrected clone in `duplicate` (04/09/2018)

## v2.0

- Created branch 1.x (31/08/2018)
- Made use of c975L/ConfigBundle (31/08/2018)
- Added Route `exceptionchecker_config` (31/08/2018)
- Removed declaration of parameters in Configuration class as they are end-user parameters and defined in c975L/ConfigBundle (31/08/2018)
- Added `bundle.yaml` (31/08/2018)
- Updated `README.md` (31/08/2018)
- Added `UPGRADE.md` (31/08/2018)

Upgrading from v1.x? **Check UPGRADE.md**

## v1.x

## v1.9.5

- Fixed Voter constants (31/08/2018)

## v1.9.4.2

- Corrected missing comma (27/08/2018)

## v1.9.4.1

- Used a `switch()` for the FormFactory more readable (27/08/2018)

## v1.9.4

- Added ExceptionCheckerFormFactory + Interface (27/08/2018)
- Corrected template call for "create" Route (27/08/2018)

## v1.9.3

- Removed 'true ===' as not needed (25/08/2018)
- Added dependency on "c975l/config-bundle" and "c975l/services-bundle" (26/08/2018)
- Removed un-needed Services (26/08/2018)

## v1.9.2

- Replaced links in dashboard by buttons (25/08/2018)

## v1.9.1

- Removed ID display in dashboard (23/08/2018)
- Added missing type in ExceptionCheckerToolsInterface (23/08/2018)
- Added flash on deletion (23/08/2018)

## v1.9

- Added link to BuyMeCoffee (22/08/2018)
- Added link to apidoc (22/08/2018)
- Removed FQCN (22/08/2018)
- Created Service (22/08/2018)
- Made Controller skinny (22/08/2018)
- Corrected redirection after submission of duplicated ExceptionChecker (22/08/2018)
- Corrected Route `exceptionchecker_create_from_url` rendering (22/08/2018)
- Corrected Translations (22/08/2018)
- Updated `README.md` (22/08/2018)

## v1.8.1

- Renamed things link to `add` to `create` (02/08/2018)
- Ordered in alphabetical Voters constants (02/08/2018)

## v1.8

- Made use of Voters for access rights (01/08/2018)
- Renamed `new` to `add` to avoid using php reserved word (01/08/2018)
- Rename Route `exceptionchecker_add` (ec-add/{kind}) to `exceptionchecker_add_from_url` (01/08/2018)
- Added info about kind of url in add form (01/08/2018)

## v1.7.2

- Injected `AuthorizationCheckerInterface` in Controllers to avoid use of `$this->get()` (30/07/2018)
- Made use of ParamConverter (30/07/2018)

## v1.7.1

- Removed `SubmitType` in ExceptionCheckerType and replaced by adding button in template as it's not a "Best Practice" (21/07/2018)

## v1.7

- Removed `Action` in controller method name as not requested anymore (21/07/2018)
- Corrected meta in `layout.html.twig` (21/07/2018)
- Use of Yoda notation (21/07/2018)

## v1.6.6

- Added test in `forms/new.html.twig` to check if user is defined to not display toolbar, as this form is  also called via "exceptionchecker_add" Route (26/06/2018)
- Corrected `forms/javascript.html.twig` that was hiding full form for certain configurations (26/06/2018)

## v1.6.5

- Hide Redirection field when the kind is not a Redirection (25/06/2018)

## v1.6.4

- Suppressed input field for secret code when signed in (24/06/2018)

## v1.6.3

- Corrected flash message when adding from ec-add (19/06/2018)

## v1.6.2

- Added possibility to change to reidrected kind when using `exceptionchecker_add` Route (26/05/2018)

## v1.6.1.2

- Updated `RouterInterface` (22/05/2018)

## v1.6.1.1

- Removed required in composer.json (22/05/2018)

## v1.6.1

- Updated `README.md` (15/05/2018)

## v1.6

- Added Method to Route `exceptionchecker_add` (15/05/2018)
- Added link to Route `exceptionchecker_add` in log, to be used by simply clicking on it when receiving log email (15/05/2018)

## v1.5.1

- Modified toolbars calls due to modification of c975LToolbarBundle (13/05/2018)

## v1.5

- Corrected the way the `GoneHttpException` is sent (13/05/2018)

## v1.4.2

- Corrected names of supported Exception to full qualified ones (01/05/2018)
- Used `instanceof` in place of `is_a()` to check against supported Exceptions (01/05/2018)

## v1.4.1

- Added missing info in `README.md` (01/05/2018)

## v1.4

- Added possibility to add url (deleted/excluded) directly from url call with secret code (01/05/2018)
- Added missing methods for Routes (01/05/2018)

## v1.3.3

- Changed utf8mb64 to utf8 for table definition (24/04/2018)
- Added supported list of Exceptions (27/04/2018)

## v1.3.2

- Added default timestamp for creation field in `exception_checker.sql` (23/04/2018)
- Corrected `findByUrl` query as result can be multiple (23/04/2018)

## v1.3.1

- Re-ordered variables in `ExceptionCheckerType.php` (16/04/2018)
- Corrected indentation in `ExceptionCheckerType.php` (16/04/2018)

## v1.3

- Added Entity to allow adding, modifying, deleting new url exception (15/04/2018)
- Removed Route `exception_checker_excluded` and replaced by config valur `redirectExcluded` (15/04/2018)
- Removed condition test, in Listener, on `NotFoundHttpException` to test all Exceptions (except `GoneHttpException` as it will be throwed for deleted urls) (15/04/2018)
- Removed use of text files to rely only on database [BC-Break] (15/04/2018)

## v1.2

- Added excludedUrls functionality (14/04/2018)

## v1.1

- Added core files (14/04/2018)

## v1.0

- Initial commit (14/04/2018)
