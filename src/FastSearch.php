<?php
namespace SumeetGhimire\FastSearch;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FastSearch
{
    protected $models = [];
    protected $columns = [];
    protected $keyword;
    protected $matchType = 'like'; 
    protected $cacheDuration = 600; 
    protected $selectAll = false;
    public function addModel($model, $columns,$selectAll)
    {
        $this->models[] = $model;
        $this->columns[] = $columns;
        $this->selectAll = $selectAll;
        return $this;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = trim($keyword);
        return $this;
    }

    public function setMatchType($matchType)
    {
        $this->matchType = $matchType;
        return $this;
    }

    public function setCacheDuration($duration)
    {
        $this->cacheDuration = $duration;
        return $this;
    }
   
    public function search()
    {
        if (empty($this->models) || empty($this->keyword)) {
            return collect();
        }

        $cacheKey = $this->generateCacheKey();

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $queries = collect();

            foreach ($this->models as $index => $model) {
                $columns = $this->columns[$index];
                $selectAll = $this->selectAll;
                $queries->push($this->buildQuery($model, $columns, $selectAll));
            }

            // Combine the queries with unionAll and ensure consistent column selection
            $finalQuery = $queries->reduce(fn($carry, $query) => $carry ? $carry->unionAll($query) : $query);
            
            // Perform the query and get the results
            return $finalQuery->limit(100)->get();
        });
    }

    protected function buildQuery($model, $columns, $selectAll )
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        // Create the query
        $query = $model::query();
    
        // If selectAll is true, select all columns
        if ($selectAll) {
            $query->select('*');  // Select all columns
        } else {
            // Otherwise, select only the specified columns
            $query->select($columns);
        }
    
        // Build the search query with LIKE or exact match
        foreach ($columns as $column) {
            if ($this->matchType === 'exact') {
                $query->orWhere($column, '=', $this->keyword);
            } else {
                // Use LIKE search for all columns
                $query->orWhere($column, 'like', '%' . $this->keyword . '%');
            }
        }
    
        return $query;
    }
    
    

    protected function generateCacheKey()
    {
        // Ensure that columns are always an array (in case it's a string or an array of strings)
        if (is_string($this->columns)) {
            $this->columns = [$this->columns];
        }
    
        // Flatten the columns array if it's an array of arrays
        $flattenedColumns = [];
        foreach ($this->columns as $column) {
            if (is_array($column)) {
                $flattenedColumns = array_merge($flattenedColumns, $column);
            } else {
                $flattenedColumns[] = $column;
            }
        }
    
        return 'search_' . md5($this->keyword . $this->matchType . implode('_', $this->models) . implode('_', $flattenedColumns));
    }
}
