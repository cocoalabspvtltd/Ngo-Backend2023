<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FundraiserSchemeUpdateRequest;

/**
 * FundraiserSchemeUpdateRequestSearch represents the model behind the search form of `backend\models\FundraiserSchemeUpdateRequest`.
 */
class FundraiserSchemeUpdateRequestSearch extends FundraiserSchemeUpdateRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fundraiser_id', 'campaign_id', 'status', 'relation_master_id', 'created_by', 'pricing_id','is_approved'], 'integer'],
            [['image_url', 'title', 'closing_date', 'story', 'created_at', 'modified_at', 'name', 'email', 'phone_number', 'patient_name', 'health_issue', 'hospital', 'city', 'beneficiary_account_name', 'beneficiary_account_number', 'beneficiary_bank', 'beneficiary_ifsc', 'beneficiary_image'], 'safe'],
            [['fund_required', 'country_code'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = FundraiserSchemeUpdateRequest::find()
        ->leftJoin('user','user.id=fundraiser_scheme_update_request.created_by')
        ->where(['fundraiser_scheme_update_request.status'=>1])
        ->andWhere(['!=','user.role','super-admin']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fundraiser_id' => $this->fundraiser_id,
            'campaign_id' => $this->campaign_id,
            'fund_required' => $this->fund_required,
            'closing_date' => $this->closing_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'country_code' => $this->country_code,
            'relation_master_id' => $this->relation_master_id,
            'created_by' => $this->created_by,
            'pricing_id' => $this->pricing_id,
            'is_approved' => $this->is_approved
        ]);

        $query->andFilterWhere(['like', 'image_url', $this->image_url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'story', $this->story])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'patient_name', $this->patient_name])
            ->andFilterWhere(['like', 'health_issue', $this->health_issue])
            ->andFilterWhere(['like', 'hospital', $this->hospital])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'beneficiary_account_name', $this->beneficiary_account_name])
            ->andFilterWhere(['like', 'beneficiary_account_number', $this->beneficiary_account_number])
            ->andFilterWhere(['like', 'beneficiary_bank', $this->beneficiary_bank])
            ->andFilterWhere(['like', 'beneficiary_ifsc', $this->beneficiary_ifsc])
            ->andFilterWhere(['like', 'beneficiary_image', $this->beneficiary_image]);

        if($this->from_date){
            $query->andFilterWhere(['>=','DATE(fundraiser_scheme_update_request.created_at)',date('Y-m-d',strtotime($this->from_date))]);            
        }
        if($this->to_date){
            $query->andFilterWhere(['<=','DATE(fundraiser_scheme_update_request.created_at)',date('Y-m-d',strtotime($this->to_date))]);            
        }

        return $dataProvider;
    }
}
