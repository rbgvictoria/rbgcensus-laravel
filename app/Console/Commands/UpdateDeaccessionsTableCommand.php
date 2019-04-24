<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateDeaccessionsTableService;

class UpdateDeaccessionsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:deaccessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Deaccessions table';

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
        $service = new UpdateDeaccessionsTableService();
        $this->info('Updating deaccessions...');
        $service->update();
        $this->info('Done.');
    }
}
