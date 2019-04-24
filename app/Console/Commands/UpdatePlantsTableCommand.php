<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdatePlantsTableService;

class UpdatePlantsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:plants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $service = new UpdatePlantsTableService();
        $this->info('Updating plants...');
        $service->update();
        $this->info('Done.');
    }
}
