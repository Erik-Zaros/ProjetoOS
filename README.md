# ProjetoOS

## Objetivo do Projeto

Este sistema é baseado em um fluxo simplificado de abertura de Ordem de Serviço, com funcionalidades para cadastro de clientes, produtos e ordens de serviço.

## MENU
- Arquivo: menu.html
- Esta é a tela principal do sistema, onde o usuário é recebido com um gráfico de pizza e um gráfico de colunas que indicam a quantidade atual de registros de clientes, produtos e ordens de serviço.
- Para exibir os gráficos, foi utilizada a biblioteca "Highcharts". No arquivo `grafico_pizza.php`, estão contidas as queries SQL para obter os dados referentes aos clientes, produtos e ordens de serviço. A configuração do gráfico de pizza e do gráfico de colunas, assim como as chamadas AJAX para buscar os dados, estão no arquivo `menu.js`. Além disso, o sistema permite gerar um arquivo CSV com todos os registros de clientes, produtos e ordens de serviço cadastrados.
![image](https://github.com/user-attachments/assets/f8fc3c45-da72-4d21-b547-cfe879fb4223)

## CLIENTE
- Arquivo: cliente.html
- Nesta tela, o usuário pode cadastrar clientes e editar aqueles já cadastrados. No entanto, durante a edição, não é permitido ao usuário alterar o CPF do cliente.
- Foi criado o arquivo `cadastrar_cliente.php` para cadastrar clientes, o arquivo `listar_clientes.php` para listar os clientes, o arquivo `buscar_cliente.php` para buscar os dados de um cliente específico, e o arquivo `editar_cliente.php` para editar os dados de um cliente. No arquivo `cliente.js`, estão todas as chamadas AJAX para cada arquivo PHP. O bloqueio da edição do CPF também é implementado no arquivo `cliente.js`, quando o sistema busca as informações de um cliente específico para realizar a edição.
  
![image](https://github.com/user-attachments/assets/5aab1dba-3d42-4265-8b2a-c2f192536672)

## PRODUTO
- Arquivo: produto.html
- Nesta tela, o usuário pode cadastrar produtos e editar aqueles já cadastrados, podendo ativar ou inativar o produto no sistema.
- Foi criado o arquivo `cadastrar_produto.php` para cadastrar produtos, o arquivo `listar_produtos.php` para listar os produtos cadastrados, o arquivo `buscar_produtos.php` para buscar os dados de um produto específico, e o arquivo `editar_produto.php` para editar os dados de um produto. No arquivo `produto.js`, estão todas as chamadas AJAX para cada arquivo PHP.

![image](https://github.com/user-attachments/assets/37a81f3b-1fbf-434f-8e16-dbb0e0e64a8c)

## CADASTRA OS
- Arquivo: cadastra_os.html
- Nesta tela, o usuário pode cadastrar uma ordem de serviço. Se o cliente que está sendo cadastrado não existir, o sistema cria o cliente normalmente. Além disso, o sistema lista apenas os produtos ativos para a abertura da ordem de serviço.
- Foi criado o arquivo `cadastrar_os.php` para que o usuário possa cadastrar a ordem de serviço. Nesse arquivo, já existe a verificação para inserir um novo cliente na tabela de clientes caso o cliente não exista. A chamada AJAX para o arquivo `cadastrar_os.php` está no arquivo `os.js`. Além disso, no arquivo `os.js`, foi implementada a lógica para listar somente produtos ativos para que o usuário não possa cadastrar uma ordem de serviço com produtos inativos.

![image](https://github.com/user-attachments/assets/ecec08a7-0d8a-455e-a0ee-fa44cc0b3f81)

## CONSULTA OS
- Arquivo: consulta_os.html
- Nesta tela, o usuário consegue visualizar as ordens de serviço que foram cadastradas na tela anterior, podendo utilizar filtros para consultar ordens de serviço específicas. Também é possível ao usuário finalizar as ordens de serviço que estão abertas. Além disso, o número da ordem de serviço é um link que direciona o usuário para a tela de cadastro da ordem de serviço, com os dados preenchidos, permitindo a edição dos dados, exceto o número da ordem de serviço. Após a edição, o usuário é redirecionado novamente para a tela de consulta de ordens de serviço.
- Foi criado o arquivo `finalizar_os.php` para que o usuário possa finalizar a ordem de serviço. O arquivo `listar_os.php` exibe as ordens de serviço cadastradas. O arquivo `filtrar_os.php` permite que o usuário filtre por ordens de serviço específicas, pelo nome do consumidor ou por intervalos de datas. O arquivo `buscar_os.php` é responsável por buscar os dados da ordem de serviço, e o arquivo `editar_os.php` permite ao usuário editar os dados da ordem de serviço. No arquivo `os.js`, estão todas as chamadas AJAX para cada arquivo PHP.

![image](https://github.com/user-attachments/assets/e3b319ed-3eb7-4e37-a358-4cd08c3fc298)

## Erik Delanda Zaros
