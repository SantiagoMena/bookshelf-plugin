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
![File System](https://github.com/SantiagoMena/bookshelf-plugin/blob/main/imgs/fs.png?raw=true)

4. Make Migrations:
```
php craft migrate/up --plugin=_bookshelf
```

#### 1. Add Books: Users should be able to include books in their bookshelf. Details should include Title, Author, Genre, Publication Year, Cover Image, and a brief description of the book.

![Add Book](https://github.com/SantiagoMena/bookshelf-plugin/blob/main/imgs/create.gif?raw=true)

#### 2. View Collection: The plugin should present users' book collections in a visually appealing and user-friendly interface.

![View Collection](https://github.com/SantiagoMena/bookshelf-plugin/blob/main/imgs/user.gif?raw=true)

#### 3. Share Collection: The plugin should allow users to share their book collections via a unique URL.

![Share Collection](https://github.com/SantiagoMena/bookshelf-plugin/blob/main/imgs/share.gif?raw=true)

#### 4. Manage Wishlist: Users should have the ability to create a wishlist for books they would like to acquire in the future.

![Wishlist](https://github.com/SantiagoMena/bookshelf-plugin/blob/main/imgs/wishlist.gif?raw=true)

