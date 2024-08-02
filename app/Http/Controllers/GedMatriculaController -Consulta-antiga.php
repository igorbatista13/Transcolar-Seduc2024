<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Response;

use illuminate\Support\Facades\DB;


use App\Models\GedMatricula;


class GedMatriculaController extends Controller
{
    public function exportCsv(Request $request)
    {
    
        //GedAluCod	GedAluIdINEP	GedAluPartPrjSoc	GedAluLotPartPrjSoc	GedAluAtenEscDif	
        // GedAluTransPub	GedAluRespTransPub	GedAluRecAteEduEsp	GedAluIncPor	GedAluIncEm	GedAluAltPor
        // 	GedAluAltEm	GedAluInativ	GedAluAlfab	GedAluLogin	GedAluSnh	GedAluEmailConf	GedAluCodConfEmail
        //     	GedAluMaeInep	GedAluDtaCadPDA	GedAluSss	GedAluDtaUltAltSnh	GedAluDtaLgn
        //         	GedAluTipoSanguineo	GedAluInfAdicionais	GedAluPartBenSocial	GedAluNumPasseLivre
        //             	GedAluPasseLivre	GedAluRespScrt	GedAluPergScrt
    
       $fileName = 'Transcolar.csv';
    
       $tasks = \DB::select("SELECT DISTINCT
       cast(m.GerAnoLetCod as int) as anobase,
       cast(CASE WHEN L.GerLotIdINEP = 51036550 THEN 51027 ELSE org.GerOrgRegCodEdu END as varchar) AS sre, /*regional*/
       'Estadual' as tipo_dependencia,
       COALESCE(L.GerLotIdINEP,9999999) AS inep_escola,
       l.GerLotNom AS nome_escola,
       pl.GerPrmEndLogNom as endereco_escola,
       pl.GerPrmLotNmrLog as numero_escola,
       '' as complemento_escola,
       pl.GerPrmBaiNom as bairro_escola,
       COALESCE(C.GerCidId,-1) AS codibge7_escola,
       c.GerCidNom AS municipio_escola,
       'MT' as estado_escola,
       pl.GerPrmCEP as cep_escola,
       '('+RTRIM(LTRIM(pl.GerPrmLotDDD)) +') '+ RTRIM(LTRIM(pl.GerPrmLotTel)) as ddd_telefone_escola,
       CASE MR2.GerMatRegModalidadeEduca 
               WHEN 2 THEN 'ENSINO ESPECIAL'
               WHEN 3 THEN 'EJA'
           ELSE CASE MR2.GerMatRegAreaAtuEduca 
                   WHEN 1 THEN 'ENSINO FUNDAMENTAL 1'
                   WHEN 2 THEN 'ENSINO FUNDAMENTAL 2'
                   WHEN 3 THEN 'ENSINO M�DIO'
                   WHEN 4 THEN 'PR�-ESCOLA'
               END
       END AS tipo_ensino,
       CASE MR2.GerMatRegModalidadeEduca 
               WHEN 2 THEN 'EE'
               WHEN 3 THEN 'JA'
           ELSE CASE MR2.GerMatRegAreaAtuEduca 
                   WHEN 1 THEN 'EF'
                   WHEN 2 THEN 'EF2'
                   WHEN 3 THEN 'EM'
                   WHEN 4 THEN 'PE'
               END
       END AS sigla_tipo_ensino,
       me.GerModalidadeEnsinoDsc as modalidade,
       '' AS submodalidade,
       COALESCE(eee.GerEtpEnsNom, ee.GerEtapaEnsinoDsc) AS serie,
       m.GerAnoLetCod as periodo_letivo,
       '' as sigla_concessionaria_escola, 
       pl.GerPrmLotUndUC as codigo_energia_escola,
       pl.GerPrmLatitude as latdec_posicao_original_escola,
       pl.GerPrmLongitude as londec_posicao_original_escola,
       C2.GerCidId AS codibge7_aluno,
       c2.GerCidNom as municipio_aluno,
       trn.GerTrnDsc as turno_aluno,
       m.GedAluCod as registro_aluno,
       m.GedMatCod as matricula_aluno,
       a.GedAluIdINEP as inep_aluno,
       p.GerPesNom as nome_aluno,
       p.GerPesDtaNasc as data_nascimento,
       p.GerPesEnd as endereco_aluno,
       P.GerPesNmrLog AS numero_aluno,
       p.GerPesCmpLog as complemento_aluno,
       p.GerPesBairro as bairro_aluno,
       c2.GerEstSgl as estado_aluno,
       p.GerPesCEP as cep_aluno,
       '('+RTRIM(LTRIM(p.GerPesTElCelDDD)) +') '+ RTRIM(LTRIM(p.GerPesTelCel)) as ddd_telefone_aluno,
       P.GerPesNomMae as nome_mae_aluno,
       '' as cpf_mae,
       CASE p.GerPesDistCod WHEN 1 THEN 'ENERGISA' WHEN 2 THEN 'CERGRO' WHEN 3 THEN 'ENEL' else '' END as sigla_concessionaria_aluno,
       p.GerPesUC as codigo_energia_aluno,
       '' AS latdec_posicao_original_aluno,
       '' AS londec_posicao_original_aluno,
       CASE WHEN EXISTS (SELECT NE.GedAluNecEduEspCod 
                         FROM GED.TBGEDNECESSIDADESESPECIAIS NE
                         WHERE NE.GedAluCod = M.GedAluCod AND NE.GedAluNecEduEspCod = 6 /*DEFICI�NCIA F�SICA*/) THEN 'S' ELSE 'N' END AS mobilidade_reduzida,
       CASE WHEN EXISTS (SELECT NE.GedAluNecEduEspCod 
                         FROM GED.TBGEDNECESSIDADESESPECIAIS NE
                         WHERE NE.GedAluCod = M.GedAluCod AND NE.GedAluNecEduEspCod = 6 /*DEFICI�NCIA F�SICA*/) THEN 'S' ELSE 'N' END AS veiculo_especial,
       CASE WHEN M.GedMatUtiTraEsc = 0 and a.GedAluPasseLivre = 0 THEN 'S' ELSE 'N' END AS frota_propria, /*aluno n�o utiliza Passe Livre nem rural*/
       CASE WHEN M.GedMatUtiTraEsc = 1 THEN 'S' ELSE 'N' END AS utiliza_transporte_rural,
       CASE WHEN M.GedMatAluEsp = 1 OR A.GedAluRecAteEduEsp = 1 THEN 'S' ELSE 'N' END AS possui_deficiencia,
       GETDATE() AS dt_atualizacao
       FROM GED.TBGEDMATRICULA m (NOLOCK)
       INNER JOIN ged.TBGEDALUNO a (NOLOCK) ON a.GedAluCod = m.GedAluCod
       INNER JOIN GER.TBGERPESSOA p (NOLOCK) ON m.GedAluCod = p.GerPesCod
       INNER JOIN GER.TBGERLOTACAO l (NOLOCK) ON l.GerLotCod = m.GerLotCod
       INNER JOIN GER.TBGERPARMLOTACAO pl (NOLOCK) ON PL.GerLotCod = M.GerLotCod AND PL.GerAnoLetCod = M.GerAnoLetCod
       INNER JOIN GER.TBGERCIDADE c (NOLOCK) ON l.GerCidId = c.GerCidId
       INNER JOIN GER.TBGERCIDADE C2 ON C2.GerCidId = P.GerPesEndCid
       INNER JOIN GER.TBGERTURMA t(NOLOCK) ON t.GerAnoLetCod = m.GerAnoLetCod AND t.GerLotCod = m.GerLotCod AND t.GerAmbCod = m.GerAmbCod AND t.GerTrnCod = m.GerTrnCod AND t.GerTurCod = m.GerTurCod
       INNER JOIN GER.TBGERTURNO trn (NOLOCK) on trn.GerTrnCod = t.GerTrnCod
       INNER JOIN GER.TBGERMATRIZ mtz1 (NOLOCK) ON mtz1.GerAnoLetCod = t.GerAnoLetCod AND mtz1.GerMatCod = COALESCE(M.GedMatMltCicMatCod,t.GerMatCod)
       INNER JOIN GER.TBGERMATRIZ mtz2 (NOLOCK) ON mtz2.GerAnoLetCod = t.GerAnoLetCod AND mtz2.GerMatCod = t.GerMatCod /*Matriz turma*/
       INNER JOIN GER.TBGERMATRIZREGRA mr (NOLOCK) ON mr.GerMatRegCod = mtz1.GerMatCod AND mr.GerAnoRegLetCod = mtz1.GerAnoLetCod /* Regra Matriz desmembrada*/
       INNER JOIN GER.TBGERMATRIZREGRA mr2 (NOLOCK) ON mr2.GerMatRegCod = t.GerMatCod AND mr2.GerAnoRegLetCod = t.GerAnoLetCod /*Regra matriz turma*/
       INNER JOIN GER.TBGERMODALIDADEENSINO me (NOLOCK) ON me.GerModalidadeEnsinoId = mtz2.GerModalidadeEnsinoId
       LEFT JOIN GER.TBGERETAPAENSINOEDUCACENSO eee (NOLOCK) ON eee.GerEtpEnsCod = mr.GerMatRegEnsCod
       LEFT JOIN GER.TBGERETAPAENSINO ee (NOLOCK) ON ee.GerEtapaEnsinoId = mtz1.GerEtapaEnsinoId
       LEFT JOIN GER.TBGERORGAOREGIONAL org (NOLOCK) ON org.GerOrgRegCod = pl.GerOrgRegCod
       WHERE m.GerAnoLetCod = year(getdate())
       AND EXISTS (SELECT 1 FROM GED.TBGEDMATRICULADISC md (NOLOCK) WHERE md.GedMatCod = m.GedMatCod AND md.GedMatDiscSit IN (5, 6, 7))
       AND m.GedMatDep = 0 ");  


      // dd($tasks);
    
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );
    
            $columns = array('anobase', 'sre', 'tipo_dependencia', 'inep_escola', 'nome_escola', 'endereco_escola', 'numero_escola', 'complemento_escola', 'bairro_escola',
                             'codibge7_escola', 'municipio_escola', 'estado_escola', 'cep_escola', 'ddd_telefone_escola', 'tipo_ensino', 'sigla_tipo_ensino', 'modalidade',
                             'submodalidade', 'serie', 'periodo_letivo', 'sigla_concessionaria_escola', 'codigo_energia_escola', 'latdec_posicao_original_escola', 'londec_posicao_original_escola',

                             'codibge7_aluno', 'municipio_aluno', 'turno_aluno', 'registro_aluno', 'matricula_aluno', 'inep_aluno', 'nome_aluno', 'data_nascimento', 'endereco_aluno', 'numero_aluno',
                             'complemento_aluno', 'bairro_aluno', 'estado_aluno', 'cep_aluno', 'ddd_telefone_aluno', 'nome_mae_aluno', 'cpf_mae', 'sigla_concessionaria_aluno', 'codigo_energia_aluno',
                             'latdec_posicao_original_aluno', 'londec_posicao_original_aluno', 'mobilidade_reduzida', 'veiculo_especial', 'frota_propria', 'utiliza_transporte_rural', 'possui_deficiencia', 
                             'dt_atualizacao'

            );
    
//   $callback = function() use($tasks, $columns) {
                $file = fopen($fileName, 'w');
                $delimiter = ';'; //alterar o padrão para o delimitador -> . (ponto)
                fputcsv($file, $columns, $delimiter);
    
                foreach ($tasks as $task) {
                    $row['anobase']                             = $task->anobase;
                    $row['sre']                                 = $task->sre;
                    $row['tipo_dependencia']                    = $task->tipo_dependencia;
                    $row['inep_escola']                         = $task->inep_escola;
                    $row['nome_escola']                         = $task->nome_escola;
                    $row['endereco_escola']                     = $task->endereco_escola;
                    $row['numero_escola']                       = $task->numero_escola;
                    $row['complemento_escola']                  = $task->complemento_escola;
                    $row['bairro_escola']                       = $task->bairro_escola;
                    $row['codibge7_escola']                     = $task->codibge7_escola;
                    $row['municipio_escola']                    = $task->municipio_escola;
                    $row['estado_escola']                       = $task->estado_escola;
                    $row['cep_escola']                          = $task->cep_escola;
                    $row['ddd_telefone_escola']                 = $task->ddd_telefone_escola;
                    $row['tipo_ensino']                         = $task->tipo_ensino;
                    $row['sigla_tipo_ensino']                   = $task->sigla_tipo_ensino;
                    $row['modalidade']                          = $task->modalidade;
                    $row['submodalidade']                       = $task->submodalidade;
                    $row['serie']                               = $task->serie;
                    $row['periodo_letivo']                      = $task->periodo_letivo;
                    $row['sigla_concessionaria_escola']         = $task->sigla_concessionaria_escola;
                    $row['codigo_energia_escola']               = $task->codigo_energia_escola;
                    $row['latdec_posicao_original_escola']      = $task->latdec_posicao_original_escola;
                    $row['londec_posicao_original_escola']      = $task->londec_posicao_original_escola;
                    //24 itens até aqui
                    $row['codibge7_aluno']                      = $task->codibge7_aluno;
                    $row['municipio_aluno']                     = $task->municipio_aluno;
                    $row['turno_aluno']                         = $task->turno_aluno;
                    $row['registro_aluno']                      = $task->registro_aluno;
                    $row['matricula_aluno']                     = $task->matricula_aluno;
                    $row['inep_aluno']                          = $task->inep_aluno;
                    $row['nome_aluno']                          = $task->nome_aluno;
                    $row['data_nascimento']                     = $task->data_nascimento;
                    $row['endereco_aluno']                      = $task->endereco_aluno;
                    $row['numero_aluno']                        = $task->numero_aluno;
                    $row['complemento_aluno']                   = $task->complemento_aluno;
                    $row['bairro_aluno']                        = $task->bairro_aluno;
                    $row['estado_aluno']                        = $task->estado_aluno;
                    $row['cep_aluno']                           = $task->cep_aluno;
                    $row['ddd_telefone_aluno']                  = $task->ddd_telefone_aluno;
                    $row['nome_mae_aluno']                      = $task->nome_mae_aluno;
                    $row['cpf_mae']                             = $task->cpf_mae;
                    $row['sigla_concessionaria_aluno']          = $task->sigla_concessionaria_aluno;
                    $row['codigo_energia_aluno']                = $task->codigo_energia_aluno;
                    $row['latdec_posicao_original_aluno']       = $task->latdec_posicao_original_aluno;
                    $row['londec_posicao_original_aluno']       = $task->londec_posicao_original_aluno;
                    $row['mobilidade_reduzida']                 = $task->mobilidade_reduzida;
                    $row['veiculo_especial']                    = $task->veiculo_especial;
                    $row['frota_propria']                       = $task->frota_propria;
                    $row['utiliza_transporte_rural']            = $task->utiliza_transporte_rural;
                    $row['possui_deficiencia']                  = $task->possui_deficiencia;
                    $row['dt_atualizacao']                      = $task->dt_atualizacao;

                    //51 itens totais
       
    
                    fputcsv($file, array($row['anobase'], $row['sre'], $row['tipo_dependencia'], $row['inep_escola'], $row['nome_escola'],
                    $row['endereco_escola'], $row['numero_escola'], $row['complemento_escola'], $row['bairro_escola'], $row['codibge7_escola'],
                    $row['municipio_escola'], $row['estado_escola'], $row['cep_escola'], $row['ddd_telefone_escola'], $row['tipo_ensino'],
                    //15
                    $row['sigla_tipo_ensino'], $row['modalidade'], $row['submodalidade'], $row['serie'], $row['periodo_letivo'],
                    $row['sigla_concessionaria_escola'], $row['codigo_energia_escola'], $row['latdec_posicao_original_escola'], $row['londec_posicao_original_escola'], $row['codibge7_aluno'],
                    $row['municipio_aluno'], $row['turno_aluno'], $row['registro_aluno'], $row['matricula_aluno'], $row['inep_aluno'],
                    //30
                    $row['nome_aluno'], $row['data_nascimento'], $row['endereco_aluno'], $row['numero_aluno'], $row['complemento_aluno'],
                    $row['bairro_aluno'], $row['estado_aluno'], $row['cep_aluno'], $row['ddd_telefone_aluno'], $row['nome_mae_aluno'],

                    $row['cpf_mae'], $row['sigla_concessionaria_aluno'], $row['codigo_energia_aluno'], $row['latdec_posicao_original_aluno'], $row['londec_posicao_original_aluno'],
                    $row['mobilidade_reduzida'], $row['veiculo_especial'], $row['frota_propria'], $row['utiliza_transporte_rural'], $row['possui_deficiencia'],
                    $row['dt_atualizacao']), $delimiter );
                }
    
                fclose($file);
            

             // envia para o ftp
             $file_local = File::get("../public/Transcolar.csv");
             //   dd($file_local);
             Storage::disk('sftp')->put($fileName, $file_local);
 
 

            }



}
