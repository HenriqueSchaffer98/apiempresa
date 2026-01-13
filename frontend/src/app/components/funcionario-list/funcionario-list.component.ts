import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FuncionarioService } from '../../services/funcionario.service';
import { Funcionario } from '../../models/models';

@Component({
  selector: 'app-funcionario-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './funcionario-list.component.html',
  styleUrl: './funcionario-list.component.css'
})
export class FuncionarioListComponent implements OnInit {
  funcionarios: Funcionario[] = [];
  loading = true;

  constructor(private funcionarioService: FuncionarioService) {}

  ngOnInit() {
    this.loadFuncionarios();
  }

  loadFuncionarios() {
    this.loading = true;
    this.funcionarioService.getAll().subscribe({
      next: (response) => {
        this.funcionarios = response.data;
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  deleteFuncionario(id: number) {
    if (confirm('Tem certeza que deseja remover este funcionário?')) {
      this.funcionarioService.delete(id).subscribe(() => {
        this.loadFuncionarios();
      });
    }
  }
}
