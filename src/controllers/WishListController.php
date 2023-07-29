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

}