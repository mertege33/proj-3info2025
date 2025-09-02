<?php

class Origens {
    private int $userId;
    private int $maxGen;
    private array $treeNodes = [];
    private array $visited = [];
    private array $aggregate = [];
    private float $totalWeight = 0.0;
    private int $usedNodesCount = 0;

    public function __construct(int $userId, int $maxGen = 6) {
        $this->userId = $userId;
        $this->maxGen = $maxGen;
    }

    public function calcular(): void {
        $perfil = $this->dbFetch(
            "SELECT usuario_idusuario, id_pai, id_mae, nacionalidade FROM perfil WHERE usuario_idusuario = :uid LIMIT 1",
            [':uid' => $this->userId]
        );

        if (!$perfil) {
            throw new Exception("Perfil do usuário não encontrado. Para calcular ancestrais é preciso um registro em `perfil` ligado ao usuário.");
        }

        $pai = (int)($perfil['id_pai'] ?? 0);
        $mae = (int)($perfil['id_mae'] ?? 0);

        if (($pai <= 0 && $mae <= 0) && (!isset($perfil['nacionalidade']) || trim((string)$perfil['nacionalidade']) === '')) {
            throw new Exception("Nenhum ancestral encontrado (id_pai/id_mae ausentes) e sem nacionalidade no perfil do usuário.");
        }

        if ($pai > 0) $this->traverseAncestors($pai, 1);
        if ($mae > 0) $this->traverseAncestors($mae, 1);

        if ($this->totalWeight == 0.0) {
            if (isset($perfil['nacionalidade']) && trim((string)$perfil['nacionalidade']) !== '') {
                $nat = $perfil['nacionalidade'];
                $this->aggregate[$nat] = ($this->aggregate[$nat] ?? 0.0) + 1.0;
                $this->totalWeight = 1.0;
                $this->treeNodes[] = [
                    'id' => $this->userId,
                    'nome' => $this->fetchUserName($this->userId),
                    'nacionalidade' => $nat,
                    'generation' => 0,
                    'id_pai' => null,
                    'id_mae' => null,
                ];
                $this->usedNodesCount++;
            } else {
                throw new Exception("Nenhuma nacionalidade encontrada nos ancestrais, nem nacionalidade do próprio perfil.");
            }
        }
    }

    public function getPercentuais(): array {
        if ($this->totalWeight <= 0) return [];
        $percentages = [];
        foreach ($this->aggregate as $country => $w) {
            $percentages[$country] = ($w / $this->totalWeight) * 100.0;
        }
        arsort($percentages);
        return $percentages;
    }

    public function getTree(): array {
        return $this->treeNodes;
    }

    public function getUsedNodesCount(): int {
        return $this->usedNodesCount;
    }

    public function renderTreeHTML(): string {
        $nodes = $this->treeNodes;
        $html = '<div class="tree-raw" style="margin-top:12px;">';
        $html .= '<h4>Árvore de ancestrais (visual)</h4>';
        $html .= '<div style="font-size:13px; color:#dfeff1;">';
        usort($nodes, function($a,$b){ return ($a['generation'] ?? 0) <=> ($b['generation'] ?? 0); });
        foreach ($nodes as $n) {
            $gen = (int)($n['generation'] ?? 0);
            $indent = max(0, $gen - 1) * 18;
            $name = !empty($n['nome']) ? htmlspecialchars($n['nome']) : "Usuário #".htmlspecialchars($n['id'] ?? '');
            $nat = !empty($n['nacionalidade']) ? htmlspecialchars($n['nacionalidade']) : '<span style="opacity:0.7">— não informado —</span>';
            $html .= "<div style='padding-left:{$indent}px; margin-bottom:8px; display:flex; gap:10px; align-items:center;'>";
            $html .= "<div style='min-width:220px'><strong>{$name}</strong></div>";
            $html .= "<div style='width:160px'>Nacionalidade: {$nat}</div>";
            $html .= "<div style='color:#9bd; font-size:13px;'>Geração: ".($gen===0 ? 'º (próprio)' : $gen)."</div>";
            $html .= "</div>";
        }
        $html .= '</div></div>';
        return $html;
    }

    private function traverseAncestors(int $userId, int $generation = 1): void {
        if ($userId <= 0) return;
        if ($generation > $this->maxGen) return;
        if (isset($this->visited[$userId])) return;
        $this->visited[$userId] = true;

        $profile = $this->dbFetch(
            "SELECT usuario_idusuario, id_pai, id_mae, nacionalidade FROM perfil WHERE usuario_idusuario = :uid LIMIT 1",
            [':uid' => $userId]
        );

        $userRow = $this->dbFetch(
            "SELECT id_usuario, nome FROM usuario WHERE id_usuario = :uid LIMIT 1",
            [':uid' => $userId]
        );

        $node = [
            'id' => $userId,
            'nome' => $userRow ? $userRow['nome'] : null,
            'nacionalidade' => $profile && trim((string)($profile['nacionalidade'] ?? '')) !== '' ? $profile['nacionalidade'] : null,
            'generation' => $generation,
            'id_pai' => $profile ? (int)($profile['id_pai'] ?? 0) : null,
            'id_mae' => $profile ? (int)($profile['id_mae'] ?? 0) : null,
        ];

        $this->treeNodes[] = $node;
        $this->usedNodesCount++;

        if (!empty($node['nacionalidade'])) {
            $weight = pow(0.5, $generation); 
            $this->aggregate[$node['nacionalidade']] = ($this->aggregate[$node['nacionalidade']] ?? 0.0) + $weight;
            $this->totalWeight += $weight;
        }

        if (!empty($node['id_pai']) && $node['id_pai'] !== 0) $this->traverseAncestors((int)$node['id_pai'], $generation + 1);
        if (!empty($node['id_mae']) && $node['id_mae'] !== 0) $this->traverseAncestors((int)$node['id_mae'], $generation + 1);
    }

    private function fetchUserName(int $uid): ?string {
        $r = $this->dbFetch(
            "SELECT id_usuario, nome FROM usuario WHERE id_usuario = :uid LIMIT 1",
            [':uid' => $uid]
        );
        return $r ? $r['nome'] : null;
    }

    private function dbFetch(string $sql, array $params = []): ?array {
        $stmt = Database::executar($sql, $params);
        if ($stmt === false) return null;
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function dbFetchAll(string $sql, array $params = []): array {
        $stmt = Database::executar($sql, $params);
        if ($stmt === false) return [];
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
