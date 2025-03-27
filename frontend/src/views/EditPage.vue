<template>
  <div class="container mt-5">
    <h2 class="text-center">Editar Cliente/Cobrança: #{{ id }}</h2>

    <!-- FORM -->
    <div v-if="item">
      <form @submit.prevent="saveItem">
        <!-- Nome -->
        <div class="form-group">
          <label for="nome">Nome</label>
          <input v-model="item.nome" type="text" class="form-control" id="nome" maxlength="50"/>
        </div>

        <!-- CPF -->
        <div class="form-group">
          <label for="cpf">CPF</label>
          <input v-model="item.cpf" type="text" class="form-control" id="cpf" required  maxlength="11"/>
        </div>

        <!-- Endereço -->
        <div class="form-group">
          <label for="endereco">Endereço</label>
          <input v-model="item.endereco" type="text" class="form-control" id="endereco" required maxlength="50"/>
        </div>

        <!-- Bairro -->
        <div class="form-group">
          <label for="bairro">Bairro</label>
          <input v-model="item.bairro" type="text" class="form-control" id="bairro" required maxlength="50"/>
        </div>

        <!-- Cidade -->
        <div class="form-group">
          <label for="cidade">Cidade</label>
          <input v-model="item.cidade" type="text" class="form-control" id="cidade" required maxlength="50"/>
        </div>

        <!-- Estado -->
        <div class="form-group">
          <label for="estado">Estado</label>
          <input v-model="item.estado" type="text" class="form-control" id="estado" required maxlength="2"/>
        </div>

        <!-- Telefone -->
        <div class="form-group">
          <label for="telefone">Telefone</label>
          <input v-model="item.telefone" type="text" class="form-control" id="telefone" required maxlength="20"/>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email</label>
          <input v-model="item.email" type="email" class="form-control" id="email" required maxlength="50" />
        </div>

        <!-- Número da Parcela -->
        <div class="form-group">
          <label for="numero_parcela">Número da Parcela</label>
          <input v-model="item.numero_parcela" type="number" class="form-control" id="numero_parcela" required min="1"/>
        </div>

        <!-- Contrato -->
        <div class="form-group">
          <label for="contrato">Contrato</label>
          <input v-model="item.contrato" type="text" class="form-control" id="contrato" required maxlength="100"/>
        </div>

        <!-- Data de Vencimento -->
        <div class="form-group">
          <label for="data_vencimento">Data de Vencimento</label>
          <input v-model="item.data_vencimento" type="date" class="form-control" id="data_vencimento" required/>
        </div>

        <!-- Valor da Parcela -->
        <div class="form-group">
          <label for="valor_parcela">Valor da Parcela R$</label>
          <input v-model="item.valor_parcela" type="number" class="form-control" id="valor_parcela" step="any" required/>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Atualizar</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "EditPage",
  props: ["id"], //ID P EDITAR
  data() {
    return {
      item: null, 
    };
  },
  mounted() {
    this.getItem();
  },
  methods: {
    // FUNCAO BUSCAR CLIENTE/COBRANCA
    async getItem() {
      try {
        const response = await axios.get(
          `http://localhost/preambulo_tech/backend/public/index.php/api/cobrancas/${this.id}`
        );
        this.item = response.data.retorno[0][0];
      } catch (error) {
        console.error("Erro ao carregar item para edição", error);
        alert("Erro ao carregar item para edição");
      }
    },

    // FUNCAO PARA ATUALIZAR
    async saveItem() {
      try {
        const response = await axios.put(
          `http://localhost/preambulo_tech/backend/public/index.php/api/cobrancas/${this.id}`,
          this.item
        );
          const responseData = response.data;         
          const httpCode = responseData.cabecalho["http code"];

        if (httpCode === 200) {
          console.log("Cliente/cobranca atualizado com sucesso.");
          alert("Cliente/cobranca atualizado com sucesso.");
          this.$router.push("/clientecobranca"); //REDIRECIONA
        }
        else{
          console.log("Erro ao atualizar cliente/cobranca.");
          alert("Erro ao atualizar cliente/cobranca: " + responseData.retorno["erro"]);
        }
      } catch (error) {
        console.error("Erro ao atualizar cliente/cobranca.", error);
        alert("Erro ao atualizar cliente/cobranca.");
      }
    },    
    formatarValorParcela() {
      // Atualiza o valor numérico real sempre que o usuário digitar
      this.valor_parcela = parseFloat(
        this.formattedValorParcela.replace(',', '.')
      );
    },
  },
}

</script>

<style scoped>

</style>
