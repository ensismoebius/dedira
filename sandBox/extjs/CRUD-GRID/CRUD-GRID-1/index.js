Ext.onReady(function(){
	//Definimos que todas as colunas de todos os grid ser�o ornden�veis
	Ext.grid.ColumnModel.prototype.defaults = {sortable: true};
	
	//Criamos nossa classe herdando da classe base do ExtJS, a Observable
	var usuario = Ext.extend(Ext.util.Observable, {
		
		//�sta fun��o � disparada autom�ticamente quando se cria uma classe
		//Aqui definimos subfun��es que ir�o come�ar a montar nossa tela
		constructor: function(){
			/*
			 * with coloca todo o c�digo dentro de seu bloco no escopo do
			 * objeto entre parenteses, neste caso "this",
			 * Seria a mesma coisa que fazer:
			 * this.initStores();
			 * this.initPrincipal();
			 */
			with (this){
				//Aqui separo a cria��o dos stores que devem ser criados
				//antes dos componentes que os usar�o
				initStores();
				//Aqui crio a tela principal da aplica��o, a Window com
				//um Grid dentro
				initPrincipal();
			}
		},
		
		initStores: function(){
			//Cria��o do Store, JsonStore pq receberemos dados em formato JSON
			this.dsCategorias = new Ext.data.JsonStore({
				url       : 'php/categorias.php', //Arquivo de onde os dados devem ser buscados
				root      : 'data', //Propriedade que cont�m os registros
				idProperty: 'cat_id', //Propriedade que ser� o id do store, caso haja uma coluna primaryKey use-a aqui
				fields    : [ //Lista de campos que devem ser mapeados para o store
					'cat_id',
					'cat_descricao'
				],
				baseParams: { //Parametros a serem enviados na requisi��o ajax
					_action: 'select' //A��o que queremos executar no PHP, deve ser tratada do lado servidor
				}
			})
			
			this.dsUsuarios = new Ext.data.JsonStore({
				url: 'php/usuarios.php',
				root: 'data',
				fields: [
					'usu_login', //Caso um campo deva ser apenas adicionado basta colocar o seu nome
					'usu_nome',
					'usu_senha',
					'usu_email',
					//Caso precisarmos passar mais dados sobre este campo devemos passar um objeto
					//como por exemplo no caso de campos de data devemos passar o formato que esta data vem do banco
					{name: 'usu_data_nascimento', type: 'date', dateFormat: 'Y-m-d g:i:s'},
					{name: 'cat_id', type: 'int'},
					{name: 'inserted', type: 'bool'}
				],
				baseParams: {
					_action: 'select'
				}
			})
		},
		
		initPrincipal: function(){
			//Aqui criamos o Grid � respons�vel por mostrar os dados ao usu�rio
			this.grid = new Ext.grid.GridPanel({
				store     : this.dsUsuarios, //Definimos de qual store o grid deve buscar os dados
				loadMask  : true, //Aqui definimos que queremos uma mascara de "carregando" quando o grid estiver buscando dados
				border    : false, //Aqui tiramos uma borda fina que fica ao redor do grid, teste com true para verificar a diferen�a, � sutil
				stripeRows: true, //Dixa as linhas zebradas
				columns: [{ //Aqui definimos cada coluna do grid, n�o, n�o basta ligar o grid ao stora
					dataIndex: 'usu_nome',  //Campo do store que esta coluna deve mostrar os dados
					header   : 'Usu�rio',  //Titulo da coluna que ser� visivel ao usu�rio
					width    : 170 //Largura da coluna em pixels
				},{
					dataIndex: 'usu_login', 
					header   : 'Login',
					width    : 50
				},{
					dataIndex: 'usu_senha', 
					header   : 'Senha',
					width    : 50
				},{
					dataIndex: 'usu_email', 
					header   : 'E-mail',
					width    : 180
				},{
					dataIndex: 'usu_data_nascimento', 
					header   : 'Dt. Nasc.',
					width    : 70,
					xtype    : 'datecolumn' //Aqui definimos um tipo de coluna, datecolumn ir� renderizar a data no formato da lingua, nosso calo pt_BR
				},{
					dataIndex: 'cat_id',
					header   : 'Categoria',
					width    : 80,
					renderer: { //Aqui temos uma coluna que � na verdade o id de uma categoria e queremos mostrar a descri��o desta categoria
					            //Por isso definimos uma fun��o de renderiza��o
						scope: this, //Escopo para podermos pegar objetos dentro desta fun��o atrav�z do this
						fn   : function(value, metaData, record, rowIndex, colIndex, store){
							//procuramos o valor, que � o id da categoria, no store de categorias
							if(this.dsCategorias.getById(value)){
								//achando o registro pegamos a descri��o do mesmo e retornamos para ser mostrado na coluna
								return this.dsCategorias.getById(value).data.cat_descricao;
							}
						}
					}
				}],
				viewConfig: {
					forceFit: true, //Mant�m as colunas com tamanho m�ximo, evita espa�os em branco depois das colunas
					emptyText: '<center>Sem registros para exibir</center>' //Texto a ser mostrado quando n�o houver dados no grid
				}
			});
			
			//Aqui criamos a janela
			this.winGrid = new Ext.Window({
				title : '<center>CRUD Completo - Parte 1</center>', //Titulo da janela, centarlizado
				height: 240, //Altura
				width : 620, //Largura
				layout: 'fit', //Aqui definimos que o filho da janela, nosso grid, ficar� com o tamanho total da janela
				items : this.grid, //Atribu�mos o filho a janela
				listeners: { //Eventos
					show: function(){ //Ao abrir a janela
						//Carrega o Store de categorias
						this.dsCategorias.load({ 
							callback: function(){ //Fun��o disparada quando o store de categorias terminar de carregar
								//Carrega o store de Usu�rios
								this.dsUsuarios.load();
							},
							scope: this
						});
					},
					scope: this
				}
			})
			
			//Mostramos a janela
			this.winGrid.show();
		}
	});
	//Aqui criamos o objeto, isso dispara todo o nosso c�digo acima
	var cadUsuario = new usuario;
})