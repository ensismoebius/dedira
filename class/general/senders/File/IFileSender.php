<?php
interface IFileSender{
	public function connect();
	public function upload($filePathOrign, $directoryPathDestiny, $nameDestiny ,$callBackFunction = null);
	public function close();
	public function selftest();
}
?>