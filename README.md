# ProjetoOS
 
 Este sistema é baseado em um fluxo simplificado de abertura de Ordem de Serviço, com funcionalidades para cadastro de clientes, produtos e ordens de serviço.
 
 ## MENU
 - Arquivo: menu.php
 - Esta é a tela principal do sistema, onde o usuário é recebido com um gráfico de pizza e um gráfico de colunas que indicam a quantidade atual de registros de clientes, produtos e ordens de serviço. Também é apresentado um gráfico com a quantidade de produtos ativos e inativos do sistema.
 - Para exibir os gráficos, foi utilizada a biblioteca "Highcharts". No arquivo graficoPizza.php, constam as queries SQL para obter os dados referentes aos clientes, produtos e ordens de serviço, além dos dados dos produtos ativos e inativos. A configuração do gráfico de pizza e do gráfico de colunas, assim como as chamadas AJAX para buscar os dados, estão no arquivo menu.js. Além disso, o sistema permite gerar um arquivo CSV com todos os registros de clientes, produtos e ordens de serviço cadastrados.
   
 ![image](https://github.com/user-attachments/assets/93f1e866-0a78-4a27-9ec6-c6c231a02979)
 
 ## CLIENTE
 - Arquivo: cliente.php
 - Nesta tela, o usuário pode cadastrar clientes e editar aqueles já cadastrados. Entretanto, durante a edição, não é permitido alterar o CPF do cliente.
 - Foi criado o arquivo `cadastraCliente.php` para cadastrar clientes, o arquivo `listaCliente.php` para listar os clientes, o arquivo `buscaCliente.php` para buscar os dados de um cliente específico e o arquivo `editaCliente.php` para editar os dados de um cliente. No arquivo `buscaCep.php` existe a chamada ao serviço web viacep para buscar o CEP do usuário. No arquivo `cliente.js` estão todas as chamadas AJAX para cada arquivo PHP. O bloqueio da edição do CPF também é implementado no `cliente.js` quando o sistema busca as informações de um cliente específico para realizar a edição.
   
 ![image](https://github.com/user-attachments/assets/a27acdf5-5080-4ac3-a7e9-4b6eddbb7ec2)
 
 ## PRODUTO
 - Arquivo: produto.php
 - Nesta tela, o usuário pode cadastrar produtos e editar aqueles já cadastrados, podendo ativar ou inativar o produto no sistema.
 - Foi criado o arquivo `cadastraProduto.php` para cadastrar produtos, o arquivo `listaProduto.php` para listar os produtos cadastrados, o arquivo `buscaProduto.php` para buscar os dados de um produto específico, e o arquivo `editaProduto.php` para editar os dados de um produto. No arquivo `produto.js`, estão todas as chamadas AJAX para cada arquivo PHP.
 
 ![image](https://github.com/user-attachments/assets/a467ea62-9d76-418a-baf9-fe200f03f04d)
 
 ## CADASTRA OS
 - Arquivo: cadastra_os.php
 - Nesta tela, o usuário pode cadastrar uma ordem de serviço. Se o cliente que está sendo cadastrado não existir, o sistema cria o cliente normalmente. Além disso, o sistema lista apenas os produtos ativos para a abertura da ordem de serviço.
 - Foi criado o arquivo `cadastraOS.php` para que o usuário possa cadastrar a ordem de serviço. Nesse arquivo, já existe a verificação para inserir um novo cliente na tabela de clientes caso o cliente não exista. A chamada AJAX para o arquivo `cadastraOS.php` está no arquivo `os.js`. Além disso, no arquivo `os.js`, foi implementada a lógica para listar somente produtos ativos para que o usuário não possa cadastrar uma ordem de serviço com produtos inativos.
 
 ![image](https://github.com/user-attachments/assets/cc424cd9-c6de-4ca5-be63-99163fb9a420)
 
 ## CONSULTA OS
 - Arquivo: consulta_os.php
 - Nesta tela, o usuário pode visualizar as ordens de serviço cadastradas na tela anterior, podendo utilizar filtros para consultar ordens específicas. Também é possível finalizar as ordens de serviço que estão abertas. Além disso, o número da ordem de serviço é um link que direciona o usuário para o arquivo `os_press.php`, onde poderá visualizar melhor as informações da ordem. Para as ordens que não foram finalizadas, é exibido o botão "alterar", permitindo que o usuário edite os dados. O sistema não permite a edição de ordens de serviço já finalizadas.
 - Foi criado o arquivo `finalizaOS.php` para que o usuário finalize a ordem de serviço. O arquivo `listaOS.php` exibe as ordens de serviço cadastradas. O arquivo `filtraOS.php` possibilita que o usuário filtre as ordens por nome do consumidor ou por intervalos de datas. O arquivo `buscaOS.php` é responsável por buscar os dados da ordem de serviço, enquanto o arquivo `editaOS.php` permite a edição dos dados. Além disso, o arquivo `os_press.php` possibilita ao usuário visualizar as informações detalhadas da ordem. Todas as chamadas AJAX para os arquivos PHP estão centralizadas no arquivo `os.js`.
 
 ![image](https://github.com/user-attachments/assets/94b8dde4-6740-4bea-9ee8-5334943323df)
 
 ## DETALHES DA ORDEM DE SERVIÇO
 - Arquivo: os_press.php
 
 ![image](https://github.com/user-attachments/assets/99b85c76-ad72-45c5-be12-573c4893f238)
 
 
 ## Erik Delanda Zaros
