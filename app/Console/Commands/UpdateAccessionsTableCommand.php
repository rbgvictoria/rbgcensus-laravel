<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateAccessionsTableService;

class UpdateAccessionsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:accessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Accessions table from MySQL database';

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
        $service = new UpdateAccessionsTableService();
        
        $this->info('Updating accessions...');
        $service->update();
        
        $this->info('Done.');
    }
}
