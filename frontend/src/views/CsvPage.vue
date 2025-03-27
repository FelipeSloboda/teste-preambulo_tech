<template>
  <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center">
      <h1>Importação do arquivo .CSV</h1><br>
      <form @submit.prevent="enviarCsv" class="w-55 mx-auto">
        <!-- UPLOAD CSV -->
        <div class="mb-3">
          <input type="file" class="form-control" id="fileInput" @change="anexarCsv" />
          <small id="fileHelp" class="form-text text-muted">
            O arquivo deve ser no formato .CSV <br>Tamanho máximo de 2MB.
          </small>
        </div>
        <!-- BTN ENVIAR -->
        <button type="submit" class="btn btn-success">Importar CSV</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CsvPage',
  data() {
    return {
      file: null,
    };
  },
  methods: {
    // FUNÇÃO ANEXAR ARQUIVO
    anexarCsv(event) {
      // ARMAZENA O CSV
      this.file = event.target.files[0]; 
    },

    // FUNÇÃO ENVIAR CSV
    async enviarCsv() {
      if (this.file) {
        // VERIFICA SE FORMATO É CSV
        if (!this.file.name.endsWith('.csv') && this.file.type !== 'text/csv') {
          console.error('Erro: Arquivo não esta no formato .CSV');
          alert('Erro: Arquivo não esta no formato .CSV');
          this.file = null;
        } else {
          console.log('Aviso: Arquivo selecionado.');
          // CRIA O FORM
          const formData = new FormData();
          formData.append('file', this.file);

          try {
            // ENVIA CSV VIA AXIOS PARA API
            const response = await axios.post('http://localhost/preambulo_tech/backend/public/index.php/api/csv', formData, {
              headers: {
                'Content-Type': 'multipart/form-data', // Tipo de conteúdo para upload de arquivo
              },
            });

            // VERIFICA A RESPOSTA DA API
            const responseData = response.data;         
            const httpCode = responseData.cabecalho["http code"];

            if (httpCode === 200) {
              console.log('Sucesso: Arquivo enviado com sucesso.');
              alert('Sucesso: Arquivo enviado com sucesso.');
            } else {
              console.erro('Erro ao enviar o arquivo.');
              alert('Erro ao enviar o arquivo.');
            }
          } catch (error) {
            console.error('Erro ao enviar o arquivo:', error);
            alert('Erro ao enviar o arquivo.');
          }
        }
      } else {
        console.error('Erro: Arquivo não selecionado.');
        alert('Erro: Arquivo não selecionado.');
      }
    }
  }
};
</script>

<style scoped>

</style>
