<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'bibliotecas/PHPMailer/src/Exception.php';
require 'bibliotecas/PHPMailer/src/PHPMailer.php';
require 'bibliotecas/PHPMailer/src/SMTP.php';

function traduz_prioridade($codigo)
{

    $prioridade = '';

    switch ($codigo) {

        case 1:

            $prioridade = 'Baixa';
            break;

        case 2:

        $prioridade = 'Média';
        break;

        case 3:

        $prioridade = 'Alta';
        break;





    }

    return $prioridade;
}


function traduz_data_para_banco($data)
{
    if ($data == "") {

        return "";

    }

    $dados = explode("/", $data);

    if (count($dados) != 3) {

        return $data;

    }

    $data_mysql = "{$dados[2]}-{$dados[1]}-{$dados[0]}";

    return $data_mysql;
}

function traduz_data_para_exibir($data)
{
    if ($data == "" OR $data == "0000-00-00") {

        return "";
    }

    $dados = explode("-", $data);

    if (count($dados) != 3) {

        return $data;

    }

    $data_exibir = "{$dados[2]}/{$dados[1]}/{$dados[0]}";

    return $data_exibir;

}

function traduz_concluida($concluida)
{
    if ($concluida == 1) {

        return 'Sim';
    }

    return 'Não';

}

function tem_post()
{
    if (count($_POST) > 0) {

        return true;
    }

    return false;
}

function validar_data($data)
{
    $padrao = '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/';
    $resultado = preg_match($padrao, $data);

    if (!$resultado) {

        return false;
    }

    $dados = explode('/', $data);

    $dia = $dados[0];
    $mes = $dados[1];
    $ano = $dados[2];

    $resultado = checkdate($mes, $dia, $ano);

    return $resultado;
}

function tratar_anexo($anexo) {

    $padrao = '/^.+(\.pdf|\.zip)$/';
    $resultado = preg_match($padrao, $anexo['name']);

    if (!$resultado) {

        return false;
    }

    move_uploaded_file($anexo['tmp_name'], "anexos/{$anexo['name']}");

    return true;
}

function preparar_corpo_email($tarefa, $anexos)
{

    ob_start();

    include "template_email.php";

    $corpo = ob_get_contents();

    ob_end_clean();

    return $corpo;

}

function enviar_email($tarefa, $anexos = array())
{

    $email = new PHPMailer();

    $email->isSMTP();
    $email->Host = 'smtp.live.com';
    $email->Port = 587;
    $email->SMTPSecure = 'tls';
    $email->SMTPAuth = true;
    $email->Username = "sancrisxa@htomail.com";
    $email->Password = "Sa1Bri2Na3";
    $email->setFrom("sancrisxa@hotmail.com", "Avisador de Tarefas");
    $email->addAddress(EMAIL_NOTIFICACAO);

    $subject->Suject = "Aviso de tarefa: {$tarefa['nome']}"; 

    $corpo = preparar_corpo_email($tarefa, $anexos);
    $email->msgHTML($corpo);

    foreach($anexos as $anexo) {

        $email->addAttachment("anexos/{$anexo['arquivo']}");
    }

    $email->send();
}



