<?php
namespace App\Covinhas\Model;

class Covinha
{
    public int $id_perfil;
    public int $usuario_idusuario;
    public ?int $id_pai = null;
    public ?int $id_mae = null;
    public ?int $cov_queixo = null;
    public ?int $cov_bochecha = null;

    public function __construct(array $data = [])
    {
        $this->id_perfil        = $data['id_perfil'] ?? 0;
        $this->usuario_idusuario= $data['usuario_idusuario'] ?? 0;
        $this->id_pai           = $data['id_pai'] ?? null;
        $this->id_mae           = $data['id_mae'] ?? null;
        $this->cov_queixo       = $data['cov_queixo'] ?? null;
        $this->cov_bochecha     = $data['cov_bochecha'] ?? null;
    }
}
