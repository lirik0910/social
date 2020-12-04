<?php


namespace App\Libraries\GraphQL;


use Illuminate\Database\Query\Builder;

abstract class AbstractSelection
{
    const DEFAULT_PAGINATION_PARAMS = [
        'limit' => 10,
        'offset' => 0
    ];

    const DEFAULT_ORDER_BY_PARAMS = [
        [
            'column' => 'created_at',
            'dir' => 'DESC'
        ],
        [
            'column' => 'id',
            'dir' => 'DESC'
        ]
    ];

    /**
     * Pagination params
     *
     * @var array
     */
    protected $pagination;

    /**
     * Selection ordering query params
     *
     * @var array
     */
    protected $order_by;

    /**
     * Instance of selection base query
     *
     * @var Builder
     */
    protected $query_instance;


    /**
     * Get selection base query instance
     *
     * @return mixed
     */
    abstract public function getBaseQuery();

    /**
     * Execute base query with needed clauses
     *
     * @return mixed
     */
    protected function getResults()
    {
        $this->query_instance = clone $this->getBaseQuery();
        $this->setAdditionalClauses();
        $this->setOrderByClauses();
        $this->setPaginationClauses();

        return $this
            ->query_instance
            ->get();
    }

    /**
     * Get results total count for base query
     *
     * @return integer
     */
    public function getResultsTotalCount()
    {
        return $this
            ->getBaseQuery()
            ->count();
    }

    /**
     * You can rewrite it for additional logic with query instance
     *
     * @return void
     */
    protected function setAdditionalClauses(){}

    /**
     * Set selection ordering clauses
     *
     * @return mixed
     */
    private function setOrderByClauses()
    {
        $order_by = $this->order_by ?? self::DEFAULT_ORDER_BY_PARAMS;

        if(is_array($order_by[array_key_first($order_by)])) {
            foreach($order_by as $params) {
                $this
                    ->query_instance
                    ->orderBy($params['column'], $params['dir']);
            }
        } else {
            $this
                ->query_instance
                ->orderBy($order_by['column'], $order_by['dir']);
        }


        return $this->query_instance;
    }

    /**
     * Set selection pagination clauses
     *
     * @return mixed
     */
    private function setPaginationClauses()
    {
        $limit = $this->pagination['limit'] ?? self::DEFAULT_PAGINATION_PARAMS['limit'];
        $offset = $this->pagination['offset'] ?? self::DEFAULT_PAGINATION_PARAMS['offset'];

        return $this
            ->query_instance
            ->limit($limit)
            ->offset($offset);
    }
}
