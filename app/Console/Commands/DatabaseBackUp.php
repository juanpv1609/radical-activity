<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Mail\BackupEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

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
     * @return int
     */
    public function handle()
    {

        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".sql";
        $command = "".env('DUMP_PATH')." --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  > " . storage_path() . "/app/backup/" . $filename;
        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);
        $zipFileName=$fileName;
        $zipPassword='jk1609PV*';
        $commandZip ='zip -jrq -P '.$zipPassword.' '.$zipFileName.'.zip '.$fileName;
        exec($commandZip,$output,$returnVar);
        //dd($output);

            $details=[
            'title' => 'Ejecución automática de backup diario',
            'body' => 'Estimad@ el software Registro de actividades ha generado el backup diario automático correspondiente al '.Carbon::now()->format('Y-m-d'),
            'file' =>  $zipFileName.'.zip'
        ];
        Mail::to('juan.perugachi@gruporadical.com')
                    //->cc(['paul.canchignia@gruporadical.com','xavier.montoya@gruporadical.com'])
                    ->send(new BackupEmail($details));






    }
}
