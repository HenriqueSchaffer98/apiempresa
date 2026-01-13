import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Cliente } from '../models/models';

/**
 * Serviço para gerenciar operações de Clientes via API.
 */
@Injectable({
  providedIn: 'root'
})
export class ClienteService {
  private apiUrl = 'http://localhost:8000/api/clientes';

  constructor(private http: HttpClient) { }

  getAll(): Observable<any> {
    return this.http.get<any>(this.apiUrl);
  }

  getById(id: number): Observable<Cliente> {
    return this.http.get<Cliente>(`${this.apiUrl}/${id}`);
  }

  create(data: FormData): Observable<any> {
    return this.http.post<any>(this.apiUrl, data);
  }

  update(id: number, data: FormData): Observable<any> {
    data.append('_method', 'PUT');
    return this.http.post<any>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }
}
