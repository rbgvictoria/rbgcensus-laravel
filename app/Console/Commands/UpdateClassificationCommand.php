<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateTaxaTableService;

class UpdateClassificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:classification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update classification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $service = new UpdateTaxaTableService();
        $this->info('Updating classification...');
        $service->makeClassification();
        $this->info('Done.');
    }
}
