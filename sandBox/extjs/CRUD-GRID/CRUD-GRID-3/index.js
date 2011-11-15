Ext.onReady(function(){
	
	Ext.grid.ColumnModel.prototype.defaults = {sortable: true};
	
	//Aqui fa�o algumas defini��es padr�o para alguns componentes:
	//Todos os campos ficam com mensagem de erro ao lado, quando tiverem
	Ext.form.Field.prototype.msgTarget      = 'side';
	//Espa�amento ao redor do formul�rio
	Ext.form.FormPanel.prototype.bodyStyle  = 'padding:5px';
	//Alinhamento dos labels a direita
	Ext.form.FormPanel.prototype.labelAlign = 'right';
	//Aqui iniciamos o QuickTips para que quando o usu�rio passar
	//o mouse sobre o icone de erro de um campo seja mostrado
	//uma dica com o erro
	Ext.QuickTips.init();
	
	var usuario = Ext.extend(Ext.util.Observable, {
		//Aqui separa o titulo da janela do formul�rio pq irei muda-lo
		//de acordo com a a��o insert ou update
		formTitle: 'Cadastro de Usu�rios ',
		
		constructor: function(){
			with (this){
				initStores();
				//Adiciono mais uma fun��o de inicializa��o
				initForm();
				initPrincipal();
			}
		},
		
		//Fun��o que ser� chamada ao clicar no bot�o de adicionar da janela principal
		adicionar: function(){
			//Defino uma propriedade na janela do form pra indicar que estamos abrindo-a
			//para editar um registro e assim poder definir o que fazer na hora de salvar
			this.winForm.update = false;
			//Aqui altero o titulo pegando aquele titulo salvo acima e adicionando um texto
			//indicando que a��o estamos executando
			this.winForm.setTitle(this.formTitle+'[Inserindo]');
			//Mostramos a janela
			this.winForm.show();
			//Procuro o campo usu_login e defino que ele n�o � somente leitura podendo ser editado
			this.form.getForm().findField('usu_login').setReadOnly(false);
			//Limpo o formul�rio para iniciar a inser��o de um novo registro
			this.form.getForm().reset();
		},
		
		//Fun��o a ser chamada quando clicar no bot�o de editar da janela principal
		editar: function(){
			//Para editar precisamos que o usu�rio tenha selecionado um registro ent�o
			//verificamos se existe alguama sele��o no nosso grid.
			if(this.grid.getSelectionModel().hasSelection()){
				//Mudamos nossa propriedade para indicar que a janela est� em modo de atualiza��o
				this.winForm.update = true;
				//Mudamos o titulo para indicar ao usu�rio a a��o que est� sendo executada
				this.winForm.setTitle(this.formTitle+'[Alterando]');
				//mostramos a janela
				this.winForm.show();
				//Colocamos o campo usu_login como somente leitura, afinal ele � nossa chave primaria
				//e n�o pode ser alterada porque ser� usada em nossa clausula WHERE no PHP
				this.form.getForm().findField('usu_login').setReadOnly(true);
				//Carregamos o registro selecionado no grid para o nosso formul�rio
				//Lembrando que se tivermos os campos do form com os seus nomes iguais aos campos
				//do store basta fazer como abaixo
				this.form.getForm().loadRecord(this.grid.getSelectionModel().getSelected());
			}else{
				//Caso n�o tenhamos nenhuma linha selecionada avisamos ao usu�rio
				Ext.Msg.alert('Aten��o', 'Selecione um registro');
			}
		},
		
		//Fun��o a ser chamada quando clicar no bot�o de deletar da tela principal
		deletar: function(){
			//Novamente verificamos se o usu�rio selecionou alguma linha
			if(this.grid.getSelectionModel().hasSelection()){
				//Separamos o registro selecionado para uma variavel para evitar de
				//chamar estas fun��es com frequencia ja que usarei este registro mais
				//de uma vez abaixo
				var record = this.grid.getSelectionModel().getSelected();
				//Perguntamos ao usu�rio se ele realmente deseja excluir o registro
				Ext.Msg.confirm('Aten��o', 'Voc� est� prestes a excluir o usu�rio <b>'+record.data.usu_nome+'</b>. Deseja continuar?', function(btn){
					//Testamos qual bot�o ele clicou
					if(btn == 'yes'){
						//Se ele aceitou blz, criamos um AJAX passando o registro que queremos deletar
						Ext.Ajax.request({
							//Aqui o arquivo php que interage com nosso banco
							url: 'php/usuarios.php',
							//Paramentros que passaremos por POST
							params: {
								//A��o a ser executada
								_action: 'delete',
								//E passamos o login do cara que queremos deletar, pq s� o login?
								//Pq o login � nossa chave prim�ria, s� preciamos dela pra fazer um delete
								usu_login: record.data.usu_login
							},
							//Fun��o chamada quando n�o houver nenhum erro de pagina como 404, 500
							success: function(r){
								//Se tudo OK, pegamos a resposta que � um JSON e decodificamos para um objeto
								var obj = Ext.decode(r.responseText);
								//Verificamos se obtivemos sucesso na a��o
								if(obj.success){
									//Se sim removemos o registro do nosso store, menos trabalhoso que efetuar um reload
									this.dsUsuarios.remove(record);
								}else{
									//Caso tenha acontecido um erro mostra uma mensagem ao usu�rio com um texto vindo do servidor
									Ext.Msg.alert('Erro', obj.msg);
								}
							},
							//Fun��o executada se tivermos um erro de arquivo n encontrado ou coisa do tipo, 404, 500, etc
							failure: function(){
								//Mostramos uma mensagem ao usu�rio pedindo para contatar o administrador
								Ext.Msg.alert('Erro', 'Ocorreu um erro ao se comunicar com o servidor, tente novamente. Se o erro persistir entre em contato com o adiministrador do sistema')
							},
							scope: this
						})
					}
				}, this)
			}else{
				//Se n�o tivermos uma linha selecionada no grid avisa ao usu�rio
				Ext.Msg.alert('Aten��o', 'Selecione um registro');
			}
		},
		
		//Fun��o a ser chamada quando clicar no bot�o salvar do formul�rio
		salvar: function(){
			//Verificamos se o formul�rio est� valido de acordo com cada campo
			if(this.form.getForm().isValid()){
				//Se sim colocamos uma mascara de "Salvando" na janela do form
				//Impedindo que o usu�rio fique fun�ando na tela
				this.winForm.el.mask('Salvando', 'x-mask-loading');
				//Usamos a fun��o do form para salvar os dados, submit
				//os dados v�o por AJAX em POST
				this.form.getForm().submit({
					//Arquivo que faz a intera��o com o banco
					url: 'php/usuarios.php',
					params: {
						//Aqui temos if compacto para verificar qual a a��o que estava
						//sendo executada no form
						_action: this.winForm.update ? 'update' : 'insert'
					},
					//Fun��o chamada se retornado success:true
					success: function(){
						//Ent�o se tudo ok retiramos a mascara de 'Salvando'
						this.winForm.el.unmask();
						//E fechamos a janela
						this.winForm.hide();
						//Recarregamos o grid para visualizarmos as mudan�as
						this.dsUsuarios.reload();
					},
					//Fun��o chamada se retornado success:false
					failure: function(form, action){
						//Se tivemos problemas tiramos a mascara
						this.winForm.el.unmask();
						//E mostramos uma mensagem ao usu�rio informando o erro
						//vindo do servidor
						Ext.Msg.alert('Erro', action.result.msg);
					},
					scope: this
				})
			}else{
				//Se temos algum campo inv�lido avisamos ao usu�rio
				Ext.Msg.alert('Aten��o', 'Exixtem campos inv�lidos');
			}
		},
		
		//Fun��o chamada ao clicar no bot�o de cancelar do formul�rio
		cancelar: function(){
			//Apenas fechamos a janela
			this.winForm.hide();
		},
		
		initStores: function(){
			this.dsCategorias = new Ext.data.JsonStore({
				url: 'php/categorias.php',
				root: 'data',
				idProperty: 'cat_id',
				fields: [
					'cat_id',
					'cat_descricao'
				],
				baseParams: {
					_action: 'select'
				}
			})
			
			this.dsUsuarios = new Ext.data.JsonStore({
				url: 'php/usuarios.php',
				root: 'data',
				totalProperty: 'total',
				fields: [
					'usu_login',
					'usu_nome',
					'usu_senha',
					'usu_email',
					{name: 'usu_data_nascimento', type: 'date', dateFormat: 'Y-m-d g:i:s'},
					{name: 'cat_id', type: 'int'},
					{name: 'inserted', type: 'bool'}
				],
				baseParams: {
					_action: 'selectLimited',
					start  : 0,
					limit  : 6
				}
			})
		},
		
		//Aqui nossa fun��o que ir� criar nosso form
		initForm: function(){
			//Criamos o formul�rio
			this.form = new Ext.form.FormPanel({
				border: false, //Tiramos a borda azul
				labelWidth: 70, //Definimos a largura dos labels
				items: [{
					xtype     : 'textfield', //Tupo do campo
					name      : 'usu_nome', //Nome a ser enviado pro server e para ser carregado do store
					fieldLabel: 'Nome', //Nome vis�vel ao usu�rio
					width     : 300, //Largura do campo
					allowBlank: false //N�o permite campo em branco
				},{
					xtype     : 'textfield',
					name      : 'usu_login',
					fieldLabel: 'Login',
					width     : 100,
					allowBlank: false
				},{
					xtype     : 'textfield',
					name      : 'usu_senha',
					fieldLabel: 'Senha',
					width     : 100,
					allowBlank: false,
					col       : true //Criamos uma coluna, uso aqui meu override do formpanel
				},{
					xtype     : 'textfield',
					name      : 'usu_email',
					fieldLabel: 'E-mail',
					width     : 300,
					allowBlank: true
				},{
					xtype     : 'datefield',
					name      : 'usu_data_nascimento',
					fieldLabel: 'Data Nasc.',
					width     : 100,
					allowBlank: false
				},{
					xtype     : 'combo',
					name      : 'cat_id',
					fieldLabel: 'Categoria',
					width     : 100,
					allowBlank: false,
					col       : true,
					//Store de onde o combo pegara sua lista de dados
					store        : this.dsCategorias,
					//Campo que ser� usado como valor
					valueField   : 'cat_id',
					//Campo que ser� mostrado na lista
					displayField : 'cat_descricao',
					//Nome do parametro que ser� enviado ao php com o valor
					hiddenName   : 'cat_id',
					//necess�rio para o combo buscar os dados do store
					triggerAction: 'all'
				}]
			})
			
			//Aqui criamos a janela que conter� nosso form
			this.winForm = new Ext.Window({
				title      : this.formTitle,
				height     : 180,
				width      : 430,
				modal      : true, //Bloqueia o resto da aplica��o for�ando o usu�rio a terminar a a��o que come�ou nesta tela
				closeAction: 'hide', //Quando fechada a janela � apenas escondida para n�o precisar ser criada novamente
				layout     : 'fit', //Aqui definimos que o filho desta tela ir� ocupar toda a �rea disponivel na janela
				items      : this.form,
				buttonAlign: 'center', //Alinhamos os bot�es no meio horizontalmente
				buttons    : [{
					text   : 'Salvar',
					iconCls: 'btn-save',
					scope  : this,
					handler: this.salvar
				},{
					text   : 'Cancelar',
					iconCls: 'btn-cancel',
					scope  : this,
					handler: this.cancelar
				}]
			})
		},
		
		initPrincipal: function(){
			this.grid = new Ext.grid.GridPanel({
				store     : this.dsUsuarios,
				loadMask  : true,
				border    : false,
				stripeRows: true,
				columns: [{
					dataIndex: 'usu_nome', 
					header   : 'Usu�rio',
					width    : 170
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
					xtype    : 'datecolumn'
				},{
					dataIndex: 'cat_id',
					header   : 'Categoria',
					width    : 80,
					renderer: {
						scope: this,
						fn   : function(value, metaData, record, rowIndex, colIndex, store){
							if(this.dsCategorias.getById(value)){
								return this.dsCategorias.getById(value).data.cat_descricao;
							}
						}
					}
				}],
				viewConfig: {
					forceFit: true,
					emptyText: '<center>Sem registros para exibir</center>'
				}
			});
			
			this.ptb = new Ext.PagingToolbar({
				store: this.dsUsuarios,
				pageSize: this.dsUsuarios.baseParams.limit,
				displayInfo: true
			})
			
			this.winGrid = new Ext.Window({
				title : '<center>CRUD Completo - Parte 3</center>',
				height: 240,
				width : 620,
				layout: 'fit',
				items : this.grid,
				listeners: {
					show: function(){
						this.dsCategorias.load({
							callback: function(){
								this.dsUsuarios.load();
							},
							scope: this
						});
					},
					scope: this
				},
				bbar: this.ptb,
				//Aqui apenas adicionamos uma barra no topo com com os bot�es de adicionar, editar e deletar
				tbar: [{
					text   : 'Adicionar',
					iconCls: 'btn-add',
					scope  : this,
					handler: this.adicionar
				},{
					text   : 'Editar',
					iconCls: 'btn-edit',
					scope  : this,
					handler: this.editar
				},{
					text   : 'Deletar',
					iconCls: 'btn-delete',
					scope  : this,
					handler: this.deletar
				}]
			})
			
			this.winGrid.show();
		}
	});
	var cadUsuario = new usuario;
})