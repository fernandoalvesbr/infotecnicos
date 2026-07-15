# Controle de Documentos de Técnicos

Sistema web simples para gerenciamento de técnicos, documentos ocupacionais e informações de contato, desenvolvido em PHP e utilizando arquivos JSON como armazenamento de dados.

O projeto foi criado para funcionar em hospedagens simples, sem necessidade de MySQL, MariaDB ou SQLite.

---

## Funcionalidades

* Sistema de login com usuário e senha
* Usuário administrador principal
* Cadastro de múltiplos usuários
* Alteração de senha
* Cadastro de técnicos
* Edição de técnicos
* Exclusão de técnicos
* Foto de perfil
* Cadastro de RG
* Cadastro de CPF
* Cadastro de e-mail pessoal
* Telefone empresarial
* Telefone de contato
* Contato de emergência
* Telefone de emergência
* Upload de documento ASO
* Upload de documento NR
* Suporte a arquivos PDF
* Conversão automática de imagens para WebP
* Visualização ampliada da foto do técnico
* Visualização rápida dos dados completos
* Botão para copiar os dados do técnico
* Importação de planilhas CSV
* Exportação para arquivo compatível com Microsoft Excel
* Tema claro e escuro
* Armazenamento em arquivos JSON
* Exclusão automática dos arquivos vinculados ao excluir um técnico

---

## Tecnologias utilizadas

* PHP
* HTML5
* CSS3
* JavaScript
* JSON
* Font Awesome
* PHP Sessions
* PHP GD para conversão de imagens em WebP

---

## Banco de dados

Este projeto não utiliza banco de dados SQL.

Os dados são armazenados nos seguintes arquivos:

```text
data.json
users.json
```

Os documentos e fotos são armazenados na pasta:

```text
uploads/
```

### Estrutura básica

```text
/
├── index.php
├── data.json
├── users.json
└── uploads/
    └── index.php
```

Os arquivos e diretórios necessários são criados automaticamente pelo sistema quando possível.

---

## Requisitos

Para executar o sistema, o servidor precisa possuir:

* Servidor web Apache, Nginx ou equivalente
* PHP com suporte a sessões
* Permissão de escrita no diretório do projeto
* Extensão GD recomendada para conversão de imagens para WebP
* Suporte às funções `password_hash()` e `password_verify()`

O projeto foi desenvolvido para funcionar em hospedagens simples com PHP.

---

## Instalação

1. Faça o download ou clone este repositório.

```bash
git clone URL_DO_REPOSITORIO
```

2. Envie os arquivos para o servidor.

3. Certifique-se de que o PHP possui permissão para criar e modificar:

```text
data.json
users.json
uploads/
```

4. Acesse o sistema pelo navegador.

Exemplo:

```text
https://seudominio.com.br/
```

Na primeira execução, o sistema tentará criar automaticamente os arquivos necessários.

---

## Acesso padrão

No primeiro acesso, caso o arquivo `users.json` ainda não exista, o sistema cria automaticamente o usuário administrador padrão.

```text
Usuário: admin
Senha: admin
```

> IMPORTANTE: altere a senha padrão imediatamente após o primeiro acesso.

---

## Administrador principal

O usuário com o nome exato:

```text
admin
```

é considerado o administrador principal do sistema.

Ele possui acesso ao gerenciamento de usuários.

O administrador pode:

* Criar usuários
* Alterar senhas de usuários
* Excluir usuários comuns
* Cadastrar técnicos
* Editar técnicos
* Excluir técnicos
* Importar dados
* Exportar dados
* Visualizar documentos
* Alterar a própria senha

O usuário `admin` é protegido contra exclusão através da interface normal do sistema.

---

## Usuários comuns

Usuários comuns podem:

* Acessar o sistema
* Alterar a própria senha
* Cadastrar técnicos
* Editar técnicos
* Excluir técnicos
* Visualizar dados
* Visualizar documentos
* Importar CSV
* Exportar dados

O gerenciamento de usuários fica disponível apenas para o usuário `admin`.

---

## Cadastro de técnicos

Cada técnico pode possuir as seguintes informações:

* Foto de perfil
* Nome completo
* RG
* CPF
* E-mail pessoal
* Telefone empresarial
* Telefone de contato
* Nome do contato de emergência
* Telefone de emergência
* Documento ASO
* Documento NR

Cada técnico recebe automaticamente um identificador único interno.

---

## Foto de perfil

O sistema permite enviar uma foto para cada técnico.

As imagens são convertidas, sempre que possível, para:

```text
WebP
```

Exemplo de nome do arquivo:

```text
perfil_ID_DO_TECNICO.webp
```

A foto pode ser visualizada em tamanho ampliado clicando diretamente sobre a imagem do técnico.

---

## Documentos ASO e NR

O sistema permite armazenar:

* ASO
* Documento NR

Os formatos suportados são:

* PDF
* Imagens

Arquivos PDF são mantidos no formato original.

Imagens são convertidas, sempre que possível, para WebP.

Exemplos:

```text
aso_ID_DO_TECNICO.pdf
nr_ID_DO_TECNICO.webp
```

---

## Visualização de dados

Ao clicar sobre o nome do técnico, o sistema apresenta uma janela com os dados completos.

São exibidos:

* Nome
* RG
* CPF
* E-mail
* Telefone empresarial
* Telefone de contato
* Contato de emergência
* Telefone de emergência

Também existe um botão para copiar todos os dados para a área de transferência.

---

## Importação de CSV

O sistema permite importar técnicos através de arquivos:

```text
.csv
```

