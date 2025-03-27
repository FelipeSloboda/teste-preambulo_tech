<?php
// backend/includes/CsvController.php

require_once 'config.php';

class CsvController {
    // Trata o dados e formatações
    // Persiste os dados no banco de dados

    public function ImportarCsv() {
        // Verifica se um arquivo foi enviado
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file = $_FILES['file']['tmp_name'];

            // Abre o arquivo CSV
            if (($handle = fopen($file, "r", )) !== FALSE) {
                $pdo = getDbConnection();
                $line = 0;
                while (($data = fgetcsv($handle, 1000, ';', '"', '\\')) !== FALSE) {
                    if ($line > 0) { // Ignora a primeira linha (cabeçalho)
                        //CLIENTE
                        $nome = $data[0];
                        $cpf = $data[1]; 
                        $telefone = $data[6]; 
                        $email = $data[7];                  

                        $stmt = $pdo->prepare("INSERT INTO clientes (nome, cpf, telefone,email) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$nome, $cpf, $telefone, $email]);
                        $cliente_id = $pdo->lastInsertId();


                        //ENDERECO
                        // cliente_id
                        $endereco = $data[2]; 
                        $bairro = $data[3]; 
                        $cidade = $data[4]; 
                        $estado = $data[5]; 

                        $stmt = $pdo->prepare("INSERT INTO enderecos (cliente_id, endereco, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$cliente_id, $endereco, $bairro, $cidade, $estado]);
                        
                        //CONTRATOS
                        // cliente_id
                        $contrato = $data[9]; 

                        $stmt = $pdo->prepare("INSERT INTO contratos (cliente_id, contrato) VALUES (?, ?)");
                        $stmt->execute([$cliente_id, $contrato]);
                        $contrato_id = $pdo->lastInsertId();

                        //COBRANÇAS
                        // cliente_id
                        // contrato_id
                        $numero_parcela = $data[8]; 
                        $data_original = $data[10];
                        $data_obj = DateTime::createFromFormat('d/m/Y', $data_original);
                        $data_vencimento = $data_obj->format('Y-m-d');
                        $valor_parcela = $data[11];
                        
                        $stmt = $pdo->prepare("INSERT INTO cobrancas (cliente_id, contrato_id, numero_parcela, data_vencimento, valor_parcela) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$cliente_id, $contrato_id, $numero_parcela, $data_vencimento, $valor_parcela]);

                    }
                    $line++;
                }
                fclose($handle);
                $retorno = [
                    "cabecalho" => [
                        "http code" => 200,
                        "status" => "sucesso"
                    ],
                    "retorno" => ["sucesso" => "Dados do CSV importado para o banco de dados."]
                ];
                return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                $retorno = [
                    "cabecalho" => [
                        "http code" => 500,
                        "status" => "erro"
                    ],
                    "retorno" => ["erro" => "Erro ao abrir o CSV."]
                ];
                return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            $retorno = [
                "cabecalho" => [
                    "http code" => 400,
                    "status" => "erro"
                ],
                "retorno" => ["erro" => "Arquivo CSV não enviado."]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
}

?>
