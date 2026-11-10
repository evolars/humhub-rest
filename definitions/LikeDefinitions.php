<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\rest\definitions;

use humhub\modules\content\interfaces\ContentProvider;
use humhub\modules\like\models\Like;
use humhub\modules\like\services\LikeService;
use humhub\modules\user\models\User;

/**
 * Class CommentDefinitions
 * @package humhub\modules\rest\definitions
 */
class LikeDefinitions
{
    public static function getLikesSummary(ContentProvider $record)
    {
        $result = [];
        $result['total'] = (new LikeService($record, new User()))->getCount();
        // Evolars: indica se o usuário da requisição já curtiu
        $me = \Yii::$app->user->getIdentity();
        $result['liked'] = $me ? (new LikeService($record, $me))->hasLiked() : false;
        return $result;
    }

    public static function getLike(Like $like)
    {
        return [
            'id' => $like->id,
            'createdBy' => UserDefinitions::getUserShort($like->createdBy),
            'createdAt' => $like->created_at,
        ];
    }

}
