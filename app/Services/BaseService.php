<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class BaseService
{
    private $model;
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function setModel($model)
    {
        $this->model = resolve($model);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function paginate($query, $request)
    {
        if ($request->get('sort_by')) {
            $sortBy = $request->get('sort_by');
            $sortOrder = $request->get('sort', 'asc');
            if (is_array($sortBy)) {
                foreach ($sortBy as $key => $value) {
                    $query->orderBy($value, $sortOrder[$key] ?? 'asc');
                }
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        if ($request->get('search') || $request->get('search_by')) {
            $search = $request->get('search');
            $searchBy = $request->get('search_by', []);
            $searchBy = is_array($searchBy) ? $searchBy : [$searchBy];
            $query->where(function ($q) use ($search, $searchBy) {
                foreach ($searchBy as $field) {
                    $q->orWhere($field, 'LIKE', "%$search%");
                }
            });
        }

        $query->when($request->has('with'), function ($query) use ($request) {
            $query->with($request->with);
        })->when($request->has('count'), function ($query) use ($request) {
            $query->withCount($request->count);
        });
        
        if ($request->boolean('no_pagination')) {
            return $query->get();
        }

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param FormRequest|Request|array $request
     */
    public function save($request)
    {
        if (is_array($request)) {
            $data = $request;
        } elseif (method_exists($request, 'validated')) {
            $data = $request->validated();
        } else {
            $data = $request->all();
        }
        return $this->getModel()::create($data);
    }

    /**
     * @param FormRequest|Request|array $request
     * @param Model $model
     */
    public function update($request, $model)
    {
        if (is_array($request)) {
            $data = $request;
        } elseif (method_exists($request, 'validated')) {
            $data = $request->validated();
        } else {
            $data = $request->all();
        }
        $model->update($data);

        return $model->fresh();
    }

    public function deleteById(int $id)
    {
        return $this->getModel()::whereKey($id)->delete();
    }

    /**
     * @param Model $model
     */
    public function delete($model)
    {
        return $model->delete();
    }

    public function start()
    {
        \DB::beginTransaction();
    }

    public function end()
    {
        \DB::commit();
    }

    public function rollback()
    {
        \DB::rollBack();
    }

    public function transaction(callable $callback)
    {
        return \DB::transaction($callback);
    }

    public function find($id)
    {
        return $this->getModel()::find($id);
    }
}