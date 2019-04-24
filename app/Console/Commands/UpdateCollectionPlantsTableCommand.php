<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateCollectionPlantsTableService;

class UpdateCollectionPlantsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:collection-plants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Collection Plants table';

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
        $service = new UpdateCollectionPlantsTableService();
        $this->info('Updating collections...');
        $service->update();
        $this->info('Done.');
    }
}
