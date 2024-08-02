<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\{
    TestEmailController,
    PessoaController,
    HomeController,
    GedMatriculaController,
    GedAlunosMatricula
};


Route::get('/matricula/csv', [GedMatriculaController::class, 'exportCsv']);
Route::get('/pessoa/csv',    [PessoaController::class, 'exportCsv']);
Route::get('/mail/csv',          [TestEmailController::class, 'test']);

// Route::get('/',              [HomeController::class, 'index']);
// Route::get('/alunos/csv',    [GedAlunosMatricula::class, 'exportCsv']);
// Route::get('/ead/csv',       [EADController::class, 'exportCsv']);
// Route::post('/logout',       [HomeController::class, 'logout']);

// Route::get('/mail', function () {
//     $data = [
//         'name' => 'IGOR',
//         'message' => 'Teste de E-mail'
//     ];

//     Mail::send('emails.test', $data, function ($message) {
//         $message->to('igorarrudabatista@gmail.com', 'Recipient Name')
//                 ->subject('TESTANDO!!!');
//     });

//     return 'Email sent!';
// });

