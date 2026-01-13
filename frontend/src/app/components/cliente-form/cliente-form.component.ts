import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { ClienteService } from '../../services/cliente.service';

@Component({
  selector: 'app-cliente-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './cliente-form.component.html',
  styleUrl: './cliente-form.component.css'
})
export class ClienteFormComponent implements OnInit {
  clienteForm: FormGroup;
  isEdit = false;
  clienteId?: number;
  loading = false;
  selectedFile: File | null = null;

  constructor(
    private fb: FormBuilder,
    private clienteService: ClienteService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.clienteForm = this.fb.group({
      nome: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9\s]+$/)]],
      login: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9]+$/)]],
      cpf: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      endereco: ['', [Validators.required]],
      senha: ['', [Validators.required]]
    });
  }

  ngOnInit() {
    this.clienteId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.clienteId) {
      this.isEdit = true;
      this.clienteForm.get('senha')?.clearValidators();
      this.clienteForm.get('senha')?.updateValueAndValidity();
      this.loadCliente();
    }
  }

  loadCliente() {
    this.clienteService.getById(this.clienteId!).subscribe((c: any) => {
      this.clienteForm.patchValue(c.data);
    });
  }

  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
  }

  onSubmit() {
    if (this.clienteForm.invalid) return;

    this.loading = true;
    const formData = new FormData();
    Object.keys(this.clienteForm.value).forEach(key => {
      if (this.clienteForm.get(key)?.value) {
        formData.append(key, this.clienteForm.get(key)?.value);
      }
    });

    if (this.selectedFile) {
      formData.append('documento', this.selectedFile);
    }

    const request = this.isEdit 
      ? this.clienteService.update(this.clienteId!, formData)
      : this.clienteService.create(formData);

    request.subscribe({
      next: () => {
        this.router.navigate(['/clientes']);
      },
      error: () => this.loading = false
    });
  }
}
