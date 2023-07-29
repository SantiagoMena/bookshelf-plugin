# bookshelf

bookshelf plugin

## Requirements

This plugin requires Craft CMS 4.4.0 or later, and PHP 8.0.2 or later.

## Install
Add to composer.json: 
```
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/SantiagoMena/bookshelf-plugin"
        }
    ]
```

1. Run:
```
$ composer require santiago-mena/craft-bookshelf:dev-main
```

2. Install Plugin:
```
 php craft plugin/install _bookshelf
```
3. Create FileSystem in Craft -> Settings -> Filesystems
```
Name: bookshelffilesystem
Handler: bookshelfFileSystem
Base URL: /uploads
Filesystem Type: Local
Base Path: @webroot/uploads
```

4. Make Migrations:
```
php craft migrate/up --plugin=_bookshelf
```