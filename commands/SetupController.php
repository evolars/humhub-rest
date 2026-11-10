<?php

namespace humhub\modules\rest\commands;

use humhub\modules\rest\Module;
use humhub\modules\user\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Evolars: configura a API a partir de variáveis de ambiente.
 *
 * HUMHUB_REST_ADMIN_TOKEN  token Bearer do administrador (usado pelo backend do Cutis)
 * HUMHUB_REST_ADMIN_USER   usuário administrador (padrão: admin)
 */
class SetupController extends Controller
{
    public function actionIndex()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('rest');
        $settings = $module->settings;
        $settings->set('enableBearerAuth', true);
        $settings->set('enableJwtAuth', false);
        $settings->set('enableBasicAuth', false);
        $settings->set('enableQueryParamAuth', false);

        $token = getenv('HUMHUB_REST_ADMIN_TOKEN');
        if (!$token) {
            $this->stdout("HUMHUB_REST_ADMIN_TOKEN vazio, token não configurado.\n");
            return ExitCode::OK;
        }

        $admin = User::findOne(['username' => getenv('HUMHUB_REST_ADMIN_USER') ?: 'admin']);
        if ($admin === null) {
            $this->stderr("Usuário administrador não encontrado.\n");
            return ExitCode::DATAERR;
        }

        $db = Yii::$app->db;
        $db->createCommand()->delete('rest_user_bearer_tokens', ['user_id' => $admin->id])->execute();
        $db->createCommand()->insert('rest_user_bearer_tokens', [
            'user_id' => $admin->id,
            'token' => hash('sha256', $token),
            'expiration' => '2037-12-31 23:59:59',
        ])->execute();

        $this->stdout("API REST configurada (Bearer do usuário {$admin->username}).\n");
        return ExitCode::OK;
    }
}
