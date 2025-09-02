<?php
class TipoSanguineo {
    private $conexao;

    public function __construct($db) {
        $this->conexao = $db;
    }

    public function calcular($idUsuario) {
        $query = "SELECT id_pai, id_mae FROM perfil WHERE usuario_idusuario = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":id", $idUsuario);
        $stmt->execute();
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$perfil) {
            return "Usuário não encontrado.";
        }

        $idPai = $perfil['id_pai'];
        $idMae = $perfil['id_mae'];

        // Busca tipo sanguíneo e fator do pai
        $queryPai = "SELECT tipo_sanguineo, fator FROM perfil WHERE usuario_idusuario = :id";
        $stmtPai = $this->conexao->prepare($queryPai);
        $stmtPai->bindParam(":id", $idPai);
        $stmtPai->execute();
        $pai = $stmtPai->fetch(PDO::FETCH_ASSOC);

        // Busca tipo sanguíneo e fator da mãe
        $queryMae = "SELECT tipo_sanguineo, fator FROM perfil WHERE usuario_idusuario = :id";
        $stmtMae = $this->conexao->prepare($queryMae);
        $stmtMae->bindParam(":id", $idMae);
        $stmtMae->execute();
        $mae = $stmtMae->fetch(PDO::FETCH_ASSOC);

        if (!$pai || !$mae) {
            return "Dados de pai ou mãe não encontrados.";
        }

        $pai_tipo = $pai['tipo_sanguineo'];
        $pai_rh   = $pai['fator'];
        $mae_tipo = $mae['tipo_sanguineo'];
        $mae_rh   = $mae['fator'];

        // Regras dos tipos sanguíneos
        $regras = [
            'A' => ['A' => ['A', 'O'], 'B' => ['A', 'B', 'AB', 'O'], 'AB' => ['A', 'B', 'AB'], 'O' => ['A', 'O']],
            'B' => ['A' => ['A', 'B', 'AB', 'O'], 'B' => ['B', 'O'], 'AB' => ['A', 'B', 'AB'], 'O' => ['B', 'O']],
            'AB' => ['A' => ['A', 'B', 'AB'], 'B' => ['A', 'B', 'AB'], 'AB' => ['A', 'B', 'AB'], 'O' => ['A', 'B']],
            'O' => ['A' => ['A', 'O'], 'B' => ['B', 'O'], 'AB' => ['A', 'B'], 'O' => ['O']]
        ];

        $filho_tipos = $regras[$pai_tipo][$mae_tipo];

        // Regras do fator Rh
        if ($pai_rh === '+' && $mae_rh === '+') {
            $filho_rh = ['+', '-'];
        } elseif ($pai_rh === '+' && $mae_rh === '-') {
            $filho_rh = ['+', '-'];
        } elseif ($pai_rh === '-' && $mae_rh === '+') {
            $filho_rh = ['+', '-'];
        } else {
            $filho_rh = ['-'];
        }

        return [
            "pai" => "{$pai_tipo}{$pai_rh}",
            "mae" => "{$mae_tipo}{$mae_rh}",
            "filho_tipos" => $filho_tipos,
            "filho_rh" => $filho_rh
        ];
    }
}
?>
