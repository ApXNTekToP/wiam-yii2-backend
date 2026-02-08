<?php
namespace app\models\form;

use yii\base\Model;

class RequestsParams extends Model
{
    public mixed $user_id;
    public mixed $amount;
    public mixed $term;

    public function rules(): array
    {
        return [
            [
                ['user_id', 'amount', 'term'],
                'required',
                'message' => 'Поле {attribute} обязательно для заполнения'
            ],
            [
                ['user_id', 'amount', 'term'],
                'integer',
                'min' => 0,
                'tooSmall' => 'Поле {attribute} должно быть положительным числом',
                'message' => 'Поле {attribute} должно быть целым числом'
            ],
        ];
    }
}