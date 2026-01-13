import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { EmpresaListComponent } from './components/empresa-list/empresa-list.component';
import { EmpresaFormComponent } from './components/empresa-form/empresa-form.component';
import { FuncionarioListComponent } from './components/funcionario-list/funcionario-list.component';
import { FuncionarioFormComponent } from './components/funcionario-form/funcionario-form.component';
import { ClienteListComponent } from './components/cliente-list/cliente-list.component';
import { ClienteFormComponent } from './components/cliente-form/cliente-form.component';
import { AuthGuard } from './guards/auth.guard';

/**
 * Definição das rotas da aplicação frontend.
 */
export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { 
    path: '', 
    component: DashboardComponent, 
    canActivate: [AuthGuard],
    children: [
      { path: 'empresas', component: EmpresaListComponent },
      { path: 'empresas/novo', component: EmpresaFormComponent },
      { path: 'empresas/editar/:id', component: EmpresaFormComponent },
      
      { path: 'funcionarios', component: FuncionarioListComponent },
      { path: 'funcionarios/novo', component: FuncionarioFormComponent },
      { path: 'funcionarios/editar/:id', component: FuncionarioFormComponent },
      
      { path: 'clientes', component: ClienteListComponent },
      { path: 'clientes/novo', component: ClienteFormComponent },
      { path: 'clientes/editar/:id', component: ClienteFormComponent },
      
      { path: '', redirectTo: 'empresas', pathMatch: 'full' }
    ]
  },
  { path: '**', redirectTo: 'login' }
];
