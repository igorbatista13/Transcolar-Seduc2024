<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificação de Exportação de CSV</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0a3f79;
            color: #ffffff;
            padding: 12px 22px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            font-size: 16px;
        }
        .content {
            padding: 24px;
            line-height: 1.8;
        }
        .footer {
            padding: 10px 20px;
            background-color: #f1f1f1;
            border-radius: 0 0 8px 8px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Notificação de Exportação dos arquivos do <b> TRANSCOLAR </b>
        </div>
        <div class="content">
            <p>Olá!</p>
            <p>Arquivo gerado! </p>
            <p>Data: {{ date('d/m/Y') }}</p>
            <p>{{ $message }}</p>
            <a href="{{ asset('/csv/Transcolar.csv')}}"> Download do arquivo </a>
            <p>Se você tiver alguma dúvida, entre em contato conosco.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Desenvolvido pela Equipe de TI da <b>  SEDUC-MT - 2024. </b> </p>
        </div>
    </div>
</body>
</html>
