<?php
class Sarda
{
    private ?int $id_perfil;
    private int $usuario_id_usuario;
    private ?int $id_pai;
    private ?int $id_mae;
    private string $sarda;

    public function __construct(
        ?int $id_perfil,
        int $usuario_id_usuario,
        ?int $id_pai,
        ?int $id_mae,
        string $sarda

    ) {
        $this->id_perfil = $id_perfil;
        $this->usuario_id_usuario = $usuario_id_usuario;
        $this->id_pai = $id_pai;
        $this->id_mae = $id_mae;
        $this->sarda = $sarda;
    }

    public function getIdPerfil(): ?int { return $this->id_perfil; }
    public function getUsuarioId(): int { return $this->usuario_id_usuario; }
    public function getIdPai(): ?int { return $this->id_pai; }
    public function getIdMae(): ?int { return $this->id_mae; }
    public function getSarda(): string { return $this->sarda; } }

    public function setIdPerfil(?int $v): void { $this->id_perfil = $v; }
    public function setUsuarioId(int $v): void { $this->usuario_id_usuario = $v; }
    public function setIdPai(?int $v): void { $this->id_pai = $v; }
    public function setIdMae(?int $v): void { $this->id_mae = $v; }
    public function setSarda(string $v): void { $this->sarda = $v; }
