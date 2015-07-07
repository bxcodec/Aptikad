<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "aitk_r_account".
 *
 * @property integer $account_id
 * @property string $username
 * @property string $password
 * @property integer $level
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRAsrama[] $aitkRAsramas
 * @property AitkRDosen[] $aitkRDosens
 * @property AitkRMahasiswa[] $aitkRMahasiswas
 */
class AitkRAccount extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    const STATUS_DELETED = 1;
    const STATUS_ACTIVE = 0;

    public static function tableName()
    {
        return 'aitk_r_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 50],
            ['username', 'unique', 'targetClass' => '\common\models\AitkRAccount', 'message' => 'This username has already been taken.'],
            [['password'], 'string', 'max' => 250],
            [['created_by', 'updated_by'], 'string', 'max' => 25],          
            ['deleted', 'default', 'value' => self::STATUS_ACTIVE],
            ['deleted', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
       
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_id' => 'Account ID',
            'username' => 'Username',
            'password' => 'Password',
//            'level' => 'Level',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRAsramas()
    {
        return $this->hasMany(AitkRAsrama::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRDosens()
    {
        return $this->hasMany(AitkRDosen::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRMahasiswas()
    {
        return $this->hasMany(AitkRMahasiswa::className(), ['account_id' => 'account_id']);
    }

    public function getAuthKey() {
        
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function validateAuthKey($authKey) {
        
    }

    public static function findIdentity($id)
    {
        return static::findOne(['account_id' => $id, 'deleted' => self::STATUS_ACTIVE]);
        //return static::findOne(['account_id' => $id]);
    }
    public static function findIdentityByAccessToken($token, $type = null) {
        
    }
     public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }


     public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'deleted' => self::STATUS_ACTIVE]);
        //return static::findOne(['username' => $username]);
    }
    
    

}
