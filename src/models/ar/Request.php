<?php

namespace app\models\ar;

use Yii;

/**
 * This is the model class for table "requests".
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property int $term
 * @property string $status
 */
class Request extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_NEW = 'new';
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 'new'],
            [['user_id', 'amount', 'term'], 'required'],
            [['user_id', 'amount', 'term'], 'default', 'value' => null],
            [['user_id', 'amount', 'term'], 'integer'],
            [['status'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'term' => 'Term',
            'status' => 'Status',
        ];
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_NEW => 'new',
            self::STATUS_PROCESSING => 'processing',
            self::STATUS_APPROVED => 'approved',
            self::STATUS_DECLINED => 'declined',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    public function setStatusToNew()
    {
        $this->status = self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function setStatusToProcessing()
    {
        $this->status = self::STATUS_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isStatusApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function setStatusToApproved()
    {
        $this->status = self::STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isStatusDeclined()
    {
        return $this->status === self::STATUS_DECLINED;
    }

    public function setStatusToDeclined()
    {
        $this->status = self::STATUS_DECLINED;
    }
}
