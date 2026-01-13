import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, map, tap } from 'rxjs';
import { AuthResponse } from '../models/models';

/**
 * Serviço responsável pela autenticação e gestão de sessão do usuário.
 */
@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api';
  private currentUserSubject = new BehaviorSubject<any>(null);

  constructor(private http: HttpClient) {
    // Carrega o usuário do armazenamento local ao iniciar o serviço
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      this.currentUserSubject.next(JSON.parse(storedUser));
    }
  }

  login(credentials: any): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials).pipe(
      tap(response => {
        this.setSession(response);
      })
    );
  }

  register(userData: any): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/register`, userData).pipe(
      tap(response => {
        this.setSession(response);
      })
    );
  }

  private setSession(response: AuthResponse) {
    localStorage.setItem('token', response.access_token);
    localStorage.setItem('user', JSON.stringify(response.user));
    this.currentUserSubject.next(response.user);
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.currentUserSubject.next(null);
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  get currentUser() {
    return this.currentUserSubject.asObservable();
  }
}
