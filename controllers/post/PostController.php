<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\rest\controllers\post;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\post\models\Post;
use humhub\modules\rest\components\BaseContentController;
use humhub\modules\rest\definitions\PostDefinitions;
use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;
use Yii;

class PostController extends BaseContentController
{
    /**
     * {@inheritdoc}
     */
    public function getContentActiveRecordClass()
    {
        return Post::class;
    }

    /**
     * {@inheritdoc}
     */
    public function returnContentDefinition(ContentActiveRecord $contentRecord)
    {
        /** @var Post $contentRecord */
        $post = Post::findOne(['id' => $contentRecord->id]);
        return PostDefinitions::getPost($post);
    }

    /**
     * Evolars: feed do usuário logado (posts dos espaços em que é membro e do próprio perfil).
     */
    public function actionFeed()
    {
        $user = Yii::$app->user->getIdentity();
        $containerIds = Space::find()->select('contentcontainer_id')
            ->where(['id' => Membership::getUserSpaceIds($user->id)])->column();
        $containerIds[] = $user->contentcontainer_id;

        $query = Post::find()->joinWith('content')
            ->where(['content.contentcontainer_id' => $containerIds])
            ->orderBy(['content.created_at' => SORT_DESC])
            ->readable();

        $pagination = $this->handlePagination($query);
        $results = [];
        foreach ($query->all() as $post) {
            $results[] = PostDefinitions::getPost($post);
        }

        return $this->returnPagination($query, $pagination, $results);
    }
}
