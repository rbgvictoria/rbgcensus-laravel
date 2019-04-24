<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdatePlantAttributesTableService;

class UpdatePlantAttributesTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:update:plant-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Plant Attributes table';

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
        $service = new UpdatePlantAttributesTableService();
        $this->info('Updating plant attributes...');
        $service->update();
        $this->info('Done.');
    }
}
