import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Funcionario } from '../models/models';

/**
 * Serviço para gerenciar operações de Funcionários via API.
 */
@Injectable({
  providedIn: 'root'
})
export class FuncionarioService {
  private apiUrl = 'http://localhost:8000/api/funcionarios';

  constructor(private http: HttpClient) { }

  getAll(): Observable<any> {
    return this.http.get<any>(this.apiUrl);
  }

  getById(id: number): Observable<Funcionario> {
    return this.http.get<Funcionario>(`${this.apiUrl}/${id}`);
  }

  create(data: FormData): Observable<any> {
    return this.http.post<any>(this.apiUrl, data);
  }

  update(id: number, data: FormData): Observable<any> {
    // O Laravel exige _method=PUT para processar multipart/form-data em atualizações
    data.append('_method', 'PUT');
    return this.http.post<any>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }
}
