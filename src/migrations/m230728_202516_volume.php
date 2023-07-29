<?php

namespace santiagomena\craftbookshelf\migrations;

use Craft;
use craft\db\Migration;
use craft\fs\Local;
use craft\helpers\Json;
use craft\models\Volume;
use santiagomena\craftbookshelf\fs\Images;

/**
 * m230729_050146_volume migration.
 */
class m230728_202516_volume extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Create new volume
        $volume = new Volume([
            'name' => 'Uploads Volume',
            'handle' => 'bookshelfImagesVolume',
            'fs' => Images::class,
        ]);

        // Save the volume
        Craft::$app->volumes->saveVolume($volume);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m230729_050146_volume cannot be reverted.\n";

        // Get the volume
        $volume = Craft::$app->volumes->getVolumeByHandle('bookshelfImagesVolume');

        // Delete the volume
        Craft::$app->volumes->deleteVolumeById($volume->id);
    }
}
