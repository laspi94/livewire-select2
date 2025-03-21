<?php

namespace Livewire\Select2\Traits;

trait LivewireSelect2
{
    public $select2Field = [];

    public function loadSelect2()
    {
        $this->dispatch('loadSelect2', [
            'data' => $this->select2Field
        ]);
    }

    public function select2Field(array $fields)
    {
        foreach ($fields as $field => $config) {
            $fields[$field] = [];

            $routes = config('livewire-select2.routes');

            $fields[$field] = $routes[$config] ?? [];

            if (!empty($routes[$config])) {
                $fields[$field]['route'] = route($routes[$config]['name']);
            }
        }

        $this->select2Field = $fields;
    }

    public function getListeners()
    {
        $slect2Listeners = [
            'select2Updated' => 'select2Updated'
        ];

        if (property_exists($this, 'listeners') && is_array($this->listeners)) {
            return array_merge($this->listeners, $slect2Listeners);
        }

        return $slect2Listeners;
    }

    public function select2Updated($field, $value)
    {
        data_set($this, $field, $value);
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            $this->resetErrorBagSelect2();

            parent::validate($rules, $messages, $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->preValidacion($e);

            throw $e;
        }
    }

    public function preValidacion(\Illuminate\Validation\ValidationException $e)
    {
        foreach ($this->select2Field as  $field => $config) {
            $data = [
                'field' => $field,
                'message' => false
            ];

            if ($e->validator->messages()->has($field)) {
                $data['message'] = $e->validator->messages()->first($field);
            }

            $this->dispatch('validateSelect2Field', $data);
        }
    }

    public function resetErrorBagSelect2()
    {
        foreach ($this->select2Field as $field => $config) {
            $data = [
                'field' => $field
            ];

            $this->dispatch('resetValidateSelect2Field', $data);
        }
    }

    public function select2PaginateList($dataModel, $search = '', $query = null, $paginate = 20, $initialValue = null)
    {
        try {
            if (is_string($dataModel) && class_exists($dataModel)) {
                try {
                    $listado = $dataModel::select2($search, $query)->orderBy('id')->paginate($paginate);

                    $registroInicial = null;

                    if ($initialValue) {
                        $registroInicial = $dataModel::select2('', $query)->find($initialValue);
                    }

                    if ($registroInicial && $listado->currentPage() == 1 && !$listado->contains('id', $initialValue)) {
                        $listado->prepend($registroInicial);
                    }

                    return response()->json([
                        'results' => $listado->items(),
                        'pagination' => ['more' => $listado->hasMorePages()],
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception("Error on build eloquent query on $dataModel, error: " . $e->getMessage());
                }
            } else {
                try {
                    if ($search) {
                        $dataModel = array_values(array_filter($dataModel, function ($value) use ($search) {

                            $query = strtolower($search);

                            foreach ($value as $key => $val) {
                                if (str_contains(strtolower($val), $query)) {
                                    return true;
                                }
                            }

                            return false;
                        }));
                    }

                    return response()->json([
                        'results' => $dataModel,
                        'pagination' => ['more' => false],
                    ]);
                } catch (\Throwable $e) {
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
