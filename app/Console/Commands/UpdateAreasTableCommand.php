<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateAreasTableService;

class UpdateAreasTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update areas';

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
        $service = new UpdateAreasTableService();
        $this->info('Updating areas...');
        $service->update();
        $this->info('Done.');
    }
}
