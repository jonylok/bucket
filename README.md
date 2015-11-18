bucket
======

This project makes possible you to save your personal notes and search by any of them using a search on top of ElasticSearch. Created using Symfony2 PHP Framework and Bootstrap on front-end.

I created this project for my personal use and to learn how elasticsearch can interact with symfony2 and php.

![Screenshot Bucket](https://raw.githubusercontent.com/rodolfobandeira/bucket/master/screenshot.png)

---

####Requirements:
- ElasticSearch
- PHP >= 5.4
- Linux Server

####Setup:

- `git clone https://github.com/rodolfobandeira/bucket.git`
- `cd bucket`
- `composer install --prefer-dist`
- `mv app/config/parameters.yml.dist app/config/parameters.yml`
- Edit your parameters.yml with your database credentials.
- `php app/console doctrine:schema:create` (To create the database schema. Tables)
- `php app/console server:run` (Running symfony server, but you can configure your own nginx or apache)
- `http://127.0.0.1:8000`

