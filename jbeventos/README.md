<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# JB Eventos

<p align="center">
  <img src="public/imgs/logoJb-com-fundo.jpeg" alt="Logo JB Eventos" width="300">
</p>

<p align="center">
  <a href="https://github.com/d4nilo-sousa/TCC-jbeventos/actions">
    <img src="https://img.shields.io/github/actions/workflow/status/d4nilo-sousa/TCC-jbeventos/laravel.yml?style=flat-square" alt="Build Status">
  </a>
  <a href="https://github.com/d4nilo-sousa/TCC-jbeventos">
    <img src="https://img.shields.io/github/stars/d4nilo-sousa/TCC-jbeventos?style=flat-square" alt="Stars">
  </a>
  <a href="https://github.com/d4nilo-sousa/TCC-jbeventos/blob/develop/LICENSE">
    <img src="https://img.shields.io/github/license/d4nilo-sousa/TCC-jbeventos?style=flat-square" alt="License">
  </a>
</p>

JB Eventos é uma rede social com foco em centralizar e facilitar o acesso a informações sobre os eventos escolares da Etec João Belarmino. A plataforma permite que administradores, coordenadores e usuários interajam em um ambiente intuitivo e funcional, promovendo maior engajamento e organização.

## Funcionalidades Principais

- **Gerenciamento de Eventos:** Criação, edição e exclusão de eventos.
- **Interação Social:** Comentários, curtidas e compartilhamento de eventos.
- **Notificações em Tempo Real:** Atualizações instantâneas sobre eventos e interações.
- **Relatórios Gerenciais:** Geração de relatórios em PDF para análise de desempenho.
- **Sistema de Usuários:** Perfis personalizados para administradores, coordenadores e usuários comuns.

## Tecnologias Utilizadas

- **Backend:** Laravel Framework
- **Frontend:** Blade Templates, Tailwind CSS
- **Banco de Dados:** MySQL
- **Outras Ferramentas:** Vite, Livewire, FullCalendar

## Requisitos do Sistema

- **PHP:** Versão 8.1 ou superior
- **Composer:** Versão 2 ou superior
- **Node.js:** Versão 16 ou superior
- **Banco de Dados:** MySQL 8.0 ou superior

## Instruções de Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/d4nilo-sousa/TCC-jbeventos.git
   cd TCC-jbeventos/jbeventos
   ```

2. Instale as dependências do PHP:
   ```bash
   composer install
   ```

3. Instale as dependências do Node.js:
   ```bash
   npm install
   ```

4. Configure o arquivo `.env`:
   - Copie o arquivo de exemplo:
     ```bash
     cp .env.example .env
     ```
   - Atualize as variáveis de ambiente, como conexão com o banco de dados e chave da aplicação.

5. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```

6. Execute as migrações e seeders para configurar o banco de dados:
   ```bash
   php artisan migrate --seed
   ```

## Como Rodar o Projeto

1. Inicie o servidor de desenvolvimento do Laravel:
   ```bash
   php artisan serve
   ```

2. Compile os arquivos front-end:
   ```bash
   npm run dev
   ```

3. Acesse o sistema no navegador:
   ```
   http://localhost:8000
   ```

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues e enviar pull requests no repositório oficial.

## Licença

Este projeto está licenciado sob a [Licença MIT](https://opensource.org/licenses/MIT).
