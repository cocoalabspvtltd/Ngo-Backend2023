<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FundraiserScheme;

/**
 * FundraiserSchemeSearch represents the model behind the search form of `backend\models\FundraiserScheme`.
 */
class FundraiserSchemeSearch extends FundraiserScheme
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'campaign_id', 'status','is_approved'], 'integer'],
            [['image_url', 'title', 'closing_date', 'story', 'created_at', 'modified_at','from_date','to_date','created_by'], 'safe'],
            [['fund_required'], 'number'],
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
        $query = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['user.role'=>'super-admin']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
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
            'campaign_id' => $this->campaign_id,
            'is_approved' => $this->is_approved,
            'fund_required' => $this->fund_required,
            'closing_date' => $this->closing_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
        ]);

        $query->andFilterWhere(['like', 'image_url', $this->image_url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'story', $this->story]);
            
        if($this->from_date){
            $query->andFilterWhere(['>=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($this->from_date))]);            
        }
        if($this->to_date){
            $query->andFilterWhere(['<=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($this->to_date))]);            
        }
        return $dataProvider;
    }
    public function searchFundraiser($params)
    {
        $query = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['!=','user.role','super-admin']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $isApproved = '';
        if($this->is_approved && $this->is_approved == 2){
            $isApproved = 2;
            $this->is_approved = '';
            $query->andWhere(['<','DATE(closing_date)',date('Y-m-d')]);
        }
        if($this->is_approved && $this->is_approved == 3){
            $isApproved = 3;
            $this->is_approved = '';
            $query->andWhere(['is_amount_collected'=>1]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'is_approved' => $this->is_approved,
            'created_by' => $this->created_by,
            'fund_required' => $this->fund_required,
            'closing_date' => $this->closing_date,
            'status' => $this->status,
            'modified_at' => $this->modified_at,
        ]);
        if($isApproved == '2'){
            $this->is_approved = 2;
        }
        if($isApproved == '3'){
            $this->is_approved = 3;
        }

        $query->andFilterWhere(['like', 'image_url', $this->image_url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'story', $this->story]);

        if($this->from_date){
            $query->andFilterWhere(['>=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($this->from_date))]);            
        }
        if($this->to_date){
            $query->andFilterWhere(['<=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($this->to_date))]);            
        }

        return $dataProvider;
    }
}
