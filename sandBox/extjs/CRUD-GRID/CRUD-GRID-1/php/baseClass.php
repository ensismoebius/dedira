<?php
//Aqui temos uma simples demonstra��o de como fazer um c�digo organizado e orientado a objetos de forma
//a economizar trabalho, tempo e linhas de c�digo

/**
Classe base.
Esta classe � respons�vel por executar todo que � redundante a todas as demais classes, como por exemplo a conex�o ao banco de dados.
Aqui definimos algumas variaveis privadas contendo as informa��es de conecx�o com o banco de dados e algumas fun��es base como
a fun��o de cone��o.

As demais classes devem herdar desta classe e implementar apenas os m�todos de manipula��o de dados como insert, delete, select, update, etc.
**/
class base {
	private $db_server   = '127.0.0.1';
	private $db_user     = 'root';
	private $db_pass     = 'root';
	private $db_database = 'crudgrid';
	protected $conn      = null;
	protected $actions   = array();
	
	public function __construct($action=null){
		//Esta fun��o � disparada quando se cria o objeto
		//Ent�o ao criamos um objeto desta classe � executada uma conex�o ao banco de dados
		$this->_connect();
		//Se alguma a��o foi passada chama a fun��o que se encarrega desta tarefa
		if(isset($action)){
			$this->_execAction($action);
		}
	}
	
	/*
	 * Esta fun��o � disparada quando objeto � destru�do, assim devemos destruir tudo que criamos,
	 * como a nossa conecx�o
	 */
	public function __destruct(){
		mysql_close($this->conn);
	}
	
	protected function _connect(){
		//Conecta ao banco
		$this->conn = mysql_connect($this->db_server, $this->db_user, $this->db_pass);
		//Seleciona o banco de dados desejado
		mysql_select_db($this->db_database,$this->conn);
	}
	
	/****
	 * Fun��o que executa uma query no banco de dados, se um dia precisarmos mudar de banco teoricamente bastaria mudar
	 * as fun��es de manipula��o do banco que est�o abstraidas aqui, tamb�m facilita por n�o precisar passar 2 par�metros
	 * passando apenas o sql
	****/
	public function _select($sql){
		return mysql_query($sql,$this->conn);
	}
	
	/****
	 * Como o mysql n�o nos prove uma fun��o que retorne todos os registros em forma de array aqui crio uma que faz isso
	****/
	public function _fetch_all($query){
		$rows = array();
		while ($row = mysql_fetch_object($query)) {
			$rows[] = $row;
		}
		return $rows;
	}
	
	/****
	 * Aqui temos uma facilidade, geralmente o que fazermos � fazer um select montar um array e imprimir em JSON,
	 * chamando esta fun��o temos o resultado de um sql j� em array pronta para ser codificado em JSON
	****/
	public function _select_fetch_all($sql){
		return $this->_fetch_all($this->_select($sql));
	}
	
	/****
	 * Esta fun��o executa um m�todo da classe pelo seu nome em STRING caso ele exista e esteja na lista
	 * de a��es contiga em $actions, esta lista deve ser definida em cada classe filha
	****/
	public function _execAction($action){
		if((in_array($action, $this->actions))&&(method_exists($this, $action))){
			call_user_func(array($this, $action));
		}else{
			echo json_encode(array(
				'success' => false,
				'msg' => utf8_encode("A��o inv�lida: '$action'")
			));
		}
	}
}
?>