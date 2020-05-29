<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QueryBuilder extends Model
{
    public $query = null;
    public $limit = 50;
    public $wheres = [];
    public $orWheres = [];
    public $withs = [];
    public $has = [];
    private $is_simple = false;

    function __construct($model, $request)
    {
        $this->query = $model::query();

        if($request->query('has'))
        {
            if(is_array($request->query('has')))
            {
                foreach($request->query('has') as $value)
                {
                    $values = explode(",", $value);
                    $this->has = $values;
                }
            }
            else
            {
                $values = explode(",", $request->query('has'));
                $this->has = $values;
            }
        }

        if($request->query('includes'))
        {
            if(is_array($request->query('includes')))
            {
                foreach($request->query('includes') as $value)
                {
                    $values = explode(",", $value);
                    $this->withs = $values;
                }
            }
            else
            {
                $values = explode(",", $request->query('includes'));
                $this->withs = $values;
            }
        }

        if($request->query('limit'))
        {
            $this->limit = $request->query('limit');

            if($this->limit == 'unlimited')
            {
                $this->limit = $model::all()->count();
            }
        }

        if($request->query('exact'))
        {
            if(is_array($request->query('exact')))
            {
                foreach($request->query('exact') as $value)
                {
                    $values = explode(",", $value);
                    $this->wheres[] = [$values[0], '=', $values[1]];
                }
            }
            else
            {
                $values = explode(",", $request->query('exact'));
                $this->wheres[] = [$values[0], '=', $values[1]];
            }
        }

        if($request->query('orexact'))
        {
            if(is_array($request->query('orexact')))
            {
                foreach($request->query('orexact') as $value)
                {
                    $values = explode(",", $value);
                    $this->orWheres[] = [$values[0], '=', $values[1]];
                }
            }
            else
            {
                $values = explode(",", $request->query('orexact'));
                $this->orWheres[] = [$values[0], '=', $values[1]];
            }
        }

        if($request->query('like'))
        {
            if(is_array($request->query('like')))
            {
                foreach($request->query('like') as $value)
                {
                    $values = explode(",", $value);
                    $this->wheres[] = [$values[0], 'LIKE', '%'. $values[1] . '%'];
                }
            }
            else
            {
                $values = explode(",", $request->query('like'));
                $this->wheres[] = [$values[0], 'LIKE', '%'. $values[1] . '%'];
            }
        }

        if($request->query('orlike'))
        {
            if(is_array($request->query('orlike')))
            {
                foreach($request->query('orlike') as $value)
                {
                    $values = explode(",", $value);
                    $this->orWheres[] = [$values[0], 'LIKE', '%'. $values[1] . '%'];
                }
            }
            else
            {
                $values = explode(",", $request->query('orlike'));
                $this->orWheres[] = [$values[0], 'LIKE', '%'. $values[1] . '%'];
            }
        }

        // dd($this->withs);

        if(!empty($this->has))
        {
            foreach($this->has as $relation){
                $this->query->has($relation);
            }
        }

        if(!empty($this->withs))
        {
            $this->query->with($this->withs);
        }

        if(!empty($this->wheres))
        {
            $this->query->where($this->wheres);
        }

        if(!empty($this->orWheres))
        {
            $this->query->orWhere($this->orWheres);
        }

    }

    public function paginate()
    {
        if($this->is_simple)
        {
            return $this->query->simplePaginate($this->limit);
        }

        return $this->query->paginate($this->limit);
    }

    public function get()
    {
        return $this->query->get();
    }

}
