<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateTaxonAreasTableService;

class UpdateTaxonAreasTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:taxon-areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Taxon areas';

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
        $service = new UpdateTaxonAreasTableService();
        $this->info('Updating taxon areas...');
        $service->update();
        $this->info('Done.');
    }
}
