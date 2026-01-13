import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { EmpresaService } from '../../services/empresa.service';

@Component({
  selector: 'app-empresa-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './empresa-form.component.html',
  styleUrl: './empresa-form.component.css'
})
export class EmpresaFormComponent implements OnInit {
  empresaForm: FormGroup;
  isEdit = false;
  empresaId?: number;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private empresaService: EmpresaService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.empresaForm = this.fb.group({
      nome: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9\s]+$/)]],
      cnpj: ['', [Validators.required]],
      endereco: ['', [Validators.required]]
    });
  }

  ngOnInit() {
    this.empresaId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.empresaId) {
      this.isEdit = true;
      this.loadEmpresa();
    }
  }

  loadEmpresa() {
    this.empresaService.getById(this.empresaId!).subscribe((empresa: any) => {
      this.empresaForm.patchValue(empresa.data);
    });
  }

  onSubmit() {
    if (this.empresaForm.invalid) return;

    this.loading = true;
    const request = this.isEdit 
      ? this.empresaService.update(this.empresaId!, this.empresaForm.value)
      : this.empresaService.create(this.empresaForm.value);

    request.subscribe({
      next: () => {
        this.router.navigate(['/empresas']);
      },
      error: () => this.loading = false
    });
  }
}
