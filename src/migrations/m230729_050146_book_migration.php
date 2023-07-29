<?php

namespace santiagomena\craftbookshelf\migrations;

use Craft;
use craft\db\Migration;
use craft\fieldlayoutelements\CustomField;
use craft\fields\Assets;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\Users;
use craft\models\FieldGroup;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\EntryType;
use craft\models\Section_SiteSettings;
use craft\models\Volume;
use yii\db\Exception;

/**
 * m230728_202516_book_migration migration.
 */
class m230729_050146_book_migration extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Don’t make the same config changes twice
//        if (Craft::$app->projectConfig->get('plugins._bookshelf', true) === null) {
            try {
//                1. Make FieldGroup
                $fieldGroup = new FieldGroup([
                    'name' => 'bookshelfBooksGroup',
                ]);
                $successFieldGroup = Craft::$app->fields->saveGroup($fieldGroup);

//                2. Make Fields
                $groups = Craft::$app->fields->getAllGroups();

                foreach ($groups as $group) {
                    if($group->name === 'bookshelfBooksGroup') {
                        $fieldGroupBooks = $group;
                    }
                }

                if($fieldGroupBooks) {
//                  Make Fields
//                  1. Make Author
                    $fieldAuthor = new PlainText([
                        'handle' => 'bookshelfAuthorField',
                        'name' => 'Book Author',
                        'groupId' => $fieldGroupBooks->id
                    ]);
                    Craft::$app->fields->saveField($fieldAuthor);

//                  2. Make Genre
                    $fieldGenre = new PlainText([
                        'handle' => 'bookshelfGenreField',
                        'name' => 'Genre',
                        'groupId' => $fieldGroupBooks->id
                    ]);
                    Craft::$app->fields->saveField($fieldGenre);

//                  3. Make Publication Year
                    $fieldPublicationYear = new Number([
                        'handle' => 'bookshelfPublicationYearField',
                        'name' => 'Publication Year',
                        'groupId' => $fieldGroupBooks->id,
                        'max' => date('Y'),
                    ]);
                    Craft::$app->fields->saveField($fieldPublicationYear);

//                  4. Make Publication Cover Image
                    $fieldCoverImage = new Assets([
                        'handle' => 'bookshelfCoverImageField',
                        'name' => 'Cover Image',
                        'groupId' => $fieldGroupBooks->id,
                        'restrictLocation' => true,
                        'restrictedLocationSubpath' => '/uploads/{author.username}/',
                        'restrictFiles' => true,
                        'allowedKinds' => ['image'],
                        'allowUploads' => 1,
                        'minRelations' => 0,
                        'maxRelations' => 1,
                        'viewMode' => 'large',
                        'previewMode' => 'full',
                        'restrictedLocationSource' => "volume:".Craft::$app->volumes->getVolumeByHandle('bookshelfImagesVolume')->uid,
                    ]);
                    Craft::$app->fields->saveField($fieldCoverImage);

//                  5. Make Publication Brief Description
                    $fieldBriefDescription = new PlainText([
                        'handle' => 'bookshelfBriefDescriptionField',
                        'name' => 'Brief Description',
                        'groupId' => $fieldGroupBooks->id
                    ]);
                    Craft::$app->fields->saveField($fieldBriefDescription);


//                  6. Make Wish List Users
                    $wishListUsers = new Users([
                        'handle' => 'bookshelfWishListUsers',
                        'name' => 'Wish List Users',
                        'groupId' => $fieldGroupBooks->id
                    ]);
                    Craft::$app->fields->saveField($wishListUsers);
                }

//              3. Create Section
                $section = new Section([
                    'name' => 'bookshelfBookSection',
                    'handle' => 'bookshelfBookSection',
                    'type' => Section::TYPE_CHANNEL,
                    'siteSettings' => [
                        new Section_SiteSettings([
                            'siteId' => Craft::$app->sites->getPrimarySite()->id,
                            'enabledByDefault' => true,
                            'hasUrls' => true,
                            'uriFormat' => 'book/{slug}',
                            'template' => '@santiagomena/craftbookshelf/_book',
                        ]),
                    ]
                ]);
                $success = Craft::$app->sections->saveSection($section);

//              4. Save entry type
                // Get the entry type to add the new field to
                $entryType = Craft::$app->getSections()->getSectionByHandle('bookshelfBookSection')->entryTypes[0];

                $fieldAuthor = Craft::$app->getFields()->getFieldByHandle('bookshelfAuthorField');
                $fieldGenre = Craft::$app->getFields()->getFieldByHandle('bookshelfGenreField');
                $fieldPublicationYear = Craft::$app->getFields()->getFieldByHandle('bookshelfPublicationYearField');
                $fieldCoverImage = Craft::$app->getFields()->getFieldByHandle('bookshelfCoverImageField');
                $fieldBriefDescription = Craft::$app->getFields()->getFieldByHandle('bookshelfBriefDescriptionField');
                $fieldWishListUsers = Craft::$app->getFields()->getFieldByHandle('bookshelfWishListUsers');

                // Get current fieldLayout
                $fieldLayout = $entryType->getFieldLayout();
                $fieldLayout->setTabs([
//                    ...$fieldLayout->getTabs(),
                    FieldLayoutTab::createFromConfig([
                        'name' => 'Book',
                        'layoutId' => $fieldLayout->id,
                        'elements' => [
                            new CustomField($fieldAuthor, [
                                'required' => true,
                            ]),
                            new CustomField($fieldGenre, [
                                'required' => true,
                            ]),
                            new CustomField($fieldPublicationYear, [
                                'required' => true,
                            ]),
                            new CustomField($fieldCoverImage, [
                                'required' => false,
                            ]),
                            new CustomField($fieldBriefDescription, [
                                'required' => true,
                            ]),
                            new CustomField($fieldWishListUsers, [
                                'required' => false,
                            ]),
                        ]
                    ])
                ]);

                $entryTypeBooks = Craft::$app->sections->saveEntryType(new EntryType([
                    'sectionId' => Craft::$app->sections->getSectionByHandle('bookshelfBookSection')->id,
                    'name' => 'books',
                    'handle' => 'bookshelfBooksEntryType',
                    'hasTitleField' => true,
                    'fieldLayout' => $fieldLayout
                ]));
            } catch (\Throwable $e) {
                throw new Exception($e);
            }
