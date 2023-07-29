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


class PublicBookshelfController extends Controller
{
    protected int|bool|array $allowAnonymous = true;
    public function actionIndex(string $username)
    {
        $author = User::find()->where(['username' => $username])->one();

        $books = [];
        if($author) {
            $books = Entry::find()->section('bookshelfBookSection')->authorId($author->id);
        }

        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $view = Craft::$app->view->renderTemplate('_bookshelf/book/indexPublic', [
            'books' => $books
        ]);
        Craft::$app->view->setTemplateMode($oldMode);
        return $view;
    }

}