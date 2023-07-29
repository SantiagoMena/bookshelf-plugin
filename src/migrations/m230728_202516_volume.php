<?php

namespace santiagomena\craftbookshelf\migrations;

use Craft;
use craft\db\Migration;
use craft\models\ImageTransform;
use craft\models\Volume;
use yii\db\Exception;
use craft\services\ImageTransforms;

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
        try {
            // Create new volume
            $volume = new Volume([
                'name' => 'Uploads Volume',
                'handle' => 'bookshelfImagesVolume',
                'fsHandle' => 'bookshelffilesystem',
            ]);

            // Save the volume
            Craft::$app->volumes->saveVolume($volume);

            $transform = new ImageTransform([
                'name' => 'Bookshelf Transform',
                'handle' => 'bookshelfTransform',
                'mode' => 'fit',
                'width' => 500,
                'height' => 500,
                'format' => 'jpg',
            ]);

            Craft::$app->getImageTransforms()->saveTransform($transform);
        } catch (\Throwable $e) {
            throw new Exception($e);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m230729_050146_volume cannot be reverted.\n";
        try {
            // Get the volume
            $volume = Craft::$app->volumes->getVolumeByHandle('bookshelfImagesVolume');

            // Delete the volume
            Craft::$app->volumes->deleteVolumeById($volume->id);

            // Delete the transform by its handle
            $transform = Craft::$app->getImageTransforms()->getTransformByHandle('bookshelfTransform');

            if ($transform) {
                Craft::$app->getImageTransforms()->deleteTransform($transform);
            }
        } catch (\Throwable $e) {
            throw new Exception($e);
        }

        return true;

    }
}
