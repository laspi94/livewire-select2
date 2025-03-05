<?php

namespace Livewire\Select2;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use \illuminate\support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class LivewireSelect2ServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutes();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-select2');

        $this->mergeConfigFrom(__DIR__ . '/config/livewire-select2.php', 'livewire-select2');

        $this->publishes([
            __DIR__ . '/config/livewire-select2.php' => config_path('livewire-select2.php'),
        ], 'config');

        Blade::directive('LivewireSelect2', function () {
            return "<?php echo view('livewire-select2::scripts')->render(); ?>";
        });

        Builder::macro('select2', function ($search = '', $extraQuery = null) {
            $model = $this->getModel();

            return $this->select(DB::raw($model->select2filter['id'] . " as id"), DB::raw($model->select2filter['text'] . " as text"))
                ->when($search != '', function ($q) use ($search, $model) {
                    $q->orWhere(function ($exq) use ($search, $model) {
                        $table = $model->getTable();
                        if (property_exists($model, 'select2filter') && !empty($model->select2filter)) {
                            foreach ($model->select2filter as $key => $column) {
                                $columnName = str_contains($column, '.') ? last(explode('.', $column)) : $column;
                                if (Schema::hasColumn($table, $columnName)) {
                                    $exq->orWhere($column, 'like', "%" . $search . "%");
                                }
                            }
                        }
                    });
                })->when($extraQuery, function ($q) use ($extraQuery) {
                    $extraQuery($q);
                });
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function loadRoutes()
    {
        Route::middleware(config('livewire-select2.middleware', []))
            ->prefix(config('livewire-select2.prefix', ''))->group(function () {
                foreach (config('livewire-select2.routes', []) as $route) {
                    Route::get($route['uri'], [$route['controller'], $route['method']])->name($route['name']);
                }
            });
    }
}
