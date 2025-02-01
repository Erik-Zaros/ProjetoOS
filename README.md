# ProjetoOS

## Objetivo do Projeto

Este sistema é baseado em um fluxo simplificado de abertura de Ordem de Serviço, com funcionalidades para cadastro de clientes, produtos e ordens de serviço.

## MENU
- Arquivo: menu.php
- Esta é a tela principal do sistema, onde o usuário é recebido com um gráfico de pizza e um gráfico de colunas que indicam a quantidade atual de registros de clientes, produtos e ordens de serviço.
- Para exibir os gráficos, foi utilizada a biblioteca "Highcharts". No arquivo `graficoPizza.php`, estão contidas as queries SQL para obter os dados referentes aos clientes, produtos e ordens de serviço. A configuração do gráfico de pizza e do gráfico de colunas, assim como as chamadas AJAX para buscar os dados, estão no arquivo `menu.js`. Além disso, o sistema permite gerar um arquivo CSV com todos os registros de clientes, produtos e ordens de serviço cadastrados.

![image](https://github.com/user-attachments/assets/009c1a02-442a-4508-8308-8b5a89334566)

## CLIENTE
- Arquivo: cliente.php
- Nesta tela, o usuário pode cadastrar clientes e editar aqueles já cadastrados. No entanto, durante a edição, não é permitido ao usuário alterar o CPF do cliente.
- Foi criado o arquivo `cadastraCliente.php` para cadastrar clientes, o arquivo `listaCLiente.php` para listar os clientes, o arquivo `buscaCliente.php` para buscar os dados de um cliente específico, e o arquivo `editaCliente.php` para editar os dados de um cliente. No arquivo `cliente.js`, estão todas as chamadas AJAX para cada arquivo PHP. O bloqueio da edição do CPF também é implementado no arquivo `cliente.js`, quando o sistema busca as informações de um cliente específico para realizar a edição.
  
![image](https://github.com/user-attachments/assets/4bb9ab66-8804-4f25-a297-49560b531684)

## PRODUTO
- Arquivo: produto.php
- Nesta tela, o usuário pode cadastrar produtos e editar aqueles já cadastrados, podendo ativar ou inativar o produto no sistema.
- Foi criado o arquivo `cadastraProduto.php` para cadastrar produtos, o arquivo `listaProduto.php` para listar os produtos cadastrados, o arquivo `buscaProduto.php` para buscar os dados de um produto específico, e o arquivo `editaProduto.php` para editar os dados de um produto. No arquivo `produto.js`, estão todas as chamadas AJAX para cada arquivo PHP.

![image](https://github.com/user-attachments/assets/4aa54be4-15bf-4698-9dde-ba1429b02dca)

## CADASTRA OS
- Arquivo: cadastra_os.php
- Nesta tela, o usuário pode cadastrar uma ordem de serviço. Se o cliente que está sendo cadastrado não existir, o sistema cria o cliente normalmente. Além disso, o sistema lista apenas os produtos ativos para a abertura da ordem de serviço.
- Foi criado o arquivo `cadastraOS.php` para que o usuário possa cadastrar a ordem de serviço. Nesse arquivo, já existe a verificação para inserir um novo cliente na tabela de clientes caso o cliente não exista. A chamada AJAX para o arquivo `cadastraOS.php` está no arquivo `os.js`. Além disso, no arquivo `os.js`, foi implementada a lógica para listar somente produtos ativos para que o usuário não possa cadastrar uma ordem de serviço com produtos inativos.

![image](https://github.com/user-attachments/assets/e019f003-cb8f-4674-a7bf-c8318f41a078)

## CONSULTA OS
- Arquivo: consulta_os.php
- Nesta tela, o usuário consegue visualizar as ordens de serviço que foram cadastradas na tela anterior, podendo utilizar filtros para consultar ordens de serviço específicas. Também é possível ao usuário finalizar as ordens de serviço que estão abertas. Além disso, o número da ordem de serviço é um link que direciona o usuário para a tela de cadastro da ordem de serviço, com os dados preenchidos, permitindo a edição dos dados, exceto o número da ordem de serviço. Após a edição, o usuário é redirecionado novamente para a tela de consulta de ordens de serviço.
- Foi criado o arquivo `finalizaOS.php` para que o usuário possa finalizar a ordem de serviço. O arquivo `listaOS.php` exibe as ordens de serviço cadastradas. O arquivo `filtraOS.php` permite que o usuário filtre por ordens de serviço específicas, pelo nome do consumidor ou por intervalos de datas. O arquivo `buscaOS.php` é responsável por buscar os dados da ordem de serviço, e o arquivo `editaOS.php` permite ao usuário editar os dados da ordem de serviço. No arquivo `os.js`, estão todas as chamadas AJAX para cada arquivo PHP.

![image](https://github.com/user-attachments/assets/32b89cff-27b2-4d31-8d32-d96e70148d70)

## Erik Delanda Zaros
