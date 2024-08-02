<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

//use Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;



class PessoaController extends Controller
{
    public function exportCsv(Request $request)
    {
    
        //GedAluCod	GedAluIdINEP	GedAluPartPrjSoc	GedAluLotPartPrjSoc	GedAluAtenEscDif	
        // GedAluTransPub	GedAluRespTransPub	GedAluRecAteEduEsp	GedAluIncPor	GedAluIncEm	GedAluAltPor
        // 	GedAluAltEm	GedAluInativ	GedAluAlfab	GedAluLogin	GedAluSnh	GedAluEmailConf	GedAluCodConfEmail
        //     	GedAluMaeInep	GedAluDtaCadPDA	GedAluSss	GedAluDtaUltAltSnh	GedAluDtaLgn
        //         	GedAluTipoSanguineo	GedAluInfAdicionais	GedAluPartBenSocial	GedAluNumPasseLivre
        //             	GedAluPasseLivre	GedAluRespScrt	GedAluPergScrt
   
       $fileName = 'Transcolar2022.csv';
    
       $tasks = \DB::select("SELECT TOP (1000) [GerPesCod]
       ,[GerPesNom]
       ,[GerPesCPF]
       ,[GerPesRG]
       ,[GerPesDtaExp]
       ,[GerPesDtaNasc]
 
   FROM [sig_educa].[GER].[TBGERPESSOA] where GerPesNom = 'IGOR DE ARRUDA BATISTA'");  


      // dd($tasks);
    
            // $headers = array(
            //     "Content-type"        => "text/csv",
            //     "Content-Disposition" => "attachment; filename=$fileName",
            //     "Pragma"              => "no-cache",
            //     "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            //     "Expires"             => "0"
            // );
    
            $columns = array('GerPesNom', 'GerPesCPF', 'GerPesRG', 'GerPesDtaExp', 'GerPesDtaNasc', 

            );

          //  $callback = function() use($tasks, $columns) {

                $file = fopen($fileName, 'w');
                $delimiter = ';'; //alterar o padrão para o delimitador -> . (ponto)
                fputcsv($file, $columns, $delimiter);
             
                foreach  ($tasks as $task) {

                    $row['GerPesNom']                           = $task->GerPesNom;
                    $row['GerPesCPF']                           = $task->GerPesCPF;
                    $row['GerPesRG']                            = $task->GerPesRG;
                    $row['GerPesDtaExp']                        = $task->GerPesDtaExp;
                    $row['GerPesDtaNasc']                       = $task->GerPesDtaNasc;                
                    
            fputcsv($file, array( $row['GerPesNom'], 
                                  $row['GerPesCPF'], $row['GerPesRG'], 
                                  $row['GerPesDtaNasc']), $delimiter );

                }
                
            fclose($file);
     
                // envia para o ftp
            $file_local = File::get("../public/Transcolar2022.csv");
            //   dd($file_local);
            Storage::disk('sftp')->put($fileName, $file_local);






}
        


}