O importador identifica automaticamente arquivos separados por:

```text
;
```

ou:

```text
,
```

A primeira linha é considerada como cabeçalho e é ignorada.

### Estrutura utilizada pelo importador

O importador atual utiliza posições específicas da planilha:

| Coluna | Informação            |
| ------ | --------------------- |
| 1      | Nome                  |
| 2      | RG                    |
| 3      | CPF                   |
| 5      | Telefone empresarial  |
| 8      | Telefone de contato   |
| 10     | Contato de emergência |

As colunas relacionadas a CNH e IMEI podem ser ignoradas.

O e-mail pessoal não é preenchido automaticamente pela importação atual.

---

## Atenção ao importar planilhas

O sistema não verifica automaticamente registros duplicados.

Portanto, importar o mesmo arquivo mais de uma vez pode gerar cadastros duplicados.

Antes de realizar uma importação em massa, é recomendado fazer backup do arquivo:

```text
data.json
```

---

## Exportação para Excel

O sistema possui um botão:

```text
Exportar Excel
```

A exportação gera um arquivo CSV compatível com Microsoft Excel.

O arquivo utiliza:

* Codificação UTF-8
* BOM UTF-8
* Separador por ponto e vírgula

Exemplo de nome:

```text
tecnicos_2026-07-15_14-30-25.csv
```

### Informações exportadas

* Nome completo
* RG
* CPF
* E-mail pessoal
* Telefone empresarial
* Telefone de contato
* Contato de emergência
* Telefone de emergência
* Documento ASO
* Documento NR

---

## Tema claro e escuro

O sistema possui suporte a:

* Modo claro
* Modo escuro

A preferência é armazenada no navegador através de:

```text
localStorage
```

O tema escolhido permanece salvo no mesmo navegador.

---

## Alteração de senha

Todos os usuários podem alterar a própria senha.

Para isso:

1. Clique em `Mudar Senha`
2. Informe a senha atual
3. Informe a nova senha
4. Confirme a alteração

As senhas são armazenadas utilizando:

```php
password_hash()
```

A validação durante o login utiliza:

```php
password_verify()
```

As senhas não são armazenadas diretamente em texto puro.

---

## Arquivos de dados

### data.json

Armazena os cadastros dos técnicos.

Exemplo de informações armazenadas:

```json
{
    "id": "identificador",
    "nome": "Nome do Técnico",
    "rg": "0000000",
    "cpf": "00000000000",
    "email_pessoal": "email@exemplo.com",
    "tel_empresarial": "000000000",
    "tel_contato": "000000000",
    "contato_emergencia": "Nome",
    "tel_emergencia": "000000000",
    "arquivo_aso": "aso_id.pdf",
    "arquivo_nr": "nr_id.pdf",
    "foto_perfil": "perfil_id.webp"
}
```

---

### users.json

Armazena os usuários autorizados a acessar o sistema.

As senhas ficam armazenadas utilizando hash.

---

### uploads/

Armazena:

* Fotos de perfil
* Documentos ASO
* Documentos NR

---

## Backup

Para realizar um backup completo, copie:

```text
index.php
data.json
users.json
uploads/
```

Os arquivos mais importantes são:

```text
data.json
users.json
uploads/
```

É recomendado realizar backups periódicos.

---

## Restauração

Para restaurar os dados:

1. Restaure o arquivo `data.json`
2. Restaure o arquivo `users.json`
3. Restaure a pasta `uploads`
4. Mantenha os nomes originais dos arquivos

Os nomes registrados dentro do `data.json` precisam corresponder aos arquivos existentes na pasta `uploads`.

---

## Segurança

Este sistema trabalha com informações pessoais e documentos.

Antes de utilizar em produção, recomenda-se:

* Alterar imediatamente a senha padrão
* Utilizar HTTPS
* Proteger `data.json`
* Proteger `users.json`
* Impedir a execução de scripts na pasta `uploads`
* Utilizar senhas fortes
* Manter backups atualizados
* Atualizar o PHP sempre que possível
* Restringir o acesso apenas a pessoas autorizadas

---

## Dados sensíveis

O sistema pode armazenar informações como:

* CPF
* RG
* E-mail
* Telefones
* Contatos de emergência
* Documentos ocupacionais

Por esse motivo, o servidor deve ser protegido adequadamente.

Os arquivos JSON não devem ficar acessíveis diretamente através do navegador.

---

## Limitações atuais

A versão atual não possui:

* Recuperação automática de senha
* Histórico de alterações
* Log de auditoria
* Lixeira para técnicos excluídos
* Validação matemática de CPF
* Verificação automática de duplicidade
* Controle de validade do ASO
* Controle individual para diferentes NRs
* Diferentes níveis detalhados de permissão
* Proteção CSRF
* Sistema de banco de dados SQL

---

## Observação

Este projeto utiliza arquivos JSON como armazenamento para facilitar a instalação em servidores e hospedagens que não possuem banco de dados disponível.

Para ambientes com grande quantidade de usuários, documentos ou acessos simultâneos, é recomendado migrar futuramente o armazenamento para um banco de dados como:

* MySQL
* MariaDB
* PostgreSQL
* SQLite

---

## Licença

Projeto disponibilizado gratuitamente.

Caso utilize ou modifique este sistema, os créditos são sempre bem-vindos.
---

## Autor

Desenvolvido para gerenciamento e organização da documentação da equipe técnica.

---

## Aviso

Este sistema deve ser utilizado respeitando as políticas internas da empresa e a legislação aplicável ao tratamento e armazenamento de dados pessoais.

