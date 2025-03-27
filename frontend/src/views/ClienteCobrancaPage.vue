<template>
  <div class="container mt-5">
    <h2 class="text-center">Lista de Clientes/Cobranças</h2>
    
    <!-- CAMPO DE FILTRO -->
    <div class="mb-3">
      <input 
        v-model="searchQuery" 
        type="text" 
        class="form-control" 
        placeholder="Filtro de pesquisar por qualquer campo"
      />
    </div>

    <!-- TABELA -->
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>CPF</th>
          <th>Endereço</th>
          <th>Bairro</th>
          <th>Cidade</th>
          <th>Estado</th>
          <th>Telefone</th>
          <th>Email</th>
          <th>Editar</th>
          <th>Excluir</th>
        </tr>
      </thead>
      <tbody>
        <!-- DADOS FILTRADOS -->
        <tr v-for="item in filteredCobranças" :key="item.id">
          <td>{{ item.id }}</td>
          <td>{{ item.nome }}</td>
          <td>{{ item.cpf }}</td>
          <td>{{ item.endereco }}</td>
          <td>{{ item.bairro }}</td>
          <td>{{ item.cidade }}</td>
          <td>{{ item.estado }}</td>
          <td>{{ item.telefone }}</td>
          <td>{{ item.email }}</td>
          <!--BTN EDITAR + EXCLUIR-->
          <td>
            <button @click="editItem(item.id)" class="btn btn-warning btn-sm">Editar</button>
          </td>
          <td> 
            <button @click="deleteItem(item.id)" class="btn btn-danger btn-sm">Excluir</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ClientecobrancasPage",
  data() {
    return {
      cobranças: [],
      searchQuery: ""  // FILTRRO
    };
  },
  computed: {
    // FUNCAO FILTRAR
    filteredCobranças() {
      return this.cobranças.filter(item => {
        return Object.values(item).some(val =>
          String(val).toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });
    }
  },
  mounted() {
    this.getCobranças();
  },
  methods: {
    // FUNÇÃO BUSCA DADOS NA API
    async getCobranças() {
      try {
        const response = await axios.get(
          "http://localhost/preambulo_tech/backend/public/index.php/api/cobrancas"
        );
        this.cobranças = response.data.retorno[0];
      } catch (error) {
        console.error("Erro ao carregar cliente/cobranca", error);
        alert("Erro ao carregar cliente/cobrancas");
      }
    },

    // FUNCAO EDITAR
    editItem(id) {
      this.$router.push({ name: 'EditPage', params: { id } }); // REDIRECIONAR PAGINA
    },

    // FUNCAO EXCLUIR
    async deleteItem(id) {
      try {
        const response = await axios.delete(
          `http://localhost/preambulo_tech/backend/public/index.php/api/cobrancas/${id}`
        );
        if (response.status === 200) {
          console.log("Sucesso: Cliente/Cobrança excluido com sucesso.");
          alert("Sucesso: Cliente/Cobrança excluido com sucesso.");
          this.getCobranças(); // RECARREGA OS DADOS
        }
      } catch (error) {
        console.error("Erro ao excluir cobrança:", error);
        alert("Erro ao excluir cobrança");
      }
    }
  }
};
</script>

<style scoped>
.table th, .table td {
  text-align: center;
}
</style>
