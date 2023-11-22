<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AgencyLandingPage;

/**
 * AgencyLandingPageSearch represents the model behind the search form of `backend\models\AgencyLandingPage`.
 */
class AgencyLandingPageSearch extends AgencyLandingPage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'agency_id', 'fundraiser_scheme_id', 'status'], 'integer'],
            [['landing_page_url', 'virtual_account_id', 'virtual_account_number', 'virtual_account_name', 'virtual_account_type', 'virtual_account_ifsc', 'created_at', 'modified_at','from_date','to_date'], 'safe'],
            [['total_amount_collected'], 'number'],
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
        $query = AgencyLandingPage::find();

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
            'agency_id' => $this->agency_id,
            'fundraiser_scheme_id' => $this->fundraiser_scheme_id,
            'total_amount_collected' => $this->total_amount_collected,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
        ]);

        $query->andFilterWhere(['like', 'landing_page_url', $this->landing_page_url])
            ->andFilterWhere(['like', 'virtual_account_id', $this->virtual_account_id])
            ->andFilterWhere(['like', 'virtual_account_number', $this->virtual_account_number])
            ->andFilterWhere(['like', 'virtual_account_name', $this->virtual_account_name])
            ->andFilterWhere(['like', 'virtual_account_type', $this->virtual_account_type])
            ->andFilterWhere(['like', 'virtual_account_ifsc', $this->virtual_account_ifsc]);
        if($this->from_date){
            $query->andFilterWhere(['>=','DATE(created_at)',date('Y-m-d',strtotime($this->from_date))]);            
        }
        if($this->to_date){
            $query->andFilterWhere(['<=','DATE(created_at)',date('Y-m-d',strtotime($this->to_date))]);            
        }
        return $dataProvider;
    }
}
