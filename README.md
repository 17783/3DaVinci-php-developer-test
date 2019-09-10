# 3DaVinci-php-developer-test
Solution for https://github.com/3DaVinci/php-developer-test <br />
Uses https://github.com/KnpLabs/php-github-api
## Install
- clone the project
- run `composer install`
- you need to have MySQL (tested vs 5.7) and PHP (tested vs 7.2)
- you can run `create_db.php` if you don't want to create the db/table manually
- check `config-example.php` for config example
- settings are stored in `config.php` file (you will need to create it)
## Run
run `get-users.php <since> <per_page>` <br />
i.e. `php get-users.php 0 10` would get you the first 10 users to register on github

## CLI parameters
- `since` - github user id to start searching from (note that for since=1 the search results would start from id=2)
- `per_page` - the number of users per request (max 100)
