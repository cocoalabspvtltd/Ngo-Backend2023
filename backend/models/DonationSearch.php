<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Donation;

/**
 * DonationSearch represents the model behind the search form of `backend\models\Donation`.
 */
class DonationSearch extends Donation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fundraiser_id', 'user_id', 'show_donor_information', 'status', 'transaction_id'], 'integer'],
            [['name', 'email', 'created_at', 'modified_at','donated_by','from_date','to_date'], 'safe'],
            [['amount'], 'number'],
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
        $query = Donation::find()
        ->where(['status'=>1])
        ->andWhere(['fundraiser_id'=>null]);

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
            'fundraiser_id' => $this->fundraiser_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'show_donor_information' => $this->show_donor_information,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'transaction_id' => $this->transaction_id,
            'donated_by' => $this->donated_by
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email]);

            
        if($this->from_date){
            $query->andFilterWhere(['>=','DATE(created_at)',date('Y-m-d',strtotime($this->from_date))]);            
        }
        if($this->to_date){
            $query->andFilterWhere(['<=','DATE(created_at)',date('Y-m-d',strtotime($this->to_date))]);            
        }
        
        return $dataProvider;
    }
    public function searchOthers($params)
    {
        $query = Donation::find()
        ->where(['status'=>1])
        ->andWhere(['not',['fundraiser_id'=>null]]);

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
            'fundraiser_id' => $this->fundraiser_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'show_donor_information' => $this->show_donor_information,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'transaction_id' => $this->transaction_id,
            'donated_by' => $this->donated_by
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email]);

            
        if($this->from_date){
            $query->andFilterWhere(['>=','DATE(created_at)',date('Y-m-d',strtotime($this->from_date))]);            
        }
        if($this->to_date){
            $query->andFilterWhere(['<=','DATE(created_at)',date('Y-m-d',strtotime($this->to_date))]);            
        }
        
        return $dataProvider;
    }
}
