# Mãos à Obra

Sistema PHP + MariaDB para gestão simples de obras, custos, ocorrências, histórico e relatórios.

## Login de teste

Administrador:
- E-mail: `admin@maoobra.com`
- Senha: `123456`

Engenheiro:
- E-mail: `engenheiro@maoobra.com`
- Senha: `123456`

## Banco de dados

Importe o arquivo:

```txt
database/banco_mao.sql
```

No XAMPP/phpMyAdmin:
1. Abra o phpMyAdmin.
2. Crie o banco `banco_mao` ou importe direto o SQL.
3. Vá em SQL/Importar e execute `database/banco_mao.sql`.

## Conexão

Arquivo:

```txt
configs/banco.php
```

No Codespaces/Linux foi usado:

```php
define('DB_USER', 'rootphp');
define('DB_PASS', '123456');
```

No XAMPP padrão geralmente fica:

```php
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Permissões

- Admin vê todos os registros.
- Engenheiro vê somente as obras atribuídas a ele.
- Custos e ocorrências aparecem para o engenheiro quando pertencem às obras dele, mesmo que o lançamento tenha sido feito por outro usuário.

## Módulos

- Dashboard
- Obras
- Materiais e Custos
- Ocorrências
- Histórico
- Relatórios com exportação CSV
