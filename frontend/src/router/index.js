import { createRouter, createWebHashHistory } from 'vue-router'
import HomePage from '../views/HomePage.vue'
import CsvPage from '../views/CsvPage.vue'
import ClienteCobrancaPage from '../views/ClienteCobrancaPage.vue'
import EditPage from '../views/EditPage.vue'
import SobrePage from '../views/SobrePage.vue'

const routes = [
  {
    path: '/home',
    name: 'home',
    component: HomePage
  },
  {
    path: '/csv',
    name: 'csv',
    component: CsvPage,
  },
  {
    path: '/clientecobranca',
    name: 'clientecobranca',
    component: ClienteCobrancaPage,
  },
  {
    path: '/editar/:id',
    name: 'EditPage',
    component: EditPage,
    props: true,  
  },
  {
    path: '/sobre',
    name: 'sobre',
    component: SobrePage,
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
