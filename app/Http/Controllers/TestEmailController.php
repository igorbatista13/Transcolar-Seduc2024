<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CsvExported; // Certifique-se de ter o Mail mailable configurado


class TestEmailController extends Controller
{
public function test() {

$data = [
    'name' => 'IGOR',
    'message' => 'Teste de E-mail'
];

Mail::send('emails.test', $data, function ($message) {
    $message->to('igorarrudabatista@gmail.com', 'Recipient Name')
            ->subject('TESTANDO!!!');
});

Mail::send('emails.report', $data, function ($message) {
    $message->to('igorarrudabatista@gmail.com', 'Nome do Destinatário')
            ->subject('Relatório Disponível');
});

return 'Email sent!';

}
}
