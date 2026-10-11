🚲 CicloManos — Sistema Web para Bicicletaria

Sistema web completo desenvolvido como Projeto de Trabalho de Conclusão de Curso (TCC) para a bicicletaria **CicloManos**. A plataforma integra um e-commerce interativo voltado para clientes (com vitrine de produtos, carrinho, lista de favoritos, simulação de compras e gestão de perfil) e um painel administrativo exclusivo para funcionários e gestores gerenciarem o inventário, vendas e serviços de manutenção.

---

🚀 Funcionalidades do Sistema

 🛒 Painel do Cliente (E-commerce)
* **Vitrine e Busca Avançada:** Visualização de produtos em destaque divididos por categorias (Bicicletas, Peças e Acessórios) e barra de busca dinâmica no banco de dados.
* **Carrinho de Compras Persistente:** Adição, remoção, cálculo automático de sub-totais e persistência de itens vinculada diretamente à conta do cliente no MySQL.
* **Lista de Favoritos:** Salvamento de produtos favoritos na conta do utilizador para acesso rápido.
* **Autenticação Segurada:** Sistema de registo com validação rigorosa de CPF, CEP, bairros cadastrados e encriptação de senhas (`password_hash`).

 ⚙️ Painel do Funcionário / Administrativo
* **Acesso Restrito:** Login corporativo diferenciado por e-mails profissionais e níveis de permissão.
* **Gestão de Produtos:** Cadastro, atualização e controlo de stock e preços de venda.
* **Controlo de Atendimento e Vendas:** Acompanhamento de pedidos e histórico de serviços de manutenção prestados pela bicicletaria.

---

 🛠️ Tecnologias Utilizadas

Este projeto foi desenvolvido utilizando tecnologias modernas para web full-stack:

* **Linguagem:** PHP (8.x) com suporte a PDO e MySQLi.
* **Banco de Dados:** MySQL / MariaDB.
* **Estilização e Layout:** HTML5, CSS3, JavaScript (Vanilla) e Bootstrap 5.3 para responsividade.
* **Ambiente de Execução Local:** XAMPP (Apache + MySQL).

---

 📂 Estrutura do Projeto

```text
ciclomanos/
│
├── Index.php              # Página principal / Vitrine de produtos
├── produtos.php           # Página de detalhes do produto
├── carrinho.php           # Gestão do carrinho de compras
├── favoritos.php          # Gestão de produtos favoritos
├── login.php              # Autenticação e Registo de Clientes
├── login_funcionario.php  # Autenticação restrita de Funcionários
├── painel_funcionario.php # Área administrativa e gestão interna
├── manutencao.php         # Informações sobre serviços da bicicletaria
├── ofertas.php            # Vitrine de produtos em promoção
├── config.php             # Configuração de conexão com a base de dados
├── style.css              # Folha de estilos principal do sistema
└── tcc/                   # Pasta de recursos visuais (logótipos, imagens)

```

---

⚙️ Como Instalar e Executar o Projeto

Siga os passos abaixo para configurar o ambiente localmente utilizando o **XAMPP**:

 1. Pré-requisitos

* Ter o [XAMPP](https://www.apachefriends.org/pt_br/index.html) (ou ambiente equivalente com Apache e MySQL) instalado na sua máquina.

 2. Clonar ou Baixar o Repositório

* Coloque a pasta do projeto `ciclomanos` dentro do diretório raiz do seu servidor web local (geralmente `htdocs` no XAMPP).

 3. Configurar a Base de Dados

1. Abra o painel do XAMPP e inicie os módulos **Apache** e **MySQL**.
2. Aceda ao [phpMyAdmin](http://localhost/phpmyadmin/).
3. Crie uma nova base de dados com o nome exato: `ciclomanos`.
4. Importe o ficheiro SQL estrutural do projeto contendo todas as tabelas necessárias.

 4. Aceder à Aplicação

Abra o seu navegador web e aceda ao link:

```text
http://localhost/ciclomanos/Index.php

```

---

 👥 Autoria

Projeto desenvolvido para fins académicos no âmbito do Trabalho de Conclusão de Curso (TCC) em Desenvolvimento de Sistemas / Software Engineering.

© 2026 CicloManos. Todos os direitos reservados.

```

```
