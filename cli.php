<?php
ini_set('memory_limit', '-1');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require_once __DIR__ . '/src/SimpleXLSX.php';

$filename = $argv[1];

$num_planilha = null;

if (!isset($argv[2])) {
	echo "O número da planilha não foi inserido.\n";
	exit(0);
	die;
}

$num_planilha = (int)$argv[2];

if (!is_integer($num_planilha)) {
	echo "Não é número.\n";
	exit(0);
	die;
}

if (!$num_planilha) {
	echo "O número não foi inserido.\n";
	exit(0);
	die;
}

echo "Iniciando: Planilha $num_planilha\n";
echo "Fazendo leitura...\n";

if ($xlsx = SimpleXLSX::parse($filename)) {

	$servername = "localhost";
	$username = "root";
	$password = "chkdsk";
	$dbname = "heroku_9d8f3f02545d669";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	echo "Conexão realizada.\n";


	$sql = "INSERT INTO `heroku_9d8f3f02545d669`.`clientes`
	(
    `id`,
	`name`,
	`endereco`,
	`bairro`,
	`cep`,
	`localidade`,
	`cod_localidade`,
	`nome_municipio`,
	`email`,
	`num_telefone1`,
	`num_telefone2`,
	`num_planilha`) VALUES ";
	
	$rows = $xlsx->rows();

	echo "Percorrendo no loop...\n";
	foreach ($rows as $key => $value) {
		echo "Chave:", $key, "\n";
		if ($key == 0) {
			$sql .= '(
				"' . $value[0] . '",
				"' . $value[1] . '",
				"' . $value[2] . '",
				"' . $value[3] . '",
				"' . $value[4] . '",
				"' . $value[5] . '",
				"' . $value[6] . '",
				"' . $value[7] . '",
				"' . $value[8] . '",
				"' . $value[9] . '",
				"' . $value[10] . '",
				"' . $num_planilha . '")';
		} else {
			$sql .= ',(
				"' . $value[0] . '",
				"' . $value[1] . '",
				"' . $value[2] . '",
				"' . $value[3] . '",
				"' . $value[4] . '",
				"' . $value[5] . '",
				"' . $value[6] . '",
				"' . $value[7] . '",
				"' . $value[8] . '",
				"' . $value[9] . '",
				"' . $value[10] . '",
				"' . $num_planilha . '")';
		}
	}

	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: <br>" . $conn->error;
	}

	$conn->close();
} else {
	echo SimpleXLSX::parseError();
}
