<?php
namespace app\models\form;

use yii\base\Model;

class ProcessorParams extends Model
{
    public mixed $delay = 0;

    public function rules(): array
    {
        return [
            [
                'delay',
                'filter',
                'filter' => fn($v) => ($v === '' || $v === null) ? 0 : intval($v),
                'skipOnEmpty' => false,
            ],
            [
                'delay',
                'integer',
                'min' => 0,
                'max' => 30,
                'tooSmall' => 'Задержка не может быть отрицательным числом',
                'tooBig' => 'Задержка не может превышать 30 секунд.',
                'skipOnEmpty' => true,
            ],
        ];
    }
}