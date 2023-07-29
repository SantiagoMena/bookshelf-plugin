<?php
namespace santiagomena\craftbookshelf\controllers;

use Craft;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\Db;
use craft\models\EntryType;
use craft\web\Controller;
use craft\web\View;


class WishListController extends Controller
{
    protected int|bool|array $allowAnonymous = false;

    public function actionIndex()
    {
        $user = Craft::$app->getUser()->getIdentity()->id;

        $books = Entry::find()->section('bookshelfBookSection')->bookshelfWishListUsers($user)->all();

        return $this->renderTemplate('_bookshelf/wishlist/wishlist', [
            'books' => $books,
        ]);
    }

    public function actionSwitch(string $uid)
    {
        $user = Craft::$app->getUser()->getIdentity()->id;
        $bookQuery = Entry::find()->section('bookshelfBookSection')->uid($uid);
        $book = $bookQuery->one();

        $existsWish = $bookQuery->bookshelfWishListUsers($user)->exists();
        $wishList = $book->bookshelfWishListUsers->asArray();

        $newWishList = [];
        if($existsWish) {
            foreach ($wishList as $key => $wish) {
                if($wish['id'] !== $user) {
                    $newWishList[] = $wish['id'];
                }
            }
        } else {
            foreach ($wishList as $key => $wish) {
                $newWishList[] = $wish['id'];
            }
            $newWishList[] = $user;
        }

        $book->setFieldValue('bookshelfWishListUsers', $newWishList);

        Craft::$app->elements->saveElement($book);

        return $this->renderTemplate('_bookshelf/book/_books');
    }

}