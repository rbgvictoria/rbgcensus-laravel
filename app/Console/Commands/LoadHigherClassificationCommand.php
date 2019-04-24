<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LoadHigherClassificationService;

class LoadHigherClassificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbg-census:load-higher-classification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads higher classification from stored CSV file';

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
        $load = new LoadHigherClassificationService();
        $load->createHigherClassificationTable();
        $this->info('Table higher_classification successfully created.');
        
        $load->loadDataFromCsv('seed-higher-classification.csv');
        $this->info('Classification data loaded into temporary table.');
        
        $this->info('Loading data into \'taxa\' table...');
        $load->kingdom();
        $this->info('Data loaded.');
                
        $this->info("Deleting temporary 'higher_classification' table...");
        $load->dropHigherClassificationTable();
        $this->info('All done.');
    }
}
