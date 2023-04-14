<?php

namespace App\Packages\Anonymizer\src\Commands;

use App\Packages\Anonymizer\src\Anonymizer;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AnonymizerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:anonymize';

    public $description = 'Anonymize specific data of the database';
    private Anonymizer $service;


    public function __construct()
    {
        parent::__construct();

        $this->service = new Anonymizer();
    }

    public function handle(): int
    {
        $anonymizationStart = microtime(true);

        $anonymizableClasses = $this->service->getAnonymizableClasses();

        $this->warn('Anonymization started');

        foreach ($anonymizableClasses as $anonymizableClass) {
            $this->anonymizeTable(
                new $anonymizableClass()
            );
        }

        $this->warn('Anonymization done in ' . CarbonInterval::seconds(microtime(true) - $anonymizationStart)->cascade()->forHumans(['parts' => 3, 'short' => true]));

        return self::SUCCESS;
    }

    private function anonymizeTable(Model $model): void
    {
        $start = microtime(true);

        $this->info('Anonymizing data of ' . $model->getTable() . ' table');

        $progressBar = $this->output->createProgressBar($this->service->getCount($model));

        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% | Remaining: %remaining:6s%');

        $this->service->getChunk($model, function (Collection $chunkItems) use ($progressBar) {
            $chunkItems->each(fn(Model $model) => $this->service->changeData($model));
            $progressBar->advance($chunkItems->count());
        });

        $progressBar->finish();

        $this->info(' - Done in ' . CarbonInterval::seconds(microtime(true) - $start)->cascade()->forHumans(['parts' => 3, 'short' => true]));
    }
}