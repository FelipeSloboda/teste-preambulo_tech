<?php
// backend/includes/CobrancaController.php

require_once 'config.php';

class CobrancaController {
    // Retorna todos os clientes/cobranças
    public function index() {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT 
                c.*, 
                e.endereco, e.bairro, e.cidade, e.estado, 
                ct.contrato, 
                cb.numero_parcela, cb.data_vencimento, cb.valor_parcela
                FROM clientes c
                LEFT JOIN enderecos e ON c.id = e.cliente_id
                LEFT JOIN contratos ct ON c.id = ct.cliente_id
                LEFT JOIN cobrancas cb ON c.id = cb.cliente_id");
            $stmt->execute();
            $cobrancas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retorno = [
                "cabecalho" => [
                    "http code" => 200,
                    "status" => "sucesso"
                ],
                "retorno" => [$cobrancas]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $retorno = [
                "cabecalho" => [
                    "http code" => 500,
                    "status" => "erro"
                ],
                "retorno" => ["erro" => "Erro ao localizar cliente: " . $e->getMessage()]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }

    // Retorna um cliente/cobrança pelo id
    public function show($id) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT 
                c.*, 
                e.endereco, e.bairro, e.cidade, e.estado, 
                ct.contrato, 
                cb.numero_parcela, cb.data_vencimento, cb.valor_parcela
                FROM clientes c
                LEFT JOIN enderecos e ON c.id = e.cliente_id
                LEFT JOIN contratos ct ON c.id = ct.cliente_id
                LEFT JOIN cobrancas cb ON c.id = cb.cliente_id
                WHERE c.id = ?");
            $stmt->execute([$id]);
            $cobranca = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $retorno = [
                "cabecalho" => [
                    "http code" => 200,
                    "status" => "sucesso"
                ],
                "retorno" => [$cobranca]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $retorno = [
                "cabecalho" => [
                    "http code" => 500,
                    "status" => "erro"
                ],
                "retorno" => ["erro" => "Erro ao localizar cliente: " . $e->getMessage()]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }

    // Edita um cliente/cobrança pelo id
    public function update($id, $nome, $cpf, $telefone, $email, $endereco, $bairro, $cidade, $estado, $contrato, $numero_parcela, $data_vencimento, $valor_parcela) {
        if (empty($nome)) {
            return $this->erroValidacao("Campo NOME invalido");
        }else if (empty($cpf) || strlen($nome) < 11) {
            return $this->erroValidacao("Campo CPF invalido");
        }elseif (empty($telefone) || strlen($nome) < 18) {
            return $this->erroValidacao("Campo TELEFONE invalido");
        }elseif (empty($email)) {
            return $this->erroValidacao("Campo EMAIL invalido");
        }elseif (empty($endereco) || empty($bairro) || empty($cidade) || empty($estado)) {
            return $this->erroValidacao("Campos ENDERECO invalido");
        }elseif (empty($contrato)) {
            return $this->erroValidacao("Campo CONTRATO invalido");
        }elseif (!is_numeric($numero_parcela) || $numero_parcela <= 0) {
            return $this->erroValidacao("Campo NUM PARCELA invalido");
        }elseif (empty($data_vencimento)) {
            return $this->erroValidacao("Campo DATA VENCIMENTO invalido");
        }elseif (!is_numeric($valor_parcela) || $valor_parcela <= 0) {
            return $this->erroValidacao("Campo VALOR PARCELA invalido");
        }
        else{
            try {
                $pdo = getDbConnection();
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("UPDATE cobrancas SET numero_parcela = ?, data_vencimento = ?, valor_parcela = ? WHERE cliente_id = ?");
                $stmt->execute([$numero_parcela, $data_vencimento, $valor_parcela, $id]);
                $stmt = $pdo->prepare("UPDATE contratos set contrato = ? WHERE cliente_id = ?");
                $stmt->execute([$contrato, $id]);
                $stmt = $pdo->prepare("UPDATE enderecos SET endereco = ?, bairro = ?, cidade = ?, estado = ? WHERE cliente_id = ?");
                $stmt->execute([$endereco, $bairro, $cidade, $estado, $id]);
                $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, cpf = ?, telefone = ?, email = ? WHERE id = ?");
                $stmt->execute([$nome, $cpf, $telefone, $email, $id]);
                $pdo->commit();

                $retorno = [
                    "cabecalho" => [
                        "http code" => 200,
                        "status" => "sucesso"
                    ],
                    "retorno" => ["sucesso" => "Cliente atualizado com sucesso"]
                ];
                return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                $pdo->rollBack();
                $retorno = [
                    "cabecalho" => [
                        "http code" => 500,
                        "status" => "erro"
                    ],
                    "retorno" => ["erro" => "Erro ao atualizar cliente: " . $e->getMessage()]
                ];
                return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        }
    }

    // Exclui um cliente/cobrança pelo id
    public function delete($id) {
        try {
            $pdo = getDbConnection();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("DELETE FROM cobrancas WHERE cliente_id = ?");
            $stmt->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM enderecos WHERE cliente_id = ?");
            $stmt->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM contratos WHERE cliente_id = ?");
            $stmt->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $pdo->commit();

            $retorno = [
                "cabecalho" => [
                    "http code" => 200,
                    "status" => "sucesso"
                ],
                "retorno" => ["sucesso" => "Cobranças excluida com sucesso."]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $pdo->rollBack();
            $retorno = [
                "cabecalho" => [
                    "http code" => 500,
                    "status" => "erro"
                ],
                "retorno" => ["erro" => "Erro ao excluir Cobranças : " . $e->getMessage()]
            ];
            return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
    public function erroValidacao($msg){
        $retorno = [
            "cabecalho" => [
                "http code" => 400,
                "status" => "erro"
            ],
            "retorno" => ["erro" => $msg]
        ];
        return json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

?>
