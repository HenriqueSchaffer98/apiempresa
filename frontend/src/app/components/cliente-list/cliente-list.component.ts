import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ClienteService } from '../../services/cliente.service';
import { Cliente } from '../../models/models';

@Component({
  selector: 'app-cliente-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './cliente-list.component.html',
  styleUrl: './cliente-list.component.css'
})
export class ClienteListComponent implements OnInit {
  clientes: Cliente[] = [];
  loading = true;

  constructor(private clienteService: ClienteService) {}

  ngOnInit() {
    this.loadClientes();
  }

  loadClientes() {
    this.loading = true;
    this.clienteService.getAll().subscribe({
      next: (response) => {
        this.clientes = response.data;
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  deleteCliente(id: number) {
    if (confirm('Tem certeza que deseja remover este cliente?')) {
      this.clienteService.delete(id).subscribe(() => {
        this.loadClientes();
      });
    }
  }
}