//        } else {
//            return false;
//        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230728_202516_book_migration cannot be reverted.\n";

//        if (Craft::$app->projectConfig->get('plugins._bookshelf', true) !== null) {
            try {
//              1. Delete FieldGroup
                $groups = Craft::$app->fields->getAllGroups();
                $fieldGroupBooks = null;
                foreach ($groups as $group) {
                    if($group->name === 'bookshelfBooksGroup') {
                        $fieldGroupBooks = $group;
                    }
                }

                if($fieldGroupBooks) {
                    $fields = Craft::$app->fields->getFieldsByGroupId($fieldGroupBooks->id);

                    foreach ($fields as $field) {
//                      2. Delete Fields
                        if(
                            $field->handle === 'bookshelfAuthorField' ||
                            $field->handle === 'bookshelfGenreField' ||
                            $field->handle === 'bookshelfPublicationYearField' ||
                            $field->handle === 'bookshelfCoverImageField' ||
                            $field->handle === 'bookshelfBriefDescriptionField' ||
                            $field->handle === 'bookshelfWishListUsers'
                        ){
                            Craft::$app->fields->deleteField($field);
                        }
                    }

                    // Remove Books Group
                    Craft::$app->fields->deleteGroup($fieldGroupBooks);
                }


//              3. Delete Section
                $section = Craft::$app->sections->getSectionByHandle('bookshelfBookSection');
                if ($section) {
                    $entryTypes = Craft::$app->sections->getEntryTypesBySectionId($section->id);
//                  4. Delete entry type
                    foreach ($entryTypes as $entryType){
                        if($entryType->handle === 'bookshelfBooksEntryType') {
                            Craft::$app->sections->deleteEntryType($entryType);
                        }
                    }

                    Craft::$app->sections->deleteSection($section);
                }
            } catch (\Throwable $e) {
                throw new Exception($e);
            }
//        }

        return true;
    }
}
