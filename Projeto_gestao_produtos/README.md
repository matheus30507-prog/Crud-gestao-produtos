# Crud-gestao-produtos

## Integrantes
Aluno: Matheus Freitas dos Santos R.A: 60005346

## Tecnologias Utilizadas

- PHP
- MySQL
- PDO
- HTML
- CSS
- JavaScript
- AJAX
- Bootstrap
- Git e GitHub


## Esboço das telas do sistema:

-Tela de login:

<img width="465" height="432" alt="Tela de login (1)" src="https://github.com/user-attachments/assets/9f313d90-8fec-4805-80d4-d5e7946e262d" />

-Tela de cadastro:

<img width="465" height="432" alt="Tela de cadastro" src="https://github.com/user-attachments/assets/e70e575f-d2a8-44c8-bd11-043527f81dd9" />

-Tela de navegacao:

<img width="1440" height="828" alt="Tela de Navegacao" src="https://github.com/user-attachments/assets/20755d34-07d6-45a3-9614-0be682fd5a34" />

-Tela de Cadastro:

<img width="1440" height="1146" alt="cadastros-screen" src="https://github.com/user-attachments/assets/a89933b1-ffde-4cb7-96ab-63b34d6bc0bb" />

- Tela de Selecao de produtos:

<img width="1440" height="900" alt="selecao-produtos" src="https://github.com/user-attachments/assets/af5a042d-41ba-4404-8d35-5689f9fa4147" />

- Tela de atualização de dados:

<img width="1440" height="900" alt="atualizar-dados" src="https://github.com/user-attachments/assets/9ec13547-17d3-400f-ac56-f1c3e6acd1a7" />

-Tela de Cesta de Produtos:

<img width="1440" height="900" alt="minha-cesta" src="https://github.com/user-attachments/assets/55fcabeb-ca13-4a97-b751-f53b8ea199b7" />






----------------------------------------------------------------------------------------------------------------------------------------------

-Diagrama de Entidade Relacionamento:

<img width="1251" height="291" alt="diagramadb" src="https://github.com/user-attachments/assets/4c669ee8-ec98-4c18-9c87-ec8e322b7fef" />


## Implementação

O sistema foi implementado utilizando PHP com acesso ao banco de dados MySQL através do PDO.

### Autenticação

O sistema possui cadastro e autenticação de usuários.

As senhas são armazenadas utilizando o algoritmo SHA-256.

### Fornecedores

O sistema permite realizar o cadastro e gerenciamento dos fornecedores.

Cada fornecedor possui:

- ID
- Nome
- CNPJ
- Telefone

### Produtos

O sistema permite cadastrar e gerenciar produtos.

Cada produto possui:

- ID
- Nome
- Preço
- Fornecedor

Os produtos são relacionados aos fornecedores através da chave estrangeira `fornecedor_id`.

### Cesta

O usuário pode selecionar produtos para adicionar à sua cesta.

O sistema possui validação para:

- Selecionar no mínimo 2 produtos;
- Não adicionar o mesmo produto mais de uma vez;
- Exibir os produtos selecionados;
- Exibir a quantidade de produtos;
- Calcular o valor total da cesta;
- Remover produtos da cesta.


## Versionamento

O projeto foi desenvolvido utilizando Git e GitHub para controle de versão.

As alterações realizadas durante o desenvolvimento foram registradas por meio de commits.
