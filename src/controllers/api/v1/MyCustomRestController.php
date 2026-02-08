<?php

namespace app\controllers\api\v1;

use app\models\ar\Request;
use app\models\form\ProcessorParams;
use app\models\form\RequestsParams;
use Random\RandomException;
use Yii;
use yii\db\Exception;
use yii\db\IntegrityException;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class MyCustomRestController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'requests' => ['POST'],
                'processor' => ['GET']
            ]
        ];

        return $behaviors;
    }

    /**
     * POST /api/requests
     * Подача новой заявки на займ. Поданная заявка сохраняется в базе данных.
     * @return array
     */
    public function actionRequests(): array
    {
        $model = new RequestsParams();
        $data = Yii::$app->request->post();

        if($model->load($data, '') && !$model->validate()){
            Yii::$app->response->statusCode = 400;

            return [
                'result' => false,
            ];
        }

        $tx = Yii::$app->db->beginTransaction();

        try{
            $request = new Request([
                'user_id' => $model->user_id,
                'amount'  => $model->amount,
                'term'    => $model->term,
                'status'  => Request::STATUS_NEW,
            ]);

            if (!$request->save()) {
                return [
                    'result' => false,
                    'errors' => $request->getErrors(),
                ];
            }

            $tx->commit();
        }catch (\Throwable $e){
            $tx->rollBack();

            Yii::$app->response->statusCode = 400;

            Yii::error($e, __METHOD__);

            return [
                'result' => false,
                'errors' => $e->getMessage()
            ];
        }

        Yii::$app->response->statusCode = 201;

        return [
            'result' => true,
            'id' => $request->id
        ];
    }

    /**
     * GET /api/processor
     * Запуск обработки заявок на займ. По результату обработки каждой заявки, ей должен быть установлен один из
     * статусов “approved” или ”declined”. Принятие решения должно происходить рандомно.
     * Вероятность аппрува заявки – 10%.
     *
     * @return array
     * @throws RandomException
     * @throws Exception
     * @throws IntegrityException
     */
    public function actionProcessor(): array
    {
        $model = new ProcessorParams();

        $model->load(Yii::$app->request->get(), '');

        $limit = 100;
        $validated = $model->validate();

        if($validated){
            $tx = Yii::$app->db->beginTransaction();

            try{
                $rows = Yii::$app->db->createCommand("
                    WITH picked as (
                        SELECT id, user_id FROM {{%requests}} WHERE status = :status_new
                        ORDER BY id FOR UPDATE SKIP LOCKED LIMIT :limit
                    )
                    UPDATE {{%requests}} r SET status = :status_processing FROM picked p WHERE r.id = p.id
                    RETURNING r.id, r.user_id
                ", [
                    ':limit' => $limit,
                    ':status_new' => Request::STATUS_NEW,
                    ':status_processing' => Request::STATUS_PROCESSING,
                ])->queryAll();

                $tx->commit();
            }catch (\Throwable $e){
                $tx->rollBack();
                Yii::error($e, __METHOD__);
                Yii::$app->response->statusCode = 500;
                return ['result' => false];
            }

            foreach($rows as $row){
                sleep($model->delay);

                $status = (random_int(1, 10) === 1) ? Request::STATUS_APPROVED : Request::STATUS_DECLINED;

                try{
                    Yii::$app->db->createCommand("
                        UPDATE {{%requests}} r SET status = :status WHERE r.id = :id AND status = :processing
                    ", [
                        ':id' => (int)$row['id'],
                        ':status' => $status,
                        ':processing' => Request::STATUS_PROCESSING,
                    ])->execute();
                }catch (\yii\db\IntegrityException $e){
                    if (($e->errorInfo[0] ?? null) === '23505' && $status === Request::STATUS_APPROVED) {
                        Yii::$app->db->createCommand("
                                UPDATE {{%requests}}
                                    SET status = :status
                                WHERE id = :id
                            ", [
                            ':status' => Request::STATUS_DECLINED,
                            ':id'     => (int)$row['id'],
                        ])->execute();
                    } else {
                        throw $e;
                    }
                }
            }

            return [
                'result' => true
            ];
        }else{
            Yii::$app->response->statusCode = 400;
            return [
                'result' => false,
            ];
        }
    }
}
