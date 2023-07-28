<?php
namespace santiagomena\craftbookshelf\controllers;

use Craft;
use craft\web\Controller;

class BookController extends Controller
{

    public function actionIndex()
    {
        echo "INDEX";
    }

    public function actionAdd()
    {
        $this->requirePostRequest();

        // Validate input and save the book
    }
}