<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateTaxaTableService;

class UpdateTaxaTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:taxa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Taxa table from MySQL database';

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
        
        $this->info('Updating taxa...');
        $service->update();
        
        $this->info('Creating nested sets...');
        $service->nestedSets();
        
        $this->info('Updating synonyms...');
        $service->synonyms();
        
        $this->info('Done.');
    }
}
