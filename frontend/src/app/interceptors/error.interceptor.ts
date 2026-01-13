import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { catchError } from 'rxjs/operators';
import { throwError } from 'rxjs';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      let errorMessage = 'Ocorreu um erro inesperado.';

      if (error.error instanceof ErrorEvent) {
        // Erro do lado do cliente
        errorMessage = `Erro: ${error.error.message}`;
      } else {
        // Erro do lado do servidor
        switch (error.status) {
          case 401:
            errorMessage = 'Sessão expirada. Por favor, faça login novamente.';
            localStorage.removeItem('token');
            // Aqui você poderia redirecionar para o login
            break;
          case 404:
            errorMessage = 'O recurso solicitado não foi encontrado.';
            break;
          case 422:
            // Erros de validação do Laravel
            if (error.error && error.error.errors) {
              const validationErrors = error.error.errors;
              const firstKey = Object.keys(validationErrors)[0];
              errorMessage = validationErrors[firstKey][0];
            } else if (error.error && error.error.message) {
              errorMessage = error.error.message;
            } else {
              errorMessage = 'Dados inválidos.';
            }
            break;
          case 500:
            errorMessage = 'Erro interno do servidor. Tente novamente mais tarde.';
            break;
          default:
            errorMessage = error.error?.message || `Erro ${error.status}: ${error.statusText}`;
        }
      }

      // Exibir alerta (poderia usar um ToastService)
      alert(errorMessage);
      
      return throwError(() => new Error(errorMessage));
    })
  );
};
