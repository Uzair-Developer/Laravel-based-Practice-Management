<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Add4Cols extends Command
{
    protected $name = 'Add4Cols';
    protected $description = 'Add4Cols';
    private $databaseName = 'pms';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $tables = DB::select("select table_name from information_schema.tables WHERE TABLE_TYPE NOT  LIKE 'VIEW' and table_schema = '$this->databaseName'");
//        dd($tables);
        foreach ($tables as $tbl) {
            if (!Schema::hasColumn($tbl->table_name, 'created_at')) {
                Schema::table($tbl->table_name, function ($table) {
                    $table->dateTime('created_at');
                });
            }
            if (!Schema::hasColumn($tbl->table_name, 'updated_at')) {
                Schema::table($tbl->table_name, function ($table) {
                    $table->dateTime('updated_at');
                });
            }
            if (!Schema::hasColumn($tbl->table_name, 'create_timestamp')) {
                Schema::table($tbl->table_name, function ($table) {
                    $table->integer('create_timestamp')->nullable();
                });
            }
//            if (!Schema::hasColumn($tbl->table_name, 'updated_at_integer')) {
//                Schema::table($tbl->table_name, function ($table) {
//                    $table->integer('updated_at_integer');
//                });
//            }
        }
        $this->info('finish');
    }
}
