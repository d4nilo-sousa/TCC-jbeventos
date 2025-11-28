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

---

## 🛠️ Tecnologias Utilizadas

<p align="center">
  <img src="https://cdn.simpleicons.org/laravel/FF2D20" alt="Laravel" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/php/777BB4" alt="PHP" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/tailwindcss/06B6D4" alt="Tailwind CSS" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/livewire/4E56A6" alt="Livewire" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/alpine-dot-js/8BC0D0" alt="Alpine.js" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/mysql/4479A1" alt="MySQL" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/git/F05032" alt="Git" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/css3/1572B6" alt="CSS3" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/javascript/F7DF1E" alt="JavaScript" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/nodedotjs/339933" alt="Node.js" height="45"/>
  &nbsp;
  <img src="https://cdn.simpleicons.org/vite/646CFF" alt="Vite" height="45"/>
</p>

---

## Requisitos do Sistema

- **PHP:** Versão 8.1 ou superior
- **Composer:** Versão 2 ou superior
- **Node.js:** Versão 16 ou superior
- **Banco de Dados:** MySQL 8.0 ou superior

---

## Instruções de Instalação

1. Clone o repositório:
   ```bash
   git clone [https://github.com/d4nilo-sousa/TCC-jbeventos.git](https://github.com/d4nilo-sousa/TCC-jbeventos.git)
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

---

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

---

## 🤝 Desenvolvedores

Este projeto foi desenvolvido como Trabalho de Conclusão de Curso (TCC) pelos seguintes alunos da Etec João Belarmino:

* **Danilo Sousa** - ([@d4nilo-sousa](https://github.com/d4nilo-sousa))
* **Felipe Silva** - ([@Felipe-Silva07](https://github.com/Felipe-Silva07))
* **Enzo Assis** - ([@eassis10](https://github.com/eassis10))
* **Leonardo Bodini** - ([@LeonardoBPNS](https://github.com/LeonardoBPNS))

---

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues e enviar pull requests no repositório oficial.

---

## Licença

Este projeto está licenciado sob a [Licença MIT](https://opensource.org/licenses/MIT).
