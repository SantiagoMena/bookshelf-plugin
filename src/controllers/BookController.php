<?php
namespace santiagomena\craftbookshelf\controllers;

use Craft;
use craft\db\Query;
use craft\db\Table;
use craft\elements\User;
use craft\helpers\Db;
use craft\models\EntryType;
use craft\web\Controller;
use craft\web\View;


class BookController extends Controller
{
    public function actionIndex()
    {
        return $this->renderTemplate('_bookshelf/book/index');
    }

    public function actionPublic(string $username)
    {
        $author = User::find()->where(['username' => $username])->one();

        $books = [];
        if($author) {
            $books = \craft\elements\Entry::find()->section('bookshelfBookSection')->authorId($author->id);
        }

        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        return Craft::$app->view->renderTemplate('_bookshelf/book/indexPublic', [
            'books' => $books
        ]);
    }

    public function actionAdd()
    {
        $this->requirePostRequest();

        // Validate input and save the book
    }
}