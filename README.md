# JB Eventos

<p align="center">
  <img src="jbeventos/public/imgs/logoJb-com-fundo.jpeg" alt="Logo JB Eventos" width="300">
</p>


JB Eventos é uma rede social com foco em centralizar e facilitar o acesso a informações sobre os eventos escolares da Etec João Belarmino. A plataforma permite que administradores, coordenadores e usuários interajam em um ambiente intuitivo e funcional, promovendo maior engajamento e organização.

## Funcionalidades Principais

- **Gerenciamento de Eventos:** Criação, edição e exclusão de eventos.
- **Interação Social:** Comentários, curtidas e compartilhamento de eventos.
- **Notificações em Tempo Real:** Atualizações instantâneas sobre eventos e interações.
- **Relatórios Gerenciais:** Geração de relatórios em PDF para análise de desempenho.
- **Sistema de Usuários:** Perfis personalizados para administradores, coordenadores e usuários comuns.

## 🛠️ Tecnologias Utilizadas

<table align="center">
  <tr>
    <th>Tecnologia</th>
    <th>Logo</th>
    <th>Função no Projeto</th>
  </tr>

  <tr>
    <td><strong>Laravel</strong></td>
    <td align="center">
      <img src="https://laravel.com/img/logomark.min.svg" width="55">
    </td>
    <td>Backend (estrutura principal do sistema)</td>
  </tr>

  <tr>
    <td><strong>PHP</strong></td>
    <td align="center">
      <img src="https://www.php.net/images/logos/new-php-logo.svg" width="55">
    </td>
    <td>Linguagem utilizada no backend</td>
  </tr>

  <tr>
    <td><strong>Tailwind CSS</strong></td>
    <td align="center">
      <img src="https://raw.githubusercontent.com/tailwindlabs/tailwindcss/master/.github/logo.svg" width="55">
    </td>
    <td>Estilização do front-end</td>
  </tr>

  <tr>
    <td><strong>Livewire</strong></td>
    <td align="center">
      <img src="https://raw.githubusercontent.com/livewire/livewire/master/art/brand/logo.svg" width="55">
    </td>
    <td>Interatividade no front sem uso excessivo de JavaScript</td>
  </tr>

  <tr>
    <td><strong>Vite</strong></td>
    <td align="center">
      <img src="https://raw.githubusercontent.com/vitejs/vite/main/docs/public/logo.svg" width="55">
    </td>
    <td>Build e processamento dos assets (JS/CSS)</td>
  </tr>


  <tr>
    <td><strong>MySQL</strong></td>
    <td align="center">
      <img src="https://www.vectorlogo.zone/logos/mysql/mysql-icon.svg" width="55">
    </td>
    <td>Banco de dados relacional do sistema</td>
  </tr>
</table>


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
