# Sistema de Gerenciamento de Empresas

Esta solução compõe uma plataforma completa para a gestão de organizações, colaboradores e clientes, estruturada em arquitetura de microsserviços com o uso de **Laravel 10** para a camada de API e **Angular 17** no desenvolvimento do ecossistema de interface do usuário. A infraestrutura é totalmente conteinerizada via **Docker**, garantindo paridade entre ambientes de desenvolvimento e produção.

---

## Instruções de Inicialização

### Requisitos Técnicos
*   **Docker** e **Docker Compose** devidamente instalados e configurados.

### Procedimento para Implantação Local

1.  **Clonagem do Repositório:**
    ```bash
    git clone <url-do-repositorio>
    cd apiempresa
    ```

2.  **Orquestração de Containers:**
    Execute o comando abaixo para realizar o build e iniciar os serviços em modo desacoplado (background):
    ```bash
    docker-compose up -d --build
    ```

3.  **Configuração da Aplicação Backend:**
    Realize a instalação das dependências, geração de chaves e execução das migrações do banco de dados:
    ```bash
    # Instalação de dependências PHP via Composer
    docker-compose exec app composer install

    # Geração de chave de segurança da aplicação
    docker-compose exec app php artisan key:generate

    # Execução das migrações de esquema do banco de dados
    docker-compose exec app php artisan migrate
    ```

4.  **Pontos de Acesso:**
    *   **Interface Principal (Frontend):** [http://localhost:4200](http://localhost:4200)
    *   **Ponto de Extremidade da API:** [http://localhost:8000](http://localhost:8000)
    *   **Documentação Técnica (Swagger):** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

---

## Especificação Técnica da API

Exceto pelas rotas de Registro e Autenticação, todos os demais endpoints requerem autenticação via cabeçalho `Authorization` utilizando o esquema **Bearer Token** (Laravel Sanctum).

### 1. Autenticação e Controle de Acesso

#### [POST] `/api/register`
Realiza o registro de um novo administrador no sistema.
*   **Payload:** `name`, `email`, `password`, `password_confirmation`.

#### [POST] `/api/login`
Autentica as credenciais e fornece o token de acesso.
*   **Payload:** `email`, `password`.

---

### 2. Gestão de Empresas

#### [GET] `/api/empresas`
Recupera a lista de empresas registradas (suporta paginação).

#### [POST] `/api/empresas`
Efetua o cadastro de uma nova entidade organizacional.
*   **Payload:** `nome`, `cnpj`, `endereco`.

#### [GET] `/api/empresas/{id}`
Exibe os dados detalhados de uma empresa específica, incluindo relacionamentos com funcionários e clientes.

#### [PUT] `/api/empresas/{id}`
Atualiza os registros de uma organização existente.
*   **Payload:** `nome`, `cnpj`, `endereco`.

#### [DELETE] `/api/empresas/{id}`
Remove permanentemente o registro de uma empresa do banco de dados.

---

### 3. Gestão de Funcionários

#### [GET] `/api/funcionarios`
Recupera a listagem de colaboradores cadastrados.

#### [POST] `/api/funcionarios`
Efetua o cadastro de um novo funcionário, incluindo o processamento de anexos documentais.
*   **Corpo da Requisição (multipart/form-data):**
    *   `login`: Identificador único de acesso.
    *   `nome`: Nome completo do colaborador.
    *   `cpf`: Cadastro de Pessoa Física (exclusivo).
    *   `email`: Endereço de correio eletrônico corporativo.
    *   `endereco`: Logradouro de residência.
    *   `senha`: Credencial com mínimo de 6 caracteres.
    *   `documento`: Anexo obrigatório (PDF, JPG, JPEG) até 2MB.
    *   `empresa_ids[]`: Lista opcional de IDs organizacionais para vinculação.

#### [POST] `/api/funcionarios/{id}`
Atualiza os atributos de um colaborador. Para operações envolvendo arquivos, deve-se utilizar o método POST com o campo `_method` definido como `PUT`.

#### [DELETE] `/api/funcionarios/{id}`
Remove o registro do colaborador e elimina o anexo documental do sistema de arquivos.

---

### 4. Gestão de Clientes

#### [GET] `/api/clientes`
Recupera a listagem detalhada de clientes ativos.

#### [POST] `/api/clientes`
Realiza o onboarding de um novo cliente, incluindo persistência de documentos comprobatórios.
*   **Atributos:** `login`, `nome`, `cpf`, `email`, `endereco`, `senha`, `documento`.

---

## Execução de Testes

A aplicação utiliza o **PHPUnit** para a automação de testes. Para garantir a integridade dos fluxos de autenticação e das operações de CRUD, os testes podem ser executados dentro do container de aplicação:

```bash
# Execução de todos os testes da suite
docker-compose exec app php artisan test
```

---

## Pilha Tecnológica

*   **Camada de Dados:** MySQL 8.0.
*   **Serviços Backend:** Laravel 10 (PHP 8.2).
*   **Interface Frontend:** Angular 17 (Arquitetura baseada em Signals e Reactive Forms).
*   **Provisionamento:** Docker & Nginx.
*   **Documentação:** Especificação OpenAPI via L5-Swagger. (http://localhost:8000/api/documentation)
