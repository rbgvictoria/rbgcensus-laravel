<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateGridsTableService;

class UpdateGridsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:grids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Grids table from MySQL database';

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
        $service = new UpdateGridsTableService();
        
        $this->info('Updating grids...');
        $service->update();
        
        $this->info('Done.');
    }
}
