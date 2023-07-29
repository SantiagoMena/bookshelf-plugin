<?php

namespace santiagomena\craftbookshelf\fs;

use Craft;
use craft\base\Fs;
use craft\fs\Local;
use craft\models\FsListing;

/**
 * Files filesystem type
 */
class Files extends Local
{
    public static function displayName(): string
    {
        return Craft::t('_bookshelf', 'Files');
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            // ...
        ]);
    }

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }
}
