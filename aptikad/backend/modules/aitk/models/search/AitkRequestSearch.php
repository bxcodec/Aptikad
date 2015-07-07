<?php

namespace backend\modules\aitk\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\aitk\models\AitkRequest;

/**
 * AitkRequestSearch represents the model behind the search form about `backend\modules\aitk\models\AitkRequest`.
 */
class AitkRequestSearch extends AitkRequest {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tujuan_sms_pengurus', 'status_asrama', 'status_dosen', 'deleted'], 'integer'],
            [['pengurus_asrama', 'request_id', 'dosen_wali', 'mahasiswa_id', 'tipe_ijin', 'waktu_start', 'waktu_end', 'alasan_ijin', 'lampiran', 'alasan_penolakan', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = AitkRequest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
            'pagination' => ['defaultPageSize' => 3]
        ]);

        
        $query->joinWith('pengurusAsrama');
        $query->joinWith('mahasiswa');
        $query->joinWith('dosenWali');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
//            'request_id' => $this->request_id,
//            'dosen_wali' => $this->dosen_wali,
//            'requester' => $this->requester,
//            'mahasiswa_id' => $this->mahasiswa_id,
//            'tujuan_sms_pengurus' => $this->tujuan_sms_pengurus,
//            'pengurus_asrama' => $this->pengurus_asrama,
            'waktu_start' => $this->waktu_start,
            'waktu_end' => $this->waktu_end,
            'status_asrama' => $this->status_asrama,
            'status_dosen' => $this->status_dosen,
            'deleted' => $this->deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'tipe_ijin', $this->tipe_ijin])
                ->andFilterWhere(['like', 'alasan_ijin', $this->alasan_ijin])
                ->andFilterWhere(['like', 'lampiran', $this->lampiran])
                ->andFilterWhere(['like', 'alasan_penolakan', $this->alasan_penolakan])
                ->andFilterWhere(['like', 'created_by', $this->created_by])
                ->andFilterWhere(['like', 'aitk_r_asrama.nama_pengurus', $this->tujuan_sms_pengurus])
                ->andFilterWhere(['like', 'aitk_r_asrama.nama_pengurus', $this->pengurus_asrama])
                ->andFilterWhere(['like', 'aitk_r_mahasiswa.nama_mahasiswa', $this->requester])
                ->andFilterWhere(['like', 'aitk_r_asrama.nama_pengurus', $this->requester])
                ->andFilterWhere(['like', 'aitk_r_mahasiswa.nama_mahasiswa', $this->mahasiswa_id])
                ->andFilterWhere(['like', 'aitk_r_dosen.nama_dosen', $this->dosen_wali])
                ->andFilterWhere(['like', 'updated_by', $this->updated_by]);

        return $dataProvider;
    }

}
