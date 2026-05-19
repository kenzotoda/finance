# Controle Financeiro Pessoal (Laravel)

Sistema web completo para controle financeiro pessoal com Laravel, seguindo arquitetura MVC, autenticação Breeze, isolamento por usuário e interface Blade + Tailwind.

## Funcionalidades

- Autenticação completa (`login`, `cadastro`, `logout`) com Laravel Breeze.
- Dashboard com resumo do mês (receitas, despesas e saldo) + gráfico anual de entradas/saídas.
- CRUD de receitas.
- CRUD de despesas.
- CRUD de despesas fixas recorrentes mensais.
- CRUD de categorias.
- Filtros por mês, ano e categoria em listagens e relatórios.
- Relatórios mensal/anual com totais e saldo.
- Validações com Form Requests.
- Paginação nas listagens.
- Alertas de sucesso/erro.
- Proteção de rotas com middleware `auth`.

## Regras de negócio implementadas

- Cada usuário acessa e manipula somente os próprios dados.
- Saldo calculado como: `receitas - despesas`.
- Despesas fixas são replicadas automaticamente para o mês atual quando o usuário acessa dashboard/listagem de despesas.

## Requisitos

- PHP 8.3+
- Composer 2+
- Node.js 18+ (recomendado)

## Instalação e execução

1. Instalar dependências PHP:

```bash
composer install
```

2. Instalar dependências front-end:

```bash
npm install
```

3. Configurar ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

4. Executar migrations + seeders:

```bash
php artisan migrate --seed
```

5. Rodar front-end:

```bash
npm run dev
```

6. Subir servidor Laravel:

```bash
php artisan serve
```

## Usuários de exemplo (seed)

- `demo@financeiro.com` / `password`
- `test@example.com` / `password`

## Estrutura principal

- `app/Http/Controllers`: controllers por módulo (`Dashboard`, `Receitas`, `Despesas`, `DespesasFixas`, `Categorias`, `Relatorios`)
- `app/Http/Requests`: validações dos formulários
- `app/Models`: models e relacionamentos
- `app/Services/ReplicarDespesasFixasService.php`: lógica de recorrência mensal
- `resources/views`: telas Blade por módulo
- `routes/web.php`: rotas protegidas por `auth`

## Sugestões de melhorias futuras

- Integração com APIs bancárias (Open Finance).
- Exportação para Excel/PDF.
- Gráficos mais avançados e comparativos por período.
- Metas financeiras e acompanhamento de orçamento por categoria.
- Notificações de vencimento (email/whatsapp).
