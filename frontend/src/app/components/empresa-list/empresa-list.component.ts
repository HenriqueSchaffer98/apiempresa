import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { EmpresaService } from '../../services/empresa.service';
import { Empresa } from '../../models/models';

@Component({
  selector: 'app-empresa-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './empresa-list.component.html',
  styleUrl: './empresa-list.component.css'
})
export class EmpresaListComponent implements OnInit {
  empresas = signal<Empresa[]>([]);
  loading = signal(true);

  constructor(private empresaService: EmpresaService) {}

  ngOnInit() {
    this.loadEmpresas();
  }

  loadEmpresas() {
    this.loading.set(true);
    this.empresaService.getAll().subscribe({
      next: (response) => {
        this.empresas.set(response.data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  deleteEmpresa(id: number) {
    if (confirm('Tem certeza que deseja remover esta empresa?')) {
      this.empresaService.delete(id).subscribe(() => {
        this.loadEmpresas();
      });
    }
  }
}
