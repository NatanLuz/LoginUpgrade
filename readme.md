# LoginUpgrade

Sistema basico de autenticacao desenvolvido em PHP, com interface responsiva em HTML/CSS e foco em fundamentos de seguranca para estudo e evolucao.

## Objetivo

Este projeto demonstra um fluxo simples de login com validacao no servidor.

## Tecnologias

- PHP
- HTML5
- CSS3
- Sessões nativas do PHP

## Estrutura do Projeto

- `index.php`: entrada principal da aplicacao e logica de autenticacao
- `style.css`: estilos visuais da interface de login
- `Login.PNG`: captura de tela da tela de login
- `Loginrealizado.PNG`: captura de tela apos autenticacao

## Screenshots

### Efetuando o login

![Login](Login.PNG)

### Login efetuado

![Loginrealizado](Loginrealizado.PNG)

## Funcionalidades

- Formulario de login em portugues
- Validacao de credenciais no servidor
- Sessao de usuario autenticado
- Logout seguro
- Feedback visual para sucesso e falha de autenticacao

## Controles de Seguranca Implementados

- Token CSRF em formularios de login e logout
- Regeneracao de ID de sessao apos login (`session_regenerate_id`)
- Cookie de sessao com `HttpOnly` e `SameSite=Strict`
- Validacao de formato de email no back-end
- Verificacao de senha com `password_verify`
- Limitacao de tentativas com bloqueio temporario
- Escape de saida com `htmlspecialchars`

## Credenciais de Teste

- Email: `admin@exemplo.com`
- Senha: `123456`

## Como Executar Localmente

1. Abra um terminal na pasta do projeto.
1. Inicie o servidor embutido do PHP em uma porta livre.

Exemplo:

```bash
php -S localhost:8001
```

1. Acesse no navegador:

```text
http://localhost:8001
```

## Limites do Projeto Atual

- Credenciais ainda estao em memoria (nao ha banco de dados)
- Nao ha cadastro de usuarios
- Nao ha recuperacao de senha
- Nao ha trilha de auditoria de acessos

## Proximos Passos Recomendados

1. Persistir usuarios em banco de dados (MySQL/PostgreSQL).
1. Armazenar apenas hashes de senha gerados por `password_hash`.
1. Adicionar politicas de senha forte e recuperacao por email.
1. Implementar logs de seguranca e monitoramento de tentativas.
1. Adicionar testes automatizados para autenticacao.



