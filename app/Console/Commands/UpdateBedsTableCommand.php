<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateBedsTableService;

class UpdateBedsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:beds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Beds table from MySQL database';

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
        $service = new UpdateBedsTableService();
        $this->info('Updating beds...');
        $service->update();
        
        $this->info('Creating nested sets...');
        $service->nestedSets();
        
        $this->info('Done.');
    }
}
