import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { FuncionarioService } from '../../services/funcionario.service';
import { EmpresaService } from '../../services/empresa.service';
import { Empresa } from '../../models/models';

@Component({
  selector: 'app-funcionario-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './funcionario-form.component.html',
  styleUrl: './funcionario-form.component.css'
})
/**
 * Componente para criar ou editar os dados de um funcionário.
 */
export class FuncionarioFormComponent implements OnInit {
  funcionarioForm: FormGroup;
  isEdit = false;
  funcionarioId?: number;
  loading = false;
  empresas: Empresa[] = [];
  selectedFile: File | null = null;

  constructor(
    private fb: FormBuilder,
    private funcionarioService: FuncionarioService,
    private empresaService: EmpresaService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.funcionarioForm = this.fb.group({
      nome: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9\s]+$/)]],
      login: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9]+$/)]],
      cpf: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      endereco: ['', [Validators.required]],
      senha: ['', [Validators.required]],
      empresa_ids: [[]]
    });
  }

  ngOnInit() {
    this.loadEmpresas();
    this.funcionarioId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.funcionarioId) {
      this.isEdit = true;
      this.funcionarioForm.get('senha')?.clearValidators();
      this.funcionarioForm.get('senha')?.updateValueAndValidity();
      this.loadFuncionario();
    }
  }

  loadEmpresas() {
    this.empresaService.getAll().subscribe(response => {
      this.empresas = response.data;
    });
  }

  loadFuncionario() {
    this.funcionarioService.getById(this.funcionarioId!).subscribe((f: any) => {
      this.funcionarioForm.patchValue(f.data);
      if (f.data.empresas) {
        this.funcionarioForm.patchValue({
          empresa_ids: f.data.empresas.map((e: any) => e.id)
        });
      }
    });
  }

  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
  }

  onSubmit() {
    if (this.funcionarioForm.invalid) return;

    this.loading = true;
    const formData = new FormData();
    Object.keys(this.funcionarioForm.value).forEach(key => {
      if (key === 'empresa_ids') {
        this.funcionarioForm.get(key)?.value.forEach((id: number) => {
          formData.append('empresa_ids[]', id.toString());
        });
      } else if (this.funcionarioForm.get(key)?.value) {
        formData.append(key, this.funcionarioForm.get(key)?.value);
      }
    });

    if (this.selectedFile) {
      formData.append('documento', this.selectedFile);
    }

    const request = this.isEdit 
      ? this.funcionarioService.update(this.funcionarioId!, formData)
      : this.funcionarioService.create(formData);

    request.subscribe({
      next: () => {
        this.router.navigate(['/funcionarios']);
      },
      error: () => this.loading = false
    });
  }
}
