# 3DaVinci-php-developer-test
Solution for https://github.com/3DaVinci/php-developer-test
## Install
- just download the files.
- you need to have MySQL (tested vs 5.7) and PHP (tested vs 7.2)
- you can run `create_db.php` if you don't want to create the db/table manually
- check `config.php` for config parameters
## Run
run `get-users.php`

## Config parameters
- `since` - github user id to start searching from (note that for since=1 the search results would start from id=2)
- `per_page` - the number of users per request (max 100)
