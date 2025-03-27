<?php
// backend/public/index.php

header("Content-Type: application/json"); //PADRAO JSON
header("Access-Control-Allow-Origin: *"); //AUTORIZA CORS
header("Access-Control-Allow-Methods: GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../includes/CsvController.php';
require_once '../includes/CobrancaController.php';

$cobranca = new CobrancaController();
$csv = new CsvController();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/preambulo_tech/backend/public/index.php', '', $uri);

$uriParts = explode('/', trim($uri, '/'));

//echo json_encode(['endpoint' => $uri]);
//echo json_encode(['method' => $method]);
//if (isset($uriParts[2])) { echo json_encode(['id' => $uriParts[2]]); }

if ($uri == "/api/csv" && $method == "POST") {
    echo $csv->ImportarCsv();
}
elseif (isset($uriParts[1]) && $uriParts[0] == "api" && $uriParts[1] == "cobrancas" && $method == "GET") {
    if (isset($uriParts[2]) && is_numeric($uriParts[2])) {
        $id = intval($uriParts[2]); // ID
        echo $cobranca->show($id);
    } else {
        echo $cobranca->index();
    }
}elseif ($uriParts[0] == "api" && $uriParts[1] == "cobrancas" && $method == "PUT") {
    if (isset($uriParts[2]) && is_numeric($uriParts[2])) {
        $id = intval($uriParts[2]); // ID

        // Recupera os dados da requisição
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        // Verifique se os dados necessários estão presentes
        if (isset($data->nome) && isset($data->cpf) && isset($data->telefone) && isset($data->email) && isset($data->endereco) && isset($data->bairro) && isset($data->cidade) && isset($data->estado) && isset($data->contrato) && isset($data->numero_parcela) && isset($data->data_vencimento) && isset($data->valor_parcela)
        ){
            echo $cobranca->update($id, $data->nome, $data->cpf, $data->telefone,   $data->email, $data->endereco, $data->bairro, $data->cidade, $data->estado, $data->contrato, $data->numero_parcela, $data->data_vencimento, $data->valor_parcela);
        } else {
            $retorno = [
                "cabecalho" => [
                    "http code" => 400,
                    "status" => "erro"
                ],
                "retorno" => ["erro" => "Dados invalidos ou insuficientes."]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }else {
        $retorno = [
            "cabecalho" => [
                "http code" => 400,
                "status" => "erro"
            ],
            "retorno" => ["erro" => "ID invalido ou nao informado."]
        ];
        return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} elseif (isset($uriParts[1]) && $uriParts[0] == "api" && $uriParts[1] == "cobrancas" && $method == "DELETE") {
    if (isset($uriParts[2]) && is_numeric($uriParts[2])) {
        $id = intval($uriParts[2]); // ID
        echo $cobranca->delete($id);
    } else {
        $retorno = [
            "cabecalho" => [
                "http code" => 400,
                "status" => "erro"
            ],
            "retorno" => ["erro" => "ID invalido ou nao informado."]
        ];
        return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} 

else {
    echo json_encode(['Erro' => 'Endpoint não encontrado.']);
}

?>
