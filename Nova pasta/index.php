<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOSPITAL FATEC BRASIL</title>
    <link rel="stylesheet" href="./php.css">
</head>
<body>
    <div class='container'>
     
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Adicionar Funcionário</h2>
            <input placeholder= "Nome" type="text" name="nome" required><br>
            <input placeholder= "Idade" type="number" name="idade" required><br>
            <input placeholder= "Salário" type="number" step="0.01" name="salario" required><br>
            <input type="submit" name="enviar" value="Enviar">
        </form>
    </div>

    <?php
    $servername = "localhost:3306";
    $username= "root";
    $password= "";
    $database="php09";


    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
  
    function criarTabelaFuncionarios($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS funcionarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(50) NOT NULL,
            idade INT NOT NULL,
            salario DECIMAL(10, 2) NOT NULL
        )";

        if ($conn->query($sql) === TRUE) {
            echo "<br>";
        } else {
            echo "Erro ao criar tabela: " . $conn->error . "<br>";
        }
    }

    function adicionarFuncionario($conn, $nome, $idade, $salario) {
        $stmt = $conn->prepare("INSERT INTO funcionarios (nome, idade, salario) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $nome, $idade, $salario);

        if ($stmt->execute()) {
            echo "<br>";
        } else {
            echo "Erro ao adicionar funcionário: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }

    function exibirFuncionarios($conn) {
        $sql = "SELECT * FROM funcionarios";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<div class='container'>";
            echo "<div class='edit-delete-container'>";
            echo "<h2>Lista de Funcionários</h2>";
            while ($row = $result->fetch_assoc()) {
                echo "Nome: " . $row['nome'] . " | Idade: " . $row['idade'] . " | Salário: " . $row['salario'] . " | ";
                echo "<a href='?editar=" . $row['id'] . "' class='editar'>Editar</a> | ";
                echo "<a href='?excluir=" . $row['id'] . "' class='excluir'>Excluir</a><br>";
            }
            echo "</div>";
            echo "</div>";
        } else {
            echo "<br>";
        }
    }

    function editarFuncionario($conn, $id, $nome, $idade, $salario) {
        $stmt = $conn->prepare("UPDATE funcionarios SET nome = ?, idade = ?, salario = ? WHERE id = ?");
        $stmt->bind_param("sddi", $nome, $idade, $salario, $id);

        if ($stmt->execute()) {
            echo "<br>";
        } else {
            echo "Erro ao editar funcionário: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }

    function excluirFuncionario($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM funcionarios WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<br>";
        } else {
            echo "Erro ao excluir funcionário: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }

    criarTabelaFuncionarios($conn);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["enviar"])) {
            $nome = $_POST["nome"];
            $idade = $_POST["idade"];
            $salario = $_POST["salario"];
            adicionarFuncionario($conn, $nome, $idade, $salario);
        } elseif (isset($_POST["editar"])) {
            $id_editar = $_POST["id_editar"];
            $nome_editar = $_POST["nome_editar"];
            $idade_editar = $_POST["idade_editar"];
            $salario_editar = $_POST["salario_editar"];
            editarFuncionario($conn, $id_editar, $nome_editar, $idade_editar, $salario_editar);
        }
    }

    if (isset($_GET['excluir'])) {
        $id_excluir = $_GET['excluir'];
        excluirFuncionario($conn, $id_excluir);
    }

    // Se a variável de consulta "editar" estiver definida, exibe um formulário preenchido com os dados atuais do funcionário
    if (isset($_GET['editar'])) {
        $id_editar = $_GET['editar'];
        $sql_editar = "SELECT * FROM funcionarios WHERE id = $id_editar";
        $result_editar = $conn->query($sql_editar);

        if ($result_editar->num_rows == 1) {
            $row_editar = $result_editar->fetch_assoc();
            // Exibir um formulário preenchido com os dados atuais do funcionário
            echo "<div class='container'>";
            echo "<h2>Editar Funcionário</h2>";
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
            echo "Nome: <input type='text' name='nome_editar' value='" . $row_editar['nome'] . "' required><br>";
            echo "Idade: <input type='number' name='idade_editar' value='" . $row_editar['idade'] . "' required><br>";
            echo "Salário: <input type='number' step='0.01' name='salario_editar' value='" . $row_editar['salario'] . "' required><br>";
            echo "<input type='hidden' name='id_editar' value='" . $row_editar['id'] . "'>";
            echo "<input type='submit' name='editar' value='Editar'>";
            echo "</form>";
            echo "</div>";
        }
    }
    ?>

    <?php
    exibirFuncionarios($conn);
    $conn->close();
    ?>
</body>
</html>
