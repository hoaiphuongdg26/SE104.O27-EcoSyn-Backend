<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateFiltersCommand extends Command
{
    protected $signature = 'generate:filters {model}';

    protected $description = 'Generate filters for a given model';

    public function handle()
    {
        $modelName = $this->argument('model');
        $filterName = $modelName . 'Filter';

        // Lấy các giá trị fillable từ model
        $model = app('App\\Models\\' . $modelName);
        $fillable = $model->getFillable();

        $content = "<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class $filterName extends QueryFilter
{
    protected \$filterable = " . var_export($fillable, true) . ";

    // Example filter method
    public function filterName(\$name)
    {
        return \$this->builder->where('name', 'like', '%' . \$name . '%');
    }
}
";

        $filePath = app_path('Filters/' . $filterName . '.php');

        if (!file_exists($filePath)) {
            file_put_contents($filePath, $content);
            $this->info("$filterName created successfully.");
        } else {
            $this->warn("$filterName already exists.");
        }
    }
}
