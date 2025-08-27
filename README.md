# ServiceCore

Sistema de gestão simplificada para abertura e acompanhamento de Ordens de Serviço, com funcionalidades para cadastro de clientes, produtos e peças.

 ## Login
 - Arquivo: login.php
- Tela inicial de acesso ao sistema. Usuários (Postos Autorizados) utilizam suas credenciais para autenticação.
 <img width="1833" height="961" alt="image" src="https://github.com/user-attachments/assets/a7f040e5-493d-4df9-ac88-8d02dee2c353" />

 ## Menu
 - Arquivo: view/menu.php
 - Primeira tela após o login. Exibe painéis com gráficos informativos sobre:
 - Status das Ordens de Serviço
 - Situação dos Produtos cadastrados
 - Situação das Peças
 - Também é possível gerar relatórios CSV contendo os registros do sistema.
 
<img width="1834" height="962" alt="image" src="https://github.com/user-attachments/assets/bd12ae1a-0a48-4c00-beb7-93a74a310e9e" />

 ## Usuários Admin
 - Arquivo: view/usuarios.php
 - Tela para cadastrar e editar os usuários com acesso administrativo ao sistema.

 <img width="1836" height="964" alt="image" src="https://github.com/user-attachments/assets/81606a58-031f-46b4-aaab-e967d9a3f431" />

 ## Cliente
 - Arquivo: view/cliente.php
 - Tela para cadastrar e editar os clientes (consumidores finais), que serão vinculados às ordens de serviço.

 <img width="1836" height="964" alt="image" src="https://github.com/user-attachments/assets/a89130ce-d0ba-43a2-b8be-1d19e6239352" />

 ## Produto
 - Arquivo: view/produto.php
 - Tela para cadastrar, editar e excluir produtos disponíveis no sistema.

 <img width="1837" height="964" alt="image" src="https://github.com/user-attachments/assets/7cebc596-3dae-434e-af09-9529a5760be8" />

 ## Peça
 - Arquivo: view/peca.php
 - Tela para cadastrar, editar e excluir peças utilizadas nas ordens de serviço.

 <img width="1836" height="964" alt="image" src="https://github.com/user-attachments/assets/72cee092-28c9-4608-ace5-28e10fa5497b" />

 ## Cadastro Os
 - Arquivo: view/cadastra_os.php
 - Tela de cadastro de Ordem de Serviço, onde é possível:
 - Selecionar um cliente já cadastrado ou cadastrar um novo automaticamente
 - Selecionar o produto relacionado à OS
 <img width="1837" height="955" alt="image" src="https://github.com/user-attachments/assets/97151170-b7ea-49e3-a4b5-cefea5091312" />

 ## Consulta Os
 - Arquivo: view/consulta_os.php
 - Tela para visualizar todas as Ordens de Serviço cadastradas.
 - É possível editar, cancelar ou finalizar uma OS existente.
 - Ao clicar no número da OS, o usuário é redirecionado para a tela de detalhamento completo da ordem.

<img width="1835" height="964" alt="image" src="https://github.com/user-attachments/assets/f5da4ba7-81d5-453b-a3f3-172f65c7fb71" />

 ## Detalhes da ordem de serviço
 - Arquivo: os_press.php
 - Tela exibida ao clicar no número da OS na consulta. Apresenta todos os dados da ordem em detalhes.

 <img width="1837" height="961" alt="image" src="https://github.com/user-attachments/assets/6843fb6a-eaa1-489d-bab5-6a9205826978" />

 ## Erik Delanda Zaros
